<?php

namespace App\Services;

use App\Models\Ebook;
use App\Models\EbookCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ProductEbookImporter
{
    public function import(array $options = []): array
    {
        $connection = DB::connection();
        $connectionName = $connection->getName();
        $prefix = (string) config('product_import.table_prefix', 'wpao_');

        $postsTable = $prefix . 'posts';
        $postMetaTable = $prefix . 'postmeta';
        $termRelationshipsTable = $prefix . 'term_relationships';
        $termTaxonomyTable = $prefix . 'term_taxonomy';
        $termsTable = $prefix . 'terms';

        $schema = $connection->getSchemaBuilder();
        foreach ([$postsTable, $postMetaTable, $termRelationshipsTable, $termTaxonomyTable, $termsTable] as $table) {
            if (! $schema->hasTable($table)) {
                throw new RuntimeException("Source table [{$table}] was not found on the current database connection [{$connectionName}].");
            }
        }

        $defaults = $this->defaultMetaKeys();

        $postTypes = $this->normaliseList($options['post_types'] ?? config('product_import.ebook_post_types', ['product']));
        if (empty($postTypes)) {
            $postTypes = ['product'];
        }

        if (empty($postTypes)) {
            throw new RuntimeException('No source product post types are configured.');
        }

        $taxonomies = $this->normaliseList($options['category_taxonomies'] ?? config('product_import.category_taxonomies', ['product_cat']));
        if (empty($taxonomies)) {
            $taxonomies = ['product_cat'];
        }

        $includedCategorySlugs = $this->normaliseList($options['category_slugs'] ?? config('product_import.included_category_slugs', ['ebook', 'books']));
        $requireDownloadable = array_key_exists('require_downloadable', $options)
            ? (bool) $options['require_downloadable']
            : (bool) config('product_import.require_downloadable', true);
        $limit = isset($options['limit']) && (int) $options['limit'] > 0 ? (int) $options['limit'] : null;

        $postsQuery = $connection->table($postsTable . ' as posts')
            ->select([
                'posts.ID as id',
                'posts.post_title as title',
                'posts.post_name as slug',
                'posts.post_excerpt as excerpt',
                'posts.post_content as content',
                'posts.post_date as published_at',
                'posts.guid as source_url',
            ])
            ->whereIn('posts.post_type', $postTypes)
            ->where('posts.post_status', 'publish')
            ->orderBy('posts.ID');

        if ($limit) {
            $postsQuery->limit($limit);
        }

        $posts = $postsQuery->get();

        if ($posts->isEmpty()) {
            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'categories' => 0,
                'issues' => ['No matching source product records were found.'],
            ];
        }

        $postIds = $posts->pluck('id')->all();
        $metaKeys = collect(config('product_import.meta_keys', $defaults))
            ->flatten()
            ->merge(['_thumbnail_id', '_downloadable', '_stock_status', '_sku'])
            ->filter()
            ->unique()
            ->values()
            ->all();

        $termSlugMap = $connection->table($termsTable)
            ->select(['term_id', 'slug'])
            ->get()
            ->pluck('slug', 'term_id');

        $metaRows = $connection->table($postMetaTable)
            ->select(['post_id', 'meta_key', 'meta_value'])
            ->whereIn('post_id', $postIds)
            ->whereIn('meta_key', $metaKeys)
            ->orderBy('meta_id')
            ->get();

        $metaByPost = $metaRows
            ->groupBy('post_id')
            ->map(function (Collection $rows) {
                return $rows->groupBy('meta_key')->map(function (Collection $items) {
                    return $items->pluck('meta_value')->values()->all();
                });
            });

        $attachmentIds = $metaRows
            ->where('meta_key', '_thumbnail_id')
            ->pluck('meta_value')
            ->filter(fn ($value) => is_numeric($value))
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();

        $attachments = empty($attachmentIds)
            ? collect()
            : $connection->table($postsTable)
                ->select(['ID', 'guid'])
                ->whereIn('ID', $attachmentIds)
                ->get()
                ->pluck('guid', 'ID');

        $termsByPost = collect();
        if (! empty($taxonomies)) {
            $termsByPost = $connection->table($termRelationshipsTable . ' as relationships')
                ->join($termTaxonomyTable . ' as taxonomy', 'taxonomy.term_taxonomy_id', '=', 'relationships.term_taxonomy_id')
                ->join($termsTable . ' as terms', 'terms.term_id', '=', 'taxonomy.term_id')
                ->select([
                    'relationships.object_id as post_id',
                    'terms.term_id',
                    'terms.name',
                    'terms.slug',
                    'taxonomy.description',
                    'taxonomy.parent as parent_term_id',
                ])
                ->whereIn('relationships.object_id', $postIds)
                ->whereIn('taxonomy.taxonomy', $taxonomies)
                ->orderBy('terms.name')
                ->get()
                ->groupBy('post_id');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $categoriesCreated = 0;
        $issues = [];

        foreach ($posts as $post) {
            $title = trim((string) $post->title);
            if ($title === '') {
                $skipped++;
                $issues[] = "Source record {$post->id} skipped: missing title.";
                continue;
            }

            $meta = $metaByPost->get($post->id, collect());
            if ($requireDownloadable && strtolower((string) ($meta->get('_downloadable', [])[0] ?? '')) !== 'yes') {
                $skipped++;
                $issues[] = "Product {$post->id} skipped: not marked as downloadable.";
                continue;
            }

            $selectedTerm = $this->selectCategoryTerm(
                $termsByPost->get($post->id, collect()),
                $includedCategorySlugs,
                $termSlugMap
            );

            if (! $selectedTerm && ! empty($includedCategorySlugs)) {
                $skipped++;
                $issues[] = "Product {$post->id} skipped: outside configured product categories.";
                continue;
            }

            [$category, $categoryWasCreated] = $this->resolveCategory($selectedTerm);
            if ($categoryWasCreated) {
                $categoriesCreated++;
            }

            $ebookQuery = Ebook::query()->where('source_product_id', $post->id);
            if (! empty($post->slug)) {
                $ebookQuery->orWhere('slug', $post->slug);
            }

            $ebook = $ebookQuery->first();

            if (! $ebook) {
                $ebook = new Ebook();
            }

            $ebook->title = $title;
            $ebook->slug = $this->generateUniqueSlug($post->slug ?: $title, $ebook->id);
            $ebook->category_id = $category?->id;
            $ebook->author = $this->resolveMetaString($meta, config('product_import.meta_keys.author', $defaults['author']));
            $ebook->isbn = $this->resolveMetaString($meta, config('product_import.meta_keys.isbn', $defaults['isbn']));
            $ebook->language = $this->resolveMetaString($meta, config('product_import.meta_keys.language', $defaults['language']));
            $ebook->pages = $this->resolveMetaString($meta, config('product_import.meta_keys.pages', $defaults['pages']));
            $ebook->format = $this->resolveMetaString($meta, config('product_import.meta_keys.format', $defaults['format']));
            $ebook->price = $this->resolveNumericMeta($meta, config('product_import.meta_keys.price', $defaults['price']));
            $ebook->old_price = $this->resolveNumericMeta($meta, config('product_import.meta_keys.old_price', $defaults['old_price']));
            $ebook->external_url = $this->normaliseRemoteUrl(
                $this->resolveMetaUrl($meta, config('product_import.meta_keys.external_url', $defaults['external_url']), $attachments)
            );
            $ebook->download_url = $this->normaliseRemoteUrl(
                $this->resolveMetaUrl($meta, config('product_import.meta_keys.download_url', $defaults['download_url']), $attachments)
            );
            $ebook->excerpt = $this->firstNonEmpty([
                $post->excerpt,
                $this->resolveMetaString($meta, config('product_import.meta_keys.excerpt', $defaults['excerpt'])),
            ]);
            $ebook->description = $post->content ?: $ebook->description;
            $ebook->cover_image = $this->normaliseRemoteUrl($this->resolveCoverImage($post, $meta, $attachments)) ?: $ebook->cover_image;
            $ebook->source_product_id = (int) $post->id;
            $ebook->source_url = $this->normaliseRemoteUrl(
                html_entity_decode((string) ($post->source_url ?: $ebook->source_url), ENT_QUOTES | ENT_HTML5, 'UTF-8')
            );
            $ebook->meta_title = $ebook->meta_title ?: $title;
            $ebook->meta_description = $ebook->meta_description ?: Str::limit(strip_tags($ebook->excerpt ?: $post->content ?: ''), 155, '');
            $ebook->meta_image = $ebook->meta_image ?: $ebook->cover_image;
            $ebook->published_at = $post->published_at ?: $ebook->published_at;
            $ebook->status = 1;

            $isNew = ! $ebook->exists;
            $ebook->save();

            if ($isNew) {
                $created++;
            } else {
                $updated++;
            }
        }

        Cache::forget('ebook_menu_categories');

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'categories' => $categoriesCreated,
            'issues' => $issues,
        ];
    }

    private function selectCategoryTerm(Collection $terms, array $includedSlugs, Collection $termSlugMap): ?object
    {
        if ($terms->isEmpty()) {
            return null;
        }

        $terms = $terms->sortByDesc(function ($term) {
            return (int) ! empty($term->parent_term_id);
        })->values();

        if (empty($includedSlugs)) {
            return $terms->first();
        }

        return $terms->first(function ($term) use ($includedSlugs, $termSlugMap) {
            $slug = Str::slug((string) ($term->slug ?: $term->name));
            $parentSlug = $term->parent_term_id
                ? Str::slug((string) ($termSlugMap[$term->parent_term_id] ?? ''))
                : null;

            return in_array($slug, $includedSlugs, true)
                || ($parentSlug && in_array($parentSlug, $includedSlugs, true));
        });
    }

    private function resolveCategory(?object $term): array
    {
        if (! $term) {
            return [null, false];
        }

        $slug = Str::slug($term->slug ?: $term->name);
        $category = EbookCategory::where('slug', $slug)->first();
        $wasCreated = false;

        if (! $category) {
            $category = new EbookCategory();
            $category->slug = $slug ?: Str::slug($term->name);
            $wasCreated = true;
        }

        $category->name = $term->name;
        $category->description = $term->description ?: $category->description;
        $category->status = true;
        $category->save();

        return [$category, $wasCreated];
    }

    private function resolveMetaString(Collection $meta, array $keys): ?string
    {
        foreach ($this->normaliseList($keys) as $key) {
            foreach ((array) $meta->get($key, []) as $value) {
                $resolved = $this->extractScalarValue($value);
                if (is_string($resolved)) {
                    $resolved = trim(strip_tags($resolved));
                    if ($resolved !== '') {
                        return $resolved;
                    }
                }
            }
        }

        return null;
    }

    private function resolveNumericMeta(Collection $meta, array $keys): ?string
    {
        foreach ($this->normaliseList($keys) as $key) {
            foreach ((array) $meta->get($key, []) as $value) {
                $resolved = $this->extractScalarValue($value);
                if ($resolved === null) {
                    continue;
                }

                $resolved = preg_replace('/[^0-9.]+/', '', (string) $resolved);
                if ($resolved !== '' && is_numeric($resolved)) {
                    return $resolved;
                }
            }
        }

        return null;
    }

    private function resolveMetaUrl(Collection $meta, array $keys, Collection $attachments): ?string
    {
        foreach ($this->normaliseList($keys) as $key) {
            foreach ((array) $meta->get($key, []) as $value) {
                $resolved = $this->extractUrl($value, $attachments);
                if ($resolved) {
                    return $resolved;
                }
            }
        }

        return null;
    }

    private function resolveCoverImage(object $post, Collection $meta, Collection $attachments): ?string
    {
        $thumbnailId = $meta->get('_thumbnail_id', [])[0] ?? null;
        if ($thumbnailId && isset($attachments[(int) $thumbnailId])) {
            return $attachments[(int) $thumbnailId];
        }

        $metaImage = $this->resolveMetaUrl($meta, config('product_import.meta_keys.cover_image', $this->defaultMetaKeys()['cover_image']), $attachments);
        if ($metaImage) {
            return $metaImage;
        }

        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', (string) $post->content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function extractUrl($value, Collection $attachments): ?string
    {
        $decoded = $this->decodeValue($value);

        if (is_numeric($decoded) && isset($attachments[(int) $decoded])) {
            return $attachments[(int) $decoded];
        }

        if (is_string($decoded)) {
            $decoded = trim($decoded);
            if (filter_var($decoded, FILTER_VALIDATE_URL)) {
                return $decoded;
            }
        }

        if (is_array($decoded)) {
            foreach ($decoded as $item) {
                $url = $this->extractUrl($item, $attachments);
                if ($url) {
                    return $url;
                }
            }
        }

        return null;
    }

    private function extractScalarValue($value): mixed
    {
        $decoded = $this->decodeValue($value);

        if (is_array($decoded)) {
            foreach ($decoded as $item) {
                $resolved = $this->extractScalarValue($item);
                if ($resolved !== null && $resolved !== '') {
                    return $resolved;
                }
            }

            return null;
        }

        return $decoded;
    }

    private function decodeValue($value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        $json = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        if ($this->looksSerialized($trimmed)) {
            $unserialized = @unserialize($trimmed);
            if ($unserialized !== false || $trimmed === 'b:0;') {
                return $unserialized;
            }
        }

        return $trimmed;
    }

    private function looksSerialized(string $value): bool
    {
        return preg_match('/^(a|O|s|i|d|b):/', $value) === 1;
    }

    private function firstNonEmpty(array $values): ?string
    {
        foreach ($values as $value) {
            $value = is_string($value) ? trim($value) : $value;
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function normaliseList($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $value))));
    }

    private function generateUniqueSlug(?string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value ?? '') ?: 'ebook';
        $slug = $base;
        $counter = 1;

        while (
            Ebook::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function normaliseRemoteUrl(?string $url): ?string
    {
        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        $host = (string) parse_url($url, PHP_URL_HOST);

        if (
            str_starts_with($url, 'http://')
            && $host !== ''
            && (
                Str::endsWith($host, '.thehorizonsunlimited.com')
                || $host === 'thehorizonsunlimited.com'
            )
        ) {
            return 'https://' . substr($url, 7);
        }

        return $url;
    }

    private function defaultMetaKeys(): array
    {
        return [
            'author' => ['author', 'book_author', '_book_author', '_author'],
            'isbn' => ['isbn', '_isbn', 'book_isbn'],
            'language' => ['language', 'book_language', '_language'],
            'pages' => ['pages', 'page_count', 'book_pages', '_pages'],
            'format' => ['format', 'book_format', '_format'],
            'price' => ['_price', 'price', 'sale_price'],
            'old_price' => ['_regular_price', 'regular_price', 'old_price'],
            'download_url' => ['download_url', 'ebook_download_url', '_downloadable_files', '_file_url', 'file_url'],
            'external_url' => ['_product_url', 'external_url', 'book_url', 'purchase_url'],
            'cover_image' => ['cover_image', 'book_cover', '_thumbnail_url', 'image'],
            'excerpt' => ['excerpt', 'short_description', '_short_description'],
        ];
    }
}

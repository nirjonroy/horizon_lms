<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Ebook extends Model
{
    use HasFactory;

    public const IMAGE_FIELD_COVER = 'cover';
    public const IMAGE_FIELD_META = 'meta';
    private const REMOTE_IMAGE_CACHE_DIRECTORY = 'media/ebook-remote-cache';

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'author',
        'isbn',
        'language',
        'pages',
        'format',
        'price',
        'old_price',
        'external_url',
        'download_url',
        'excerpt',
        'description',
        'cover_image',
        'ebook_file',
        'source_product_id',
        'source_url',
        'meta_title',
        'meta_description',
        'meta_image',
        'seo_author',
        'publisher',
        'copyright',
        'site_name',
        'keywords',
        'robots',
        'published_at',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'published_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(EbookCategory::class, 'category_id');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(
            EbookCollection::class,
            'ebook_collection_ebook',
            'ebook_id',
            'ebook_collection_id'
        )->withTimestamps();
    }

    public function coverImageUrl(): string
    {
        return $this->resolveImageUrl($this->cover_image, self::IMAGE_FIELD_COVER)
            ?? asset('frontend/assets/images/books-to-go-placeholder.svg');
    }

    public function metaImageUrl(): string
    {
        return $this->resolveImageUrl($this->meta_image, self::IMAGE_FIELD_META)
            ?? $this->coverImageUrl();
    }

    public function downloadLink(): ?string
    {
        return $this->resolveAssetUrl($this->download_url)
            ?? ($this->ebook_file ? asset($this->ebook_file) : null);
    }

    public function externalLink(): ?string
    {
        return $this->resolveAssetUrl($this->external_url);
    }

    public function sourceLink(): ?string
    {
        return $this->resolveAssetUrl($this->source_url);
    }

    public function requiresPurchase(): bool
    {
        return (float) ($this->price ?? 0) > 0;
    }

    public function canBePurchased(): bool
    {
        return $this->status && $this->requiresPurchase();
    }

    public function hasPaidAccess(?int $userId): bool
    {
        return Order::hasPaidItem($userId, Order::ITEM_TYPE_EBOOK, (int) $this->id)
            || UserEbookAccess::userHasEbookAccess($userId, (int) $this->id);
    }

    public function remoteImageSource(string $field): ?string
    {
        $value = match ($field) {
            self::IMAGE_FIELD_COVER => $this->cover_image,
            self::IMAGE_FIELD_META => $this->meta_image,
            default => null,
        };

        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $this->shouldProxyRemoteUrl($value) ? $value : null;
    }

    public function cachedRemoteImageRelativePath(string $field): ?string
    {
        $sourceUrl = $this->remoteImageSource($field);
        if (! $sourceUrl) {
            return null;
        }

        $path = (string) parse_url($sourceUrl, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (! preg_match('/^[a-z0-9]{2,5}$/', $extension)) {
            $extension = 'jpg';
        }

        return self::REMOTE_IMAGE_CACHE_DIRECTORY . '/' . sha1($this->getKey() . '|' . $field . '|' . $sourceUrl) . '.' . $extension;
    }

    public function shouldProxyRemoteUrl(string $url): bool
    {
        $host = (string) parse_url($url, PHP_URL_HOST);

        return $host !== ''
            && (
                Str::endsWith($host, '.thehorizonsunlimited.com')
                || $host === 'thehorizonsunlimited.com'
            );
    }

    private function resolveImageUrl(?string $value, string $field): ?string
    {
        if (! $value) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $relativePath = $this->cachedRemoteImageRelativePath($field);
            if ($relativePath) {
                $fullPath = public_path($relativePath);

                if (is_file($fullPath)) {
                    return asset($relativePath);
                }

                return route('ebooks.asset', [
                    'ebook' => $this->getKey(),
                    'field' => $field,
                ]);
            }

            return $this->upgradeRemoteUrl($value);
        }

        return asset($value);
    }

    private function resolveAssetUrl(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $this->upgradeRemoteUrl($value);
        }

        return asset($value);
    }

    private function upgradeRemoteUrl(string $url): string
    {
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
}

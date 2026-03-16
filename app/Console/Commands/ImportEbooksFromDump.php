<?php

namespace App\Console\Commands;

use App\Services\ProductEbookImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

class ImportEbooksFromDump extends Command
{
    protected $signature = 'ebooks:import-dump
        {path=book-to-go.sql : Path to the SQL dump file}
        {--all-products : Import all downloadable products regardless of category slug}
        {--category-slugs= : Override the configured product category slugs}
        {--limit= : Limit the number of source products to import}
        {--keep-source : Keep the temporary source tables after import}';

    protected $description = 'Load source product tables from a SQL dump and import them into ebooks.';

    public function handle(ProductEbookImporter $importer): int
    {
        set_time_limit(0);

        [$dumpPath, $searchedPaths] = $this->resolveDumpPath((string) $this->argument('path'));
        if (! $dumpPath || ! File::exists($dumpPath)) {
            $this->error('SQL dump file not found.');
            $this->line('Checked these paths:');

            foreach ($searchedPaths as $path) {
                $this->line(' - ' . $path);
            }

            return self::FAILURE;
        }

        $tables = $this->sourceTables();
        $keepSource = (bool) $this->option('keep-source');
        $originalSqlMode = $this->currentSqlMode();

        $this->info("Loading source product tables from {$dumpPath}");

        try {
            $this->setImportSqlMode();
            $this->dropSourceTables($tables);
            $stats = $this->loadSourceTablesFromDump($dumpPath, $tables);
            $this->line(sprintf(
                'Loaded %d table definitions and %d insert statements.',
                $stats['create'] ?? 0,
                $stats['insert'] ?? 0
            ));

            $this->reportSourceSummary();

            $options = [
                'limit' => $this->option('limit') ?: null,
            ];

            if ($this->option('all-products')) {
                $options['category_slugs'] = '';
            } elseif (($categorySlugs = $this->option('category-slugs')) !== null) {
                $options['category_slugs'] = (string) $categorySlugs;
            }

            $result = $importer->import($options);

            $this->info(sprintf(
                'Imported ebooks: created %d, updated %d, skipped %d, categories added %d.',
                $result['created'] ?? 0,
                $result['updated'] ?? 0,
                $result['skipped'] ?? 0,
                $result['categories'] ?? 0
            ));

            foreach (array_slice($result['issues'] ?? [], 0, 10) as $issue) {
                $this->warn($issue);
            }
        } catch (Throwable $e) {
            $this->error('Import failed: ' . $e->getMessage());

            if (! $keepSource) {
                $this->dropSourceTables($tables);
            }

            $this->restoreSqlMode($originalSqlMode);

            return self::FAILURE;
        }

        if (! $keepSource) {
            $this->dropSourceTables($tables);
            $this->line('Temporary source tables removed.');
        }

        $this->restoreSqlMode($originalSqlMode);

        return self::SUCCESS;
    }

    private function resolveDumpPath(string $path): array
    {
        if ($path === '') {
            return [null, []];
        }

        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $projectRoot = base_path();
        $projectParent = dirname($projectRoot);

        $candidates = collect([
            $normalizedPath,
            base_path($normalizedPath),
            public_path($normalizedPath),
            storage_path('app' . DIRECTORY_SEPARATOR . $normalizedPath),
            storage_path('app' . DIRECTORY_SEPARATOR . 'imports' . DIRECTORY_SEPARATOR . basename($normalizedPath)),
            $projectParent . DIRECTORY_SEPARATOR . basename($normalizedPath),
        ])
            ->filter()
            ->map(fn ($candidate) => $this->normalizeAbsolutePath($candidate))
            ->unique()
            ->values();

        $found = $candidates->first(fn ($candidate) => File::exists($candidate));

        return [$found, $candidates->all()];
    }

    private function sourceTables(): array
    {
        $prefix = (string) config('product_import.table_prefix', 'wpao_');

        return [
            $prefix . 'posts',
            $prefix . 'postmeta',
            $prefix . 'terms',
            $prefix . 'term_taxonomy',
            $prefix . 'term_relationships',
        ];
    }

    private function dropSourceTables(array $tables): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            DB::statement("DROP TABLE IF EXISTS `{$table}`");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function loadSourceTablesFromDump(string $dumpPath, array $tables): array
    {
        $targets = array_fill_keys($tables, true);
        $handle = fopen($dumpPath, 'rb');

        if (! $handle) {
            throw new \RuntimeException('Unable to read the SQL dump file.');
        }

        $statement = '';
        $stats = [
            'create' => 0,
            'insert' => 0,
        ];

        try {
            while (($line = fgets($handle)) !== false) {
                $trimmed = ltrim($line);

                if ($statement === '' && $this->shouldSkipLine($trimmed)) {
                    continue;
                }

                $statement .= $line;

                if (! $this->isStatementComplete($line)) {
                    continue;
                }

                $table = $this->extractTargetTable($statement, $targets);
                if ($table) {
                    DB::unprepared($statement);

                    if (str_starts_with(ltrim($statement), 'CREATE TABLE')) {
                        $stats['create']++;
                    }

                    if (str_starts_with(ltrim($statement), 'INSERT INTO')) {
                        $stats['insert']++;
                    }
                }

                $statement = '';
            }
        } finally {
            fclose($handle);
        }

        if ($statement !== '') {
            throw new \RuntimeException('SQL dump parsing ended with an incomplete statement.');
        }

        return $stats;
    }

    private function shouldSkipLine(string $line): bool
    {
        $trimmed = trim($line);

        if ($trimmed === '') {
            return true;
        }

        return str_starts_with($trimmed, '--')
            || str_starts_with($trimmed, '/*')
            || str_starts_with($trimmed, 'LOCK TABLES')
            || str_starts_with($trimmed, 'UNLOCK TABLES')
            || str_starts_with($trimmed, 'ALTER TABLE')
            || str_starts_with($trimmed, 'DROP TABLE IF EXISTS');
    }

    private function isStatementComplete(string $line): bool
    {
        return str_ends_with(rtrim($line), ';');
    }

    private function extractTargetTable(string $statement, array $targets): ?string
    {
        if (! preg_match('/^(CREATE TABLE|INSERT INTO)\s+`([^`]+)`/i', ltrim($statement), $matches)) {
            return null;
        }

        $table = $matches[2];

        return isset($targets[$table]) ? $table : null;
    }

    private function reportSourceSummary(): void
    {
        $prefix = (string) config('product_import.table_prefix', 'wpao_');
        $postsTable = $prefix . 'posts';
        $postMetaTable = $prefix . 'postmeta';
        $termRelationshipsTable = $prefix . 'term_relationships';
        $termTaxonomyTable = $prefix . 'term_taxonomy';
        $termsTable = $prefix . 'terms';

        $publishedProducts = DB::table($postsTable)
            ->where('post_type', 'product')
            ->where('post_status', 'publish')
            ->count();

        $downloadableProducts = DB::table($postMetaTable)
            ->where('meta_key', '_downloadable')
            ->where('meta_value', 'yes')
            ->distinct()
            ->count('post_id');

        $this->line("Source products: {$publishedProducts} published, {$downloadableProducts} marked downloadable.");

        $categories = DB::table($postsTable . ' as posts')
            ->join($termRelationshipsTable . ' as relationships', 'relationships.object_id', '=', 'posts.ID')
            ->join($termTaxonomyTable . ' as taxonomy', function ($join) {
                $join->on('taxonomy.term_taxonomy_id', '=', 'relationships.term_taxonomy_id')
                    ->where('taxonomy.taxonomy', '=', 'product_cat');
            })
            ->join($termsTable . ' as terms', 'terms.term_id', '=', 'taxonomy.term_id')
            ->where('posts.post_type', 'product')
            ->where('posts.post_status', 'publish')
            ->select('terms.name', 'terms.slug', DB::raw('COUNT(DISTINCT posts.ID) as total'))
            ->groupBy('terms.name', 'terms.slug')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        if ($categories->isNotEmpty()) {
            $this->table(
                ['Category', 'Slug', 'Products'],
                $categories->map(fn ($category) => [
                    $category->name,
                    $category->slug,
                    $category->total,
                ])->all()
            );
        }

        $sampleProducts = DB::table($postsTable)
            ->where('post_type', 'product')
            ->where('post_status', 'publish')
            ->orderBy('ID')
            ->limit(10)
            ->get(['ID', 'post_title', 'post_name']);

        if ($sampleProducts->isNotEmpty()) {
            $this->table(
                ['ID', 'Title', 'Slug'],
                $sampleProducts->map(fn ($product) => [
                    $product->ID,
                    $product->post_title,
                    $product->post_name,
                ])->all()
            );
        }
    }

    private function currentSqlMode(): string
    {
        return (string) (DB::selectOne('SELECT @@SESSION.sql_mode AS mode')->mode ?? '');
    }

    private function setImportSqlMode(): void
    {
        DB::statement("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
    }

    private function restoreSqlMode(string $mode): void
    {
        $quotedMode = str_replace("'", "\\'", $mode);

        DB::statement("SET SESSION sql_mode = '{$quotedMode}'");
    }

    private function normalizeAbsolutePath(string $path): string
    {
        $path = preg_replace('#[\\\\/]+#', DIRECTORY_SEPARATOR, $path);

        return rtrim((string) $path, DIRECTORY_SEPARATOR);
    }
}

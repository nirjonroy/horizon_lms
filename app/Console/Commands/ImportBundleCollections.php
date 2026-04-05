<?php

namespace App\Console\Commands;

use App\Services\BundleCollectionImporter;
use Illuminate\Console\Command;

class ImportBundleCollections extends Command
{
    protected $signature = 'ebook-collections:import-folder
        {path=storage/app/imports/bundle-collections : Source folder path or public Google Drive folder URL}
        {--price= : Default price for newly created bundles}
        {--old-price= : Default old price for newly created bundles}
        {--access-days= : Access window in days; blank means lifetime access}
        {--featured=0 : Mark newly created bundles as featured (1 or 0)}
        {--status=1 : Mark newly created bundles as active (1 or 0)}
        {--sort-order-start=0 : Starting sort order for new bundles}';

    protected $description = 'Import ZIP files or child folders as ebook bundle collections from a local path or public Google Drive folder.';

    public function handle(BundleCollectionImporter $importer): int
    {
        set_time_limit(0);
        $source = (string) $this->argument('path');
        $this->info('Importing bundles from: ' . $source);

        try {
            $result = $importer->importFromSource($source, [
                'price' => $this->option('price'),
                'old_price' => $this->option('old-price'),
                'access_days' => $this->option('access-days'),
                'featured' => (bool) (int) $this->option('featured'),
                'status' => (bool) (int) $this->option('status'),
                'sort_order_start' => (int) $this->option('sort-order-start'),
            ]);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Bundle import complete. Created %d, updated %d, skipped %d.',
            $result['created'] ?? 0,
            $result['updated'] ?? 0,
            $result['skipped'] ?? 0
        ));

        foreach (array_slice($result['errors'] ?? [], 0, 10) as $error) {
            $this->warn($error);
        }

        return self::SUCCESS;
    }
}

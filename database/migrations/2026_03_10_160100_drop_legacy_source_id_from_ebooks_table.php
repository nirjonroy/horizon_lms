<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ebooks') || ! Schema::hasColumn('ebooks', 'wordpress_post_id')) {
            return;
        }

        foreach ($this->legacyIndexes() as $indexName) {
            DB::statement("ALTER TABLE ebooks DROP INDEX `{$indexName}`");
        }

        DB::statement('ALTER TABLE ebooks DROP COLUMN wordpress_post_id');
    }

    public function down(): void
    {
        if (! Schema::hasTable('ebooks') || Schema::hasColumn('ebooks', 'wordpress_post_id')) {
            return;
        }

        DB::statement('ALTER TABLE ebooks ADD COLUMN wordpress_post_id BIGINT UNSIGNED NULL AFTER ebook_file');

        if (Schema::hasColumn('ebooks', 'source_product_id')) {
            DB::statement('UPDATE ebooks SET wordpress_post_id = source_product_id WHERE wordpress_post_id IS NULL AND source_product_id IS NOT NULL');
        }

        DB::statement('ALTER TABLE ebooks ADD UNIQUE INDEX ebooks_wordpress_post_id_unique (wordpress_post_id)');
    }

    private function legacyIndexes(): array
    {
        return collect(DB::select("SHOW INDEX FROM ebooks WHERE Column_name = 'wordpress_post_id'"))
            ->pluck('Key_name')
            ->filter(fn ($name) => $name !== 'PRIMARY')
            ->unique()
            ->values()
            ->all();
    }
};

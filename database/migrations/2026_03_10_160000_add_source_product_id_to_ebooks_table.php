<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ebooks')) {
            return;
        }

        if (! Schema::hasColumn('ebooks', 'source_product_id')) {
            Schema::table('ebooks', function (Blueprint $table) {
                $table->unsignedBigInteger('source_product_id')->nullable()->after('ebook_file');
            });
        }

        if (Schema::hasColumn('ebooks', 'wordpress_post_id')) {
            DB::statement('UPDATE ebooks SET source_product_id = wordpress_post_id WHERE source_product_id IS NULL AND wordpress_post_id IS NOT NULL');
        }

        if (! $this->hasUniqueIndex('ebooks', 'source_product_id')) {
            Schema::table('ebooks', function (Blueprint $table) {
                $table->unique('source_product_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('ebooks') || ! Schema::hasColumn('ebooks', 'source_product_id')) {
            return;
        }

        if ($this->hasUniqueIndex('ebooks', 'source_product_id')) {
            Schema::table('ebooks', function (Blueprint $table) {
                $table->dropUnique('ebooks_source_product_id_unique');
            });
        }

        Schema::table('ebooks', function (Blueprint $table) {
            $table->dropColumn('source_product_id');
        });
    }

    private function hasUniqueIndex(string $table, string $column): bool
    {
        return ! empty(DB::select(
            "SHOW INDEX FROM {$table} WHERE Column_name = ? AND Non_unique = 0",
            [$column]
        ));
    }
};

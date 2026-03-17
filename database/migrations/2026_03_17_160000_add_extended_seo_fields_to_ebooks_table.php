<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebooks', function (Blueprint $table) {
            $table->string('seo_author')->nullable()->after('meta_image');
            $table->string('publisher')->nullable()->after('seo_author');
            $table->string('copyright')->nullable()->after('publisher');
            $table->string('site_name')->nullable()->after('copyright');
            $table->text('keywords')->nullable()->after('site_name');
            $table->string('robots')->nullable()->after('keywords');
        });
    }

    public function down(): void
    {
        Schema::table('ebooks', function (Blueprint $table) {
            $table->dropColumn([
                'seo_author',
                'publisher',
                'copyright',
                'site_name',
                'keywords',
                'robots',
            ]);
        });
    }
};

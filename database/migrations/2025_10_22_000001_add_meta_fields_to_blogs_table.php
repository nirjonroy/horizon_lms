<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('title');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_image')->nullable()->after('meta_description');
            $table->string('author')->nullable()->after('meta_image');
            $table->string('publisher')->nullable()->after('author');
            $table->string('copyright')->nullable()->after('publisher');
            $table->string('site_name')->nullable()->after('copyright');
            $table->string('keywords')->nullable()->after('site_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_image',
                'author',
                'publisher',
                'copyright',
                'site_name',
                'keywords',
            ]);
        });
    }
};


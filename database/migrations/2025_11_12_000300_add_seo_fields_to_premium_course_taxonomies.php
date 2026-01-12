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
        $columns = function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('description');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_image')->nullable()->after('meta_description');
            $table->string('author')->nullable()->after('meta_image');
            $table->string('publisher')->nullable()->after('author');
            $table->string('copyright')->nullable()->after('publisher');
            $table->string('site_name')->nullable()->after('copyright');
            $table->text('keywords')->nullable()->after('site_name');
        };

        Schema::table('premium_course_categories', $columns);
        Schema::table('premium_course_subcategories', $columns);
        Schema::table('premium_course_child_categories', $columns);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $dropColumns = [
            'meta_title',
            'meta_description',
            'meta_image',
            'author',
            'publisher',
            'copyright',
            'site_name',
            'keywords',
        ];

        Schema::table('premium_course_categories', function (Blueprint $table) use ($dropColumns) {
            $table->dropColumn($dropColumns);
        });

        Schema::table('premium_course_subcategories', function (Blueprint $table) use ($dropColumns) {
            $table->dropColumn($dropColumns);
        });

        Schema::table('premium_course_child_categories', function (Blueprint $table) use ($dropColumns) {
            $table->dropColumn($dropColumns);
        });
    }
};

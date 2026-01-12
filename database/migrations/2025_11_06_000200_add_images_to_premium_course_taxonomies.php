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
        Schema::table('premium_course_categories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('show_on_homepage');
        });

        Schema::table('premium_course_subcategories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });

        Schema::table('premium_course_child_categories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('premium_course_categories', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('premium_course_subcategories', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('premium_course_child_categories', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};

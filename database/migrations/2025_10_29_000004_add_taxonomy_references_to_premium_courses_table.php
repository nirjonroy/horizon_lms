<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('premium_courses', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('type')
                ->constrained('premium_course_categories')
                ->nullOnDelete();

            $table->foreignId('subcategory_id')
                ->nullable()
                ->after('category_id')
                ->constrained('premium_course_subcategories')
                ->nullOnDelete();

            $table->foreignId('child_category_id')
                ->nullable()
                ->after('subcategory_id')
                ->constrained('premium_course_child_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('premium_courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('child_category_id');
            $table->dropConstrainedForeignId('subcategory_id');
            $table->dropConstrainedForeignId('category_id');
        });
    }
};

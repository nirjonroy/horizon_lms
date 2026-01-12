<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_course_child_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('premium_course_categories')
                ->cascadeOnDelete();
            $table->foreignId('subcategory_id')
                ->constrained('premium_course_subcategories')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_course_child_categories');
    }
};

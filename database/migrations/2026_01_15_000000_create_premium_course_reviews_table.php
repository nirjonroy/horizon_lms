<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_course_reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('premium_course_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('review');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->foreign('premium_course_id')
                ->references('id')
                ->on('premium_courses')
                ->cascadeOnDelete();
            $table->unique(['premium_course_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_course_reviews');
    }
};

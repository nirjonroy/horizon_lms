<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'where_to_study_id',
        'life_style_id',
        'accommodation_id',
        'cover',
        'homePage',
        'image',
        'title',
        'meta_title',
        'meta_description',
        'meta_image',
        'author',
        'publisher',
        'copyright',
        'site_name',
        'keywords',
        'slug',
        'description',
        'status',
    ];

    protected static function booted(): void
    {
        static::saving(function (Blog $blog) {
            if (! $blog->title) {
                return;
            }

            $slugWasProvided = $blog->isDirty('slug');
            $currentSlug = trim((string) $blog->slug);

            if ($currentSlug === '') {
                $slugSource = $blog->title;
            } elseif ($slugWasProvided) {
                $slugSource = $currentSlug;
            } elseif ($blog->isDirty('title')) {
                return;
            } else {
                return;
            }

            $baseSlug = Str::slug($slugSource) ?: 'blog';
            $slug = $baseSlug;
            $suffix = 1;

            while (
                static::where('slug', $slug)
                    ->when($blog->exists, fn ($query) => $query->whereKeyNot($blog->getKey()))
                    ->exists()
            ) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }

            $blog->slug = $slug;
        });
    }

    public function whereToStudy()
    {
        return $this->belongsTo(WhereToStudy::class, 'where_to_study_id');
    }

    public function internationalStudentLife()
    {
        return $this->belongsTo(InternationalStudentLife::class, 'life_style_id');
    }

    
}

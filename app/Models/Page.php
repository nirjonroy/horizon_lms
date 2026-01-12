<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Page $page) {
            if (! $page->title) {
                return;
            }

            $slugWasProvided = $page->isDirty('slug');
            $currentSlug = trim((string) $page->slug);

            if ($currentSlug === '') {
                $slugSource = $page->title;
            } elseif ($slugWasProvided) {
                $slugSource = $currentSlug;
            } elseif ($page->isDirty('title')) {
                $slugSource = $page->title;
            } else {
                return;
            }

            $baseSlug = Str::slug($slugSource) ?: 'page';
            $slug = $baseSlug;
            $suffix = 1;

            while (
                static::where('slug', $slug)
                    ->when($page->exists, fn ($query) => $query->whereKeyNot($page->getKey()))
                    ->exists()
            ) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }

            $page->slug = $slug;
        });
    }
}


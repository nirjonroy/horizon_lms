<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PremiumCourseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'show_on_homepage',
        'image',
        'meta_title',
        'meta_description',
        'meta_image',
        'author',
        'publisher',
        'copyright',
        'site_name',
        'keywords',
    ];

    public function subcategories(): HasMany
    {
        return $this->hasMany(PremiumCourseSubcategory::class, 'category_id');
    }

    public function childCategories(): HasManyThrough
    {
        return $this->hasManyThrough(
            PremiumCourseChildCategory::class,
            PremiumCourseSubcategory::class,
            'category_id',
            'subcategory_id'
        );
    }

    public function courses(): HasMany
    {
        return $this->hasMany(PremiumCourse::class, 'category_id');
    }
}

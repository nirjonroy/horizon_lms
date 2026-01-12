<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PremiumCourseSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(PremiumCourseCategory::class, 'category_id');
    }

    public function childCategories(): HasMany
    {
        return $this->hasMany(PremiumCourseChildCategory::class, 'subcategory_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(PremiumCourse::class, 'subcategory_id');
    }
}

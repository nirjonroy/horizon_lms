<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PremiumCourse extends Model
{
    protected $fillable = [
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
        'instructor',
        'duration',
        'effort',
        'questions',
        'format',
        'price',
        'old_price',
        'type',
        'category_id',
        'subcategory_id',
        'child_category_id',
        'link',
        'short_description',
        'long_description',
        'image',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PremiumCourseCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(PremiumCourseSubcategory::class, 'subcategory_id');
    }

    public function childCategory(): BelongsTo
    {
        return $this->belongsTo(PremiumCourseChildCategory::class, 'child_category_id');
    }

    public function modules()
    {
        return $this->hasMany(PremiumCourseModule::class);
    }

    public function enrollments()
    {
        return $this->hasMany(PremiumCourseEnrollment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PremiumCourseReview::class);
    }
}

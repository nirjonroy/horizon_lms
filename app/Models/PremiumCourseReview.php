<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumCourseReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'premium_course_id',
        'user_id',
        'rating',
        'review',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(PremiumCourse::class, 'premium_course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

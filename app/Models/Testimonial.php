<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'avatar',
        'rating',
        'message',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
    ];

    public function getAvatarUrlAttribute(): string
    {
        if (! $this->avatar) {
            return asset('frontend/assets/images/small-avatar-1.jpg');
        }

        return Str::startsWith($this->avatar, ['http://', 'https://'])
            ? $this->avatar
            : asset($this->avatar);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_path',
        'website_url',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getLogoUrlAttribute(): string
    {
        if (! $this->logo_path) {
            return asset('frontend/assets/images/sponsor-img.png');
        }

        return Str::startsWith($this->logo_path, ['http://', 'https://'])
            ? $this->logo_path
            : asset($this->logo_path);
    }
}

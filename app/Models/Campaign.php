<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'target_types',
        'discount_type',
        'discount_value',
        'badge_label',
        'description',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'target_types' => 'array',
        'target_categories' => 'array',
        'discount_value' => 'float',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }

    public function appliesToType(?string $type): bool
    {
        if (!$type) {
            return false;
        }

        $types = collect($this->target_types ?? []);

        return $types->contains(function ($value) use ($type) {
            return strcasecmp((string) $value, (string) $type) === 0;
        });
    }

    public function appliesToCategory(?int $categoryId): bool
    {
        if (!$categoryId) {
            return false;
        }

        $categories = collect($this->target_categories ?? []);

        return $categories->contains(function ($value) use ($categoryId) {
            return (int) $value === (int) $categoryId;
        });
    }

    public function applyDiscount(?float $basePrice): ?float
    {
        if ($basePrice === null) {
            return null;
        }

        $discounted = $basePrice;

        if ($this->discount_type === 'fixed') {
            $discounted = max(0, $basePrice - $this->discount_value);
        } else {
            $discounted = max(0, $basePrice - ($basePrice * ($this->discount_value / 100)));
        }

        return round($discounted, 2);
    }

    public function isCurrentlyActive(): bool
    {
        $now = now();

        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->lt($now)) {
            return false;
        }

        return true;
    }

    public function discountLabel(?float $basePrice = null): ?string
    {
        if ($this->discount_type === 'fixed') {
            return '-$' . number_format($this->discount_value, 2);
        }

        return '-' . rtrim(rtrim(number_format($this->discount_value, 2), '0'), '.') . '%';
    }
}

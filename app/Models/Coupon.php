<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'amount',
        'max_discount',
        'min_subtotal',
        'usage_limit',
        'per_user_limit',
        'starts_at',
        'expires_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'max_discount' => 'float',
        'min_subtotal' => 'float',
        'usage_limit' => 'integer',
        'per_user_limit' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function redemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function usageCount(): int
    {
        if (isset($this->redemptions_count)) {
            return (int) $this->redemptions_count;
        }
        return $this->redemptions()->count();
    }

    public function usageCountForUser(?int $userId): int
    {
        if (!$userId) {
            return 0;
        }

        return $this->redemptions()
            ->where('user_id', $userId)
            ->count();
    }

    public function isCurrentlyActive(): bool
    {
        return $this->stateValidationMessage() === null;
    }

    public function validateForUser(?int $userId, float $subtotal): ?string
    {
        if ($stateIssue = $this->stateValidationMessage()) {
            return $stateIssue;
        }

        if ($this->min_subtotal > 0 && $subtotal < $this->min_subtotal) {
            return 'Order total does not meet the minimum required for this coupon.';
        }

        if ($this->per_user_limit) {
            $usedByUser = $this->usageCountForUser($userId);
            if ($usedByUser >= $this->per_user_limit) {
                return 'You have already used this coupon the maximum number of times.';
            }
        }

        return null;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        $discount = 0.0;

        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->amount / 100);
        } else {
            $discount = $this->amount;
        }

        if (!is_null($this->max_discount)) {
            $discount = min($discount, (float) $this->max_discount);
        }

        return round(min($discount, $subtotal), 2);
    }

    protected function stateValidationMessage(): ?string
    {
        if (!$this->is_active) {
            return 'This coupon is not currently active.';
        }

        $now = Carbon::now();

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'This coupon is not active yet.';
        }

        if ($this->expires_at && $this->expires_at->isBefore($now)) {
            return 'This coupon has expired.';
        }

        if ($this->usage_limit && $this->usageCount() >= $this->usage_limit) {
            return 'This coupon has reached its usage limit.';
        }

        return null;
    }
}

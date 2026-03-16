<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const ITEM_TYPE_COURSE = 'course';
    public const ITEM_TYPE_EBOOK = 'ebook';
    public const ITEM_TYPE_EBOOK_PLAN = 'ebook_plan';
    public const ITEM_TYPE_EBOOK_COLLECTION = 'ebook_collection';

    protected $fillable = [
        'user_id',
        'items',
        'coupon_id',
        'coupon_code',
        'subtotal',
        'tax',
        'discount',
        'total',
        'currency',
        'status',
        'stripe_session_id',
        'stripe_payment_intent_id',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public static function hasPaidItem(?int $userId, string $type, int $id): bool
    {
        if (! $userId || $id < 1) {
            return false;
        }

        return static::query()
            ->where('user_id', $userId)
            ->paid()
            ->whereRaw(
                "JSON_CONTAINS(orders.items, JSON_OBJECT('type', ?, 'id', ?), '$')",
                [$type, $id]
            )
            ->exists();
    }

    public static function itemTypeLabel(string $type): string
    {
        return match ($type) {
            self::ITEM_TYPE_EBOOK => 'E-Book',
            self::ITEM_TYPE_EBOOK_PLAN => 'Access Plan',
            self::ITEM_TYPE_EBOOK_COLLECTION => 'Bundle Collection',
            default => 'Course',
        };
    }
}

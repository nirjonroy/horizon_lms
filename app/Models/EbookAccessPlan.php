<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EbookAccessPlan extends Model
{
    use HasFactory;

    public const SCOPE_ALL_EBOOKS = 'all_ebooks';
    public const SCOPE_COLLECTION = 'collection';

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'short_description',
        'description',
        'image',
        'access_scope',
        'ebook_collection_id',
        'billing_cycle',
        'duration_days',
        'price',
        'old_price',
        'featured',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'featured' => 'boolean',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(EbookCollection::class, 'ebook_collection_id');
    }

    public function imageUrl(): string
    {
        return $this->image ? asset($this->image) : asset('frontend/assets/images/img-loading.png');
    }

    public function canBePurchased(): bool
    {
        return $this->status && $this->price !== null;
    }

    public function durationLabel(): string
    {
        if ($this->duration_days === null) {
            return match ($this->billing_cycle) {
                'lifetime' => 'Lifetime access',
                default => ucfirst($this->billing_cycle) . ' access',
            };
        }

        if ($this->duration_days === 30) {
            return '1 month access';
        }

        if ($this->duration_days === 365) {
            return '1 year access';
        }

        return $this->duration_days . ' day access';
    }

    public function scopeLabel(): string
    {
        if ($this->access_scope === self::SCOPE_COLLECTION && $this->collection) {
            return 'Bundle access: ' . $this->collection->name;
        }

        return 'All e-books access';
    }

    public function userHasAccess(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        if ($this->access_scope === self::SCOPE_COLLECTION && $this->ebook_collection_id) {
            return UserEbookAccess::userHasCollectionAccess($userId, (int) $this->ebook_collection_id);
        }

        return UserEbookAccess::query()
            ->where('user_id', $userId)
            ->where('access_scope', self::SCOPE_ALL_EBOOKS)
            ->active()
            ->exists();
    }
}

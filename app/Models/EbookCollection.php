<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EbookCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'excerpt',
        'description',
        'cover_image',
        'price',
        'old_price',
        'access_days',
        'featured',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'access_days' => 'integer',
        'featured' => 'boolean',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function ebooks(): BelongsToMany
    {
        return $this->belongsToMany(
            Ebook::class,
            'ebook_collection_ebook',
            'ebook_collection_id',
            'ebook_id'
        )->withTimestamps();
    }

    public function accessPlans(): HasMany
    {
        return $this->hasMany(EbookAccessPlan::class);
    }

    public function accessGrants(): HasMany
    {
        return $this->hasMany(UserEbookAccess::class);
    }

    public function coverImageUrl(): string
    {
        return $this->cover_image ? asset($this->cover_image) : asset('frontend/assets/images/img-loading.png');
    }

    public function canBePurchased(): bool
    {
        return $this->status && $this->price !== null;
    }

    public function userHasAccess(?int $userId): bool
    {
        return UserEbookAccess::userHasCollectionAccess($userId, (int) $this->id);
    }

    public function accessLabel(): string
    {
        if ($this->access_days && $this->access_days > 0) {
            return $this->access_days . ' day access';
        }

        return 'Lifetime bundle access';
    }
}

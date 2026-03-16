<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEbookAccess extends Model
{
    use HasFactory;

    public const SOURCE_TYPE_PLAN = 'plan';
    public const SOURCE_TYPE_COLLECTION = 'collection';

    protected $fillable = [
        'user_id',
        'order_id',
        'source_type',
        'source_id',
        'access_scope',
        'ebook_collection_id',
        'starts_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(EbookCollection::class, 'ebook_collection_id');
    }

    public function scopeActive(Builder $query, ?CarbonInterface $at = null): Builder
    {
        $at = $at ?: now();

        return $query
            ->where('status', true)
            ->where(function (Builder $builder) use ($at) {
                $builder->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $at);
            })
            ->where(function (Builder $builder) use ($at) {
                $builder->whereNull('expires_at')
                    ->orWhere('expires_at', '>', $at);
            });
    }

    public static function userHasPlanAccess(?int $userId, int $planId): bool
    {
        if (! $userId || $planId < 1) {
            return false;
        }

        return static::query()
            ->where('user_id', $userId)
            ->where('source_type', self::SOURCE_TYPE_PLAN)
            ->where('source_id', $planId)
            ->active()
            ->exists();
    }

    public static function userHasCollectionAccess(?int $userId, int $collectionId): bool
    {
        if (! $userId || $collectionId < 1) {
            return false;
        }

        return static::query()
            ->where('user_id', $userId)
            ->active()
            ->where(function (Builder $builder) use ($collectionId) {
                $builder->where('access_scope', EbookAccessPlan::SCOPE_ALL_EBOOKS)
                    ->orWhere(function (Builder $inner) use ($collectionId) {
                        $inner->where('access_scope', EbookAccessPlan::SCOPE_COLLECTION)
                            ->where('ebook_collection_id', $collectionId);
                    });
            })
            ->exists();
    }

    public static function userHasEbookAccess(?int $userId, int $ebookId): bool
    {
        if (! $userId || $ebookId < 1) {
            return false;
        }

        return static::query()
            ->where('user_id', $userId)
            ->active()
            ->where(function (Builder $builder) use ($ebookId) {
                $builder->where('access_scope', EbookAccessPlan::SCOPE_ALL_EBOOKS)
                    ->orWhere(function (Builder $inner) use ($ebookId) {
                        $inner->where('access_scope', EbookAccessPlan::SCOPE_COLLECTION)
                            ->whereNotNull('ebook_collection_id')
                            ->whereExists(function ($subQuery) use ($ebookId) {
                                $subQuery->selectRaw('1')
                                    ->from('ebook_collection_ebook')
                                    ->whereColumn('ebook_collection_ebook.ebook_collection_id', 'user_ebook_accesses.ebook_collection_id')
                                    ->where('ebook_collection_ebook.ebook_id', $ebookId);
                            });
                    });
            })
            ->exists();
    }
}

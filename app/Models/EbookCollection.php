<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EbookCollection extends Model
{
    use HasFactory;

    private const PLACEHOLDER_TEXTS = [
        'imported from google drive.',
        'imported digital bundle.',
    ];

    protected $fillable = [
        'name',
        'slug',
        'excerpt',
        'description',
        'cover_image',
        'bundle_file',
        'download_url',
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
        return $this->resolveAssetUrl($this->cover_image)
            ?? asset('frontend/assets/images/books-to-go-placeholder.svg');
    }

    public function downloadLink(): ?string
    {
        if ($resolvedRemoteUrl = $this->resolveAssetUrl($this->download_url)) {
            return $resolvedRemoteUrl;
        }

        if ($this->bundle_file) {
            $absolutePath = public_path($this->bundle_file);
            if (is_file($absolutePath)) {
                return asset($this->bundle_file);
            }
        }

        return null;
    }

    public function hasDeliverable(): bool
    {
        return filled($this->bundle_file) || filled($this->download_url);
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

    public function summaryText(): string
    {
        $excerpt = $this->cleanText($this->excerpt);
        if ($excerpt !== null && ! $this->isPlaceholderText($excerpt)) {
            return $excerpt;
        }

        $description = $this->cleanText(strip_tags((string) $this->description));
        if ($description !== null && ! $this->isPlaceholderText($description)) {
            return Str::limit($description, 170, '');
        }

        return self::fallbackExcerptFor($this->name ?: 'Bundle Collection');
    }

    public function aboutText(): string
    {
        $description = $this->cleanText(strip_tags((string) $this->description));
        if ($description !== null && ! $this->isPlaceholderText($description)) {
            return $description;
        }

        $excerpt = $this->cleanText($this->excerpt);
        if ($excerpt !== null && ! $this->isPlaceholderText($excerpt)) {
            return $excerpt;
        }

        return self::fallbackDescriptionFor($this->name ?: 'Bundle Collection');
    }

    public function hasMeaningfulDescription(): bool
    {
        $description = $this->cleanText(strip_tags((string) $this->description));

        return $description !== null && ! $this->isPlaceholderText($description);
    }

    public static function fallbackExcerptFor(string $name): string
    {
        return trim($name) . ' is a curated digital bundle ready for instant download.';
    }

    public static function fallbackDescriptionFor(string $name): string
    {
        $displayName = trim($name) !== '' ? trim($name) : 'This bundle';
        $resourceType = self::resourceTypeFor($displayName);
        $useCase = self::useCaseFor($displayName);
        $topic = self::topicFor($displayName);

        return $displayName . ' is a curated bundle of ' . $resourceType
            . ' focused on ' . $topic . '. It is designed for ' . $useCase
            . '. Download the package to access everything in one organized collection.';
    }

    public function scopePublicCatalog(Builder $query): Builder
    {
        return $query
            ->where('status', 1)
            ->where(function (Builder $inner) {
                $inner->whereNotNull('bundle_file')
                    ->orWhereNotNull('download_url')
                    ->orWhereHas('ebooks', function ($ebookQuery) {
                        $ebookQuery->where('status', 1);
                    });
            });
    }

    private function resolveAssetUrl(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return asset($value);
    }

    private function cleanText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        return $value !== '' ? $value : null;
    }

    private function isPlaceholderText(string $value): bool
    {
        return in_array(Str::lower(trim($value)), self::PLACEHOLDER_TEXTS, true);
    }

    private static function topicFor(string $name): string
    {
        return Str::of($name)
            ->replaceMatches('/^\d+\s*/', '')
            ->replaceMatches('/\bhd\b/i', 'high-definition')
            ->replaceMatches('/\bpsd\b/i', 'PSD')
            ->replaceMatches('/\bplr\b/i', 'PLR')
            ->replaceMatches('/\bebook\b/i', 'e-book')
            ->replaceMatches('/\baudiobook\b/i', 'audiobook')
            ->replace(['_', '-'], ' ')
            ->squish()
            ->lower()
            ->toString();
    }

    private static function resourceTypeFor(string $name): string
    {
        $subject = Str::lower($name);

        return match (true) {
            Str::contains($subject, ['stock photo', 'stock photos', 'stock image', 'stock images', 'photos', 'images']) => 'visual assets',
            Str::contains($subject, ['template', 'templates']) => 'ready-to-use templates',
            Str::contains($subject, ['logo', 'logos']) => 'logo and branding assets',
            Str::contains($subject, ['ebook', 'e-book', 'audiobook', 'books']) => 'digital reading materials',
            default => 'digital resources',
        };
    }

    private static function useCaseFor(string $name): string
    {
        $subject = Str::lower($name);

        return match (true) {
            Str::contains($subject, ['stock photo', 'stock photos', 'stock image', 'stock images', 'photos', 'images']) => 'websites, blogs, presentations, social media, ads, and branded campaigns',
            Str::contains($subject, ['template', 'templates']) => 'landing pages, presentations, client work, digital products, and marketing projects',
            Str::contains($subject, ['logo', 'logos']) => 'brand kits, mockups, websites, promotions, and creative identity work',
            Str::contains($subject, ['ebook', 'e-book', 'audiobook', 'books']) => 'self-study, research, training, and on-demand digital learning',
            default => 'content creation, promotions, business use, and practical digital projects',
        };
    }
}

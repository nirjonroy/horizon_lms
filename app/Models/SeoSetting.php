<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SeoSetting extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected static $seoCache;
    private const DEFAULT_PAGE_SETTINGS = [
        'ebook-collections' => [
            'page_name' => 'ebook-collections',
            'seo_title' => '{collection_name} – Best eBook Bundle Online',
            'seo_description' => 'Buy {collection_name} PDF eBooks with instant download. High-quality digital books at affordable prices. Start learning today.',
            'author' => 'Horizons Unlimited',
            'publisher' => 'Horizons Unlimited',
            'copyright' => 'Horizons Unlimited',
            'site_name' => 'Horizons Unlimited',
            'keywords' => '["{collection_name}","ebook bundle","pdf ebooks","digital books","instant download"]',
        ],
    ];

    public static function forPage(string $slug): ?self
    {
        $normalizedSlug = Str::slug($slug);

        if (! static::$seoCache) {
            static::$seoCache = static::all();
        }

        return static::$seoCache->first(function ($item) use ($normalizedSlug, $slug) {
            return Str::slug($item->page_name ?? '') === $normalizedSlug
                || (string) $item->id === (string) $slug;
        });
    }

    public static function ensureConfiguredPages(): void
    {
        if (! static::$seoCache) {
            static::$seoCache = static::all();
        }

        foreach (self::DEFAULT_PAGE_SETTINGS as $slug => $defaults) {
            $exists = static::$seoCache->first(function ($item) use ($slug) {
                return Str::slug($item->page_name ?? '') === Str::slug($slug);
            });

            if ($exists) {
                continue;
            }

            static::create($defaults);
            static::$seoCache = null;
            static::$seoCache = static::all();
        }
    }

    public static function defaultTemplate(string $slug, string $field): ?string
    {
        return self::DEFAULT_PAGE_SETTINGS[$slug][$field] ?? null;
    }

    public static function applyTemplate(?string $template, array $replacements = []): ?string
    {
        if ($template === null) {
            return null;
        }

        $replacePairs = [];
        foreach ($replacements as $key => $value) {
            $replacePairs['{' . trim((string) $key, '{} ') . '}'] = (string) $value;
        }

        return trim(strtr($template, $replacePairs));
    }

    public static function decodeKeywords(?string $keywords, array $replacements = []): array
    {
        if (! $keywords) {
            return [];
        }

        $decoded = json_decode($keywords, true);
        if (! is_array($decoded)) {
            $decoded = array_map('trim', explode(',', $keywords));
        }

        return collect($decoded)
            ->map(fn ($keyword) => static::applyTemplate(is_string($keyword) ? $keyword : null, $replacements))
            ->filter(fn ($keyword) => filled($keyword))
            ->values()
            ->all();
    }
}

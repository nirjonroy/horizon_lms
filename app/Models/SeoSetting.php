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
}

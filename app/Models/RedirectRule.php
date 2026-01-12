<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedirectRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'sources',
        'destination_url',
        'redirect_type',
        'is_active',
        'case_insensitive',
    ];

    protected $casts = [
        'sources' => 'array',
        'is_active' => 'boolean',
        'case_insensitive' => 'boolean',
    ];

    /**
     * Normalize submitted sources into a consistent structure.
     *
     * @param  array|null  $value
     */
    public function setSourcesAttribute($value): void
    {
        $sources = collect($value)
            ->filter(fn ($item) => filled(data_get($item, 'pattern')))
            ->map(function ($item) {
                return [
                    'pattern' => trim(data_get($item, 'pattern')),
                    'match_type' => in_array(data_get($item, 'match_type'), ['exact', 'prefix', 'contains'], true)
                        ? data_get($item, 'match_type')
                        : 'exact',
                ];
            })
            ->values()
            ->all();

        $this->attributes['sources'] = json_encode($sources);
    }

    /**
     * Helper to determine redirect HTTP status code.
     */
    public function statusCode(): int
    {
        return (int) $this->redirect_type;
    }

    /**
     * Determine whether the provided path matches any configured source.
     */
    public function matches(string $path): bool
    {
        $needle = $this->case_insensitive ? mb_strtolower($path) : $path;

        foreach ($this->sources ?? [] as $source) {
            $pattern = (string) data_get($source, 'pattern');
            $matchType = data_get($source, 'match_type', 'exact');

            if ($this->case_insensitive) {
                $pattern = mb_strtolower($pattern);
            }

            switch ($matchType) {
                case 'prefix':
                    if (str_starts_with($needle, $pattern)) {
                        return true;
                    }
                    break;
                case 'contains':
                    if (str_contains($needle, $pattern)) {
                        return true;
                    }
                    break;
                default: // exact
                    if ($needle === $pattern) {
                        return true;
                    }
                    break;
            }
        }

        return false;
    }
}


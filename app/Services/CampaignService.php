<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\PremiumCourse;
use Illuminate\Support\Collection;

class CampaignService
{
    protected static ?Collection $activeCampaigns = null;

    protected static function activeCampaigns(): Collection
    {
        if (self::$activeCampaigns === null) {
            self::$activeCampaigns = Campaign::active()
                ->orderByDesc('discount_value')
                ->get();
        }

        return self::$activeCampaigns;
    }

    public static function flush(): void
    {
        self::$activeCampaigns = null;
    }

    protected static function courseValue($course, string $key)
    {
        if (is_array($course)) {
            return $course[$key] ?? null;
        }

        if (is_object($course)) {
            return $course->{$key} ?? null;
        }

        return null;
    }

    public static function campaignForCourse($course): ?Campaign
    {
        if (!$course) {
            return null;
        }

        $type = self::courseValue($course, 'type');
        $categoryId = self::courseValue($course, 'category_id');

        return self::activeCampaigns()->first(function (Campaign $campaign) use ($type, $categoryId) {
            $typeTargeted = collect($campaign->target_types ?? [])->isEmpty() || $campaign->appliesToType($type);
            $categoryTargeted = collect($campaign->target_categories ?? [])->isEmpty() || $campaign->appliesToCategory($categoryId);

            return $typeTargeted && $categoryTargeted;
        });
    }

    public static function pricingForCourse($course): object
    {
        $basePrice = null;
        $oldPrice = null;

        if ($course) {
            $priceValue = self::courseValue($course, 'price');
            $oldPriceValue = self::courseValue($course, 'old_price');
            $basePrice = is_numeric($priceValue) ? (float) $priceValue : null;
            $oldPrice = is_numeric($oldPriceValue) ? (float) $oldPriceValue : null;
        }

        $campaign = $course ? self::campaignForCourse($course) : null;

        $salePrice = $basePrice;
        if ($campaign && $basePrice !== null) {
            $salePrice = $campaign->applyDiscount($basePrice);
        }

        $hasDiscount = $campaign && $basePrice !== null && $salePrice !== null && $salePrice < $basePrice;
        $strikePrice = $oldPrice;

        if ($hasDiscount) {
            $strikePrice = $oldPrice && $oldPrice > $salePrice ? $oldPrice : $basePrice;
        }

        return (object) [
            'campaign' => $campaign,
            'has_discount' => $hasDiscount,
            'sale_price' => $salePrice,
            'base_price' => $basePrice,
            'strike_price' => $strikePrice,
            'badge' => $campaign?->badge_label ?: $campaign?->name,
            'discount_label' => $campaign?->discountLabel($basePrice),
        ];
    }
}

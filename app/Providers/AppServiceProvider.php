<?php

namespace App\Providers;

use App\Models\PremiumCourseCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('frontend.header', function ($view) {
            $categories = Cache::remember('explore_menu_categories', 3600, function () {
                return PremiumCourseCategory::with([
                    'subcategories' => function ($query) {
                        $query->orderBy('name');
                    },
                    'subcategories.childCategories' => function ($query) {
                        $query->orderBy('name');
                    },
                ])
                    ->withCount('courses')
                    ->orderBy('name')
                    ->get();
            });

            $cartItems = collect(session('cart', []));
            $headerCartCount = (int) $cartItems->reduce(function ($carry, $item) {
                return $carry + (int) ($item['quantity'] ?? 1);
            }, 0);
            $headerCartSubtotal = (float) $cartItems->reduce(function ($carry, $item) {
                $price = (float) ($item['price'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 1);
                return $carry + ($price * $quantity);
            }, 0.0);
            $headerCartOldSubtotal = (float) $cartItems->reduce(function ($carry, $item) {
                $oldPrice = $item['old_price'] ?? null;
                if ($oldPrice === null) {
                    return $carry;
                }
                $quantity = (int) ($item['quantity'] ?? 1);
                return $carry + ((float) $oldPrice * $quantity);
            }, 0.0);

            $view->with([
                'exploreMenuCategories' => $categories,
                'headerCartItems' => $cartItems->values(),
                'headerCartCount' => $headerCartCount,
                'headerCartSubtotal' => $headerCartSubtotal,
                'headerCartOldSubtotal' => $headerCartOldSubtotal,
            ]);
        });
    }
}

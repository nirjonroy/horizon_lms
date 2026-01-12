<?php

namespace App\Services;

use App\Models\Coupon;

class CartTotalsService
{
    public const DEFAULT_TAX_RATE = 0.05;

    public static function subtotal(array $cart): float
    {
        $subtotal = 0.0;

        foreach ($cart as $item) {
            $price = (float) ($item['price'] ?? 0);
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $subtotal += $price * $quantity;
        }

        return round($subtotal, 2);
    }

    public static function totals(array $cart, ?Coupon $coupon = null, float $taxRate = self::DEFAULT_TAX_RATE): array
    {
        $subtotal = self::subtotal($cart);
        $tax = round($subtotal * $taxRate, 2);
        $discount = 0.0;

        if ($coupon) {
            $discount = $coupon->calculateDiscount($subtotal);
        }

        $maxDiscount = $subtotal + $tax;
        $discount = round(min($discount, $maxDiscount), 2);

        $total = round(max(0, $subtotal + $tax - $discount), 2);

        return compact('subtotal', 'tax', 'discount', 'total');
    }
}

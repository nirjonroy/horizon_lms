<?php

namespace App\Services;

use App\Models\Coupon;

class CouponSessionManager
{
    public static function current(array $cart, ?int $userId = null, bool $persistNotice = false): ?Coupon
    {
        $sessionData = session()->get('coupon');
        if (!$sessionData) {
            return null;
        }

        $couponId = $sessionData['id'] ?? null;
        $coupon = $couponId ? Coupon::find($couponId) : null;

        if (!$coupon) {
            self::forget('Coupon no longer exists.', $persistNotice);
            return null;
        }

        $subtotal = CartTotalsService::subtotal($cart);
        $error = $coupon->validateForUser($userId, $subtotal);

        if ($error) {
            self::forget($error, $persistNotice);
            return null;
        }

        return $coupon;
    }

    public static function put(Coupon $coupon): void
    {
        session()->put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
        ]);
    }

    public static function forget(?string $message = null, bool $persistNotice = false): void
    {
        session()->forget('coupon');

        if ($message) {
            if ($persistNotice) {
                session()->flash('coupon_notice', $message);
            } else {
                session()->now('coupon_notice', $message);
            }
        }
    }
}

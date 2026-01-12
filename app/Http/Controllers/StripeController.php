<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Order;
use App\Services\CartTotalsService;
use App\Services\CouponSessionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Cart is empty');
        }

        $hadCoupon = session()->has('coupon');
        $coupon = CouponSessionManager::current($cart, auth()->id(), true);

        if ($hadCoupon && !$coupon) {
            return redirect()->route('cart.view')->with('error', session('coupon_notice') ?? 'Coupon is no longer valid.');
        }

        $totals = CartTotalsService::totals($cart, $coupon);
        if ($totals['total'] <= 0) {
            return $this->completeOrder(
                $cart,
                $coupon,
                $totals['subtotal'],
                $totals['tax'],
                $totals['discount'],
                $totals['total']
            );
        }

        $summary = [
            'subtotal' => $totals['subtotal'],
            'tax' => $totals['tax'],
            'discount' => $totals['discount'],
            'total' => $totals['total'],
            'coupon_id' => $coupon?->id,
            'coupon_code' => $coupon?->code,
        ];
        session()->put('checkout_summary', $summary);

        Stripe::setApiKey(config('services.stripe.secret'));

        $titles = collect($cart)->pluck('title')->filter()->implode(', ');
        $lineItems = [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Horizons Premium Courses',
                        'description' => Str::limit($titles ?: 'Selected premium courses', 300),
                    ],
                    'unit_amount' => (int) round($totals['total'] * 100),
                ],
                'quantity' => 1,
            ],
        ];

        $baseUrl = $request->getSchemeAndHttpHost();
        $successUrl = $baseUrl . route('stripe.success', [], false) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = $baseUrl . route('stripe.cancel', [], false);

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            session()->forget('checkout_summary');
            return redirect()->route('cart.view')->with('error', 'No items found for order');
        }

        $summary = session()->get('checkout_summary');
        $coupon = null;

        if ($summary && !empty($summary['coupon_id'])) {
            $coupon = Coupon::find($summary['coupon_id']);
        } elseif (session()->has('coupon')) {
            $coupon = CouponSessionManager::current($cart, auth()->id());
        }

        $couponCode = $summary['coupon_code'] ?? null;

        if ($summary) {
            $subtotal = $summary['subtotal'];
            $tax = $summary['tax'];
            $discount = $summary['discount'];
            $total = $summary['total'];
        } else {
            $totals = CartTotalsService::totals($cart, $coupon);
            $subtotal = $totals['subtotal'];
            $tax = $totals['tax'];
            $discount = $totals['discount'];
            $total = $totals['total'];
        }

        $status = 'paid';
        $paymentIntentId = null;

        try {
            if ($sessionId) {
                Stripe::setApiKey(config('services.stripe.secret'));
                $stripeSession = StripeSession::retrieve($sessionId);
                $status = $stripeSession->payment_status ?? 'paid';
                $paymentIntentId = $stripeSession->payment_intent ?? null;
            }
        } catch (\Throwable $e) {
            // Continue even if lookup fails
        }

        return $this->completeOrder(
            $cart,
            $coupon,
            $subtotal,
            $tax,
            $discount,
            $total,
            $status,
            $sessionId,
            $paymentIntentId,
            $couponCode
        );
    }

    /**
     * Finalize an order for both paid and zero-total carts.
     */
    private function completeOrder(
        array $cart,
        ?Coupon $coupon,
        float $subtotal,
        float $tax,
        float $discount,
        float $total,
        string $status = 'paid',
        ?string $sessionId = null,
        ?string $paymentIntentId = null,
        ?string $forcedCouponCode = null
    ) {
        if (empty($cart)) {
            session()->forget('checkout_summary');
            return redirect()->route('cart.view')->with('error', 'No items found for order');
        }

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'coupon_id' => $coupon?->id,
                'coupon_code' => $forcedCouponCode ?? $coupon?->code,
                'items' => array_values($cart),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'currency' => 'usd',
                'status' => $status,
                'stripe_session_id' => $sessionId,
                'stripe_payment_intent_id' => $paymentIntentId,
            ]);
        } catch (\Throwable $e) {
            session()->forget('checkout_summary');
            return redirect()->route('cart.view')->with('error', 'Failed to save order: ' . $e->getMessage());
        }

        if ($coupon && $discount > 0) {
            try {
                CouponRedemption::create([
                    'coupon_id' => $coupon->id,
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'discount_amount' => $discount,
                    'currency' => $order->currency,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Failed to record coupon redemption: ' . $e->getMessage());
            }
        }

        $user = auth()->user();

        if ($user) {
            try {
                \Mail::to($user->email)->send(new \App\Mail\StudentThanksMail($user, $order));
            } catch (\Exception $e) {
                \Log::error('Failed to send student mail: ' . $e->getMessage());
            }

            try {
                \Mail::to('imad@thehorizonsunlimited.com')->send(new \App\Mail\NewOrderMail($user, $order));
            } catch (\Exception $e) {
                \Log::error('Failed to send admin mail: ' . $e->getMessage());
            }
        }

        session()->forget('cart');
        session()->forget('checkout_summary');
        CouponSessionManager::forget();

        return view('frontend.payment-success');
    }

    public function cancel()
    {
        session()->forget('checkout_summary');

        return view('frontend.payment-cancel');
    }
}

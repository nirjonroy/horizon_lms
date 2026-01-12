<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\PremiumCourse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Add a course to the cart and return to the previous page.
     */
    public function addToCart(Request $request, $id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to add to cart.');
        }

        $course = PremiumCourse::where('status', 1)->findOrFail($id);
        $this->addOrUpdateCartItem($course);

        return redirect()->back()->with('success', 'Course added to cart!');
    }

    /**
     * Add to cart, then go to cart if discounted, otherwise go straight to checkout.
     */
    public function buyNow($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to continue.');
        }

        $course = PremiumCourse::where('status', 1)->findOrFail($id);
        $hasOffer = $this->courseHasOffer($course);

        // Reset quantity to 1 for a clean checkout path.
        $this->addOrUpdateCartItem($course, true);

        return $hasOffer
            ? redirect()->route('cart.view')->with('success', 'Offer applied. Review your cart.')
            : redirect()->route('checkout')->with('success', 'Ready to checkout.');
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);

        $subtotal = collect($cart)->reduce(function ($carry, $item) {
            $qty = $item['quantity'] ?? 1;
            return $carry + ((float) $item['price'] * $qty);
        }, 0.0);

        $taxRate = 0.05;
        $tax = round($subtotal * $taxRate, 2);
        $discount = 0;
        $total = round($subtotal + $tax - $discount, 2);

        $activeCoupon = null;

        return view('user.cart', compact('cart', 'subtotal', 'tax', 'discount', 'total', 'activeCoupon'));
    }

    public function removeCart($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Course removed from cart!');
    }

    public function applyCoupon(Request $request)
    {
        // Coupon system not active; keep graceful response.
        return redirect()->back()->with('error', 'Coupons are not available at this time.');
    }

    public function removeCoupon()
    {
        // No coupon to remove; keep graceful response.
        return redirect()->back()->with('success', 'No coupon applied.');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty!');
        }

        $subtotal = collect($cart)->reduce(function ($carry, $item) {
            $qty = $item['quantity'] ?? 1;
            return $carry + ((float) $item['price'] * $qty);
        }, 0.0);

        $tax = round($subtotal * 0.05, 2);
        $discount = 0;
        $total = round($subtotal + $tax - $discount, 2);

        $activeCoupon = null;

        return view('user.checkout', compact('cart', 'subtotal', 'tax', 'discount', 'total', 'activeCoupon'));
    }

    private function addOrUpdateCartItem(PremiumCourse $course, bool $resetQuantity = false): void
    {
        $cart = session()->get('cart', []);
        $id = $course->id;
        $price = (float) ($course->price ?? 0);
        $oldPrice = $course->old_price ? (float) $course->old_price : null;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $resetQuantity ? 1 : (($cart[$id]['quantity'] ?? 1) + 1);
            $cart[$id]['price'] = $price;
            $cart[$id]['old_price'] = $oldPrice;
        } else {
            $cart[$id] = [
                'id' => $id,
                'slug' => $course->slug,
                'title' => $course->title,
                'price' => $price,
                'old_price' => $oldPrice,
                'image' => $course->image,
                'instructor' => $course->instructor,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);
    }

    private function courseHasOffer(PremiumCourse $course): bool
    {
        $price = (float) ($course->price ?? 0);
        $oldPrice = $course->old_price ? (float) $course->old_price : null;

        return $oldPrice !== null && $oldPrice > $price;
    }
}

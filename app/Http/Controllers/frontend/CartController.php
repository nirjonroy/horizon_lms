<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\EbookAccessPlan;
use App\Models\EbookCollection;
use App\Models\Ebook;
use App\Models\Order;
use App\Models\PremiumCourse;
use App\Services\CartTotalsService;
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

    public function addEbookToCart(Request $request, $id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to add this e-book to your cart.');
        }

        $ebook = Ebook::where('status', 1)->findOrFail($id);

        if ($ebook->hasPaidAccess(auth()->id())) {
            return redirect()
                ->route('ebooks.download', $ebook->slug)
                ->with('success', 'You already own this e-book.');
        }

        if (! $ebook->canBePurchased()) {
            return redirect()->back()->with('error', 'This e-book is not currently available for purchase.');
        }

        $this->addOrUpdateEbookItem($ebook);

        return redirect()->back()->with('success', 'E-book added to cart!');
    }

    public function addEbookPlanToCart(Request $request, $id)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to add this access plan to your cart.');
        }

        $plan = EbookAccessPlan::with('collection')->where('status', 1)->findOrFail($id);

        if ($plan->userHasAccess(auth()->id())) {
            return redirect()->back()->with('success', 'Your account already has this access plan.');
        }

        if (! $plan->canBePurchased()) {
            return redirect()->back()->with('error', 'This access plan is not currently available.');
        }

        $this->addOrUpdateEbookPlanItem($plan);

        return redirect()->back()->with('success', 'Access plan added to cart!');
    }

    public function addEbookCollectionToCart(Request $request, $id)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to add this collection to your cart.');
        }

        $collection = EbookCollection::where('status', 1)->findOrFail($id);

        if ($collection->userHasAccess(auth()->id())) {
            return redirect()->back()->with('success', 'You already have access to this collection.');
        }

        if (! $collection->canBePurchased()) {
            return redirect()->back()->with('error', 'This collection is not currently available.');
        }

        $this->addOrUpdateEbookCollectionItem($collection);

        return redirect()->back()->with('success', 'Collection added to cart!');
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

    public function buyEbookNow($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to continue.');
        }

        $ebook = Ebook::where('status', 1)->findOrFail($id);

        if ($ebook->hasPaidAccess(auth()->id())) {
            return redirect()
                ->route('ebooks.download', $ebook->slug)
                ->with('success', 'You already own this e-book.');
        }

        if (! $ebook->canBePurchased()) {
            return redirect()->back()->with('error', 'This e-book is not currently available for purchase.');
        }

        $hasOffer = $this->ebookHasOffer($ebook);
        $this->addOrUpdateEbookItem($ebook, true);

        return $hasOffer
            ? redirect()->route('cart.view')->with('success', 'Offer applied. Review your cart.')
            : redirect()->route('checkout')->with('success', 'Ready to checkout.');
    }

    public function buyEbookPlanNow($id)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to continue.');
        }

        $plan = EbookAccessPlan::with('collection')->where('status', 1)->findOrFail($id);

        if ($plan->userHasAccess(auth()->id())) {
            return redirect()->back()->with('success', 'Your account already has this access plan.');
        }

        if (! $plan->canBePurchased()) {
            return redirect()->back()->with('error', 'This access plan is not currently available.');
        }

        $hasOffer = $this->accessPlanHasOffer($plan);
        $this->addOrUpdateEbookPlanItem($plan, true);

        return $hasOffer
            ? redirect()->route('cart.view')->with('success', 'Offer applied. Review your cart.')
            : redirect()->route('checkout')->with('success', 'Ready to checkout.');
    }

    public function buyEbookCollectionNow($id)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to continue.');
        }

        $collection = EbookCollection::where('status', 1)->findOrFail($id);

        if ($collection->userHasAccess(auth()->id())) {
            return redirect()->back()->with('success', 'You already have access to this collection.');
        }

        if (! $collection->canBePurchased()) {
            return redirect()->back()->with('error', 'This collection is not currently available.');
        }

        $hasOffer = $this->ebookCollectionHasOffer($collection);
        $this->addOrUpdateEbookCollectionItem($collection, true);

        return $hasOffer
            ? redirect()->route('cart.view')->with('success', 'Offer applied. Review your cart.')
            : redirect()->route('checkout')->with('success', 'Ready to checkout.');
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);
        $totals = CartTotalsService::totals($cart);

        $activeCoupon = null;
        $subtotal = $totals['subtotal'];
        $tax = $totals['tax'];
        $discount = $totals['discount'];
        $total = $totals['total'];

        return view('user.cart', compact('cart', 'subtotal', 'tax', 'discount', 'total', 'activeCoupon'));
    }

    public function removeCart($key)
    {
        $cart = session()->get('cart', []);

        $resolvedKey = $this->resolveCartKey($cart, (string) $key);
        if ($resolvedKey !== null) {
            unset($cart[$resolvedKey]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Item removed from cart!');
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
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to continue to checkout.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty!');
        }

        $totals = CartTotalsService::totals($cart);

        $activeCoupon = null;
        $subtotal = $totals['subtotal'];
        $tax = $totals['tax'];
        $discount = $totals['discount'];
        $total = $totals['total'];

        return view('user.checkout', compact('cart', 'subtotal', 'tax', 'discount', 'total', 'activeCoupon'));
    }

    private function addOrUpdateCartItem(PremiumCourse $course, bool $resetQuantity = false): void
    {
        $cart = session()->get('cart', []);
        $key = $this->typedCartKey(Order::ITEM_TYPE_COURSE, (int) $course->id);
        $existingKey = $this->findCartKey($cart, (int) $course->id, Order::ITEM_TYPE_COURSE);
        $price = (float) ($course->price ?? 0);
        $oldPrice = $course->old_price ? (float) $course->old_price : null;
        $existingItem = $existingKey !== null ? ($cart[$existingKey] ?? []) : [];
        $quantity = $resetQuantity ? 1 : (($existingItem['quantity'] ?? 0) + 1);

        if ($existingKey !== null && $existingKey !== $key) {
            unset($cart[$existingKey]);
        }

        $cart[$key] = array_merge(
            $existingItem,
            $this->makeCourseCartItem($course, $key),
            [
                'price' => $price,
                'old_price' => $oldPrice,
                'quantity' => max(1, $quantity),
            ]
        );

        session()->put('cart', $cart);
    }

    private function addOrUpdateEbookItem(Ebook $ebook, bool $resetQuantity = false): void
    {
        $cart = session()->get('cart', []);
        $key = $this->typedCartKey(Order::ITEM_TYPE_EBOOK, (int) $ebook->id);
        $existingKey = $this->findCartKey($cart, (int) $ebook->id, Order::ITEM_TYPE_EBOOK);
        $existingItem = $existingKey !== null ? ($cart[$existingKey] ?? []) : [];

        if ($existingKey !== null && $existingKey !== $key) {
            unset($cart[$existingKey]);
        }

        $cart[$key] = array_merge(
            $existingItem,
            $this->makeEbookCartItem($ebook, $key),
            [
                'quantity' => $resetQuantity ? 1 : max(1, (int) ($existingItem['quantity'] ?? 1)),
            ]
        );

        session()->put('cart', $cart);
    }

    private function addOrUpdateEbookPlanItem(EbookAccessPlan $plan, bool $resetQuantity = false): void
    {
        $cart = session()->get('cart', []);
        $key = $this->typedCartKey(Order::ITEM_TYPE_EBOOK_PLAN, (int) $plan->id);
        $existingKey = $this->findCartKey($cart, (int) $plan->id, Order::ITEM_TYPE_EBOOK_PLAN);
        $existingItem = $existingKey !== null ? ($cart[$existingKey] ?? []) : [];

        if ($existingKey !== null && $existingKey !== $key) {
            unset($cart[$existingKey]);
        }

        $cart[$key] = array_merge(
            $existingItem,
            $this->makeEbookPlanCartItem($plan, $key),
            [
                'quantity' => $resetQuantity ? 1 : max(1, (int) ($existingItem['quantity'] ?? 1)),
            ]
        );

        session()->put('cart', $cart);
    }

    private function addOrUpdateEbookCollectionItem(EbookCollection $collection, bool $resetQuantity = false): void
    {
        $cart = session()->get('cart', []);
        $key = $this->typedCartKey(Order::ITEM_TYPE_EBOOK_COLLECTION, (int) $collection->id);
        $existingKey = $this->findCartKey($cart, (int) $collection->id, Order::ITEM_TYPE_EBOOK_COLLECTION);
        $existingItem = $existingKey !== null ? ($cart[$existingKey] ?? []) : [];

        if ($existingKey !== null && $existingKey !== $key) {
            unset($cart[$existingKey]);
        }

        $cart[$key] = array_merge(
            $existingItem,
            $this->makeEbookCollectionCartItem($collection, $key),
            [
                'quantity' => $resetQuantity ? 1 : max(1, (int) ($existingItem['quantity'] ?? 1)),
            ]
        );

        session()->put('cart', $cart);
    }

    private function courseHasOffer(PremiumCourse $course): bool
    {
        $price = (float) ($course->price ?? 0);
        $oldPrice = $course->old_price ? (float) $course->old_price : null;

        return $oldPrice !== null && $oldPrice > $price;
    }

    private function ebookHasOffer(Ebook $ebook): bool
    {
        $price = (float) ($ebook->price ?? 0);
        $oldPrice = $ebook->old_price ? (float) $ebook->old_price : null;

        return $oldPrice !== null && $oldPrice > $price;
    }

    private function accessPlanHasOffer(EbookAccessPlan $plan): bool
    {
        $price = (float) ($plan->price ?? 0);
        $oldPrice = $plan->old_price ? (float) $plan->old_price : null;

        return $oldPrice !== null && $oldPrice > $price;
    }

    private function ebookCollectionHasOffer(EbookCollection $collection): bool
    {
        $price = (float) ($collection->price ?? 0);
        $oldPrice = $collection->old_price ? (float) $collection->old_price : null;

        return $oldPrice !== null && $oldPrice > $price;
    }

    private function makeCourseCartItem(PremiumCourse $course, string $key): array
    {
        return [
            'id' => (int) $course->id,
            'key' => $key,
            'type' => Order::ITEM_TYPE_COURSE,
            'type_label' => 'Course',
            'slug' => $course->slug,
            'url' => route('course.show', $course->slug),
            'title' => $course->title,
            'price' => (float) ($course->price ?? 0),
            'old_price' => $course->old_price ? (float) $course->old_price : null,
            'image' => $course->image,
            'subtitle' => $course->instructor ?: 'Horizons Faculty',
            'instructor' => $course->instructor,
        ];
    }

    private function makeEbookCartItem(Ebook $ebook, string $key): array
    {
        return [
            'id' => (int) $ebook->id,
            'key' => $key,
            'type' => Order::ITEM_TYPE_EBOOK,
            'type_label' => 'E-Book',
            'slug' => $ebook->slug,
            'url' => route('ebooks.show', $ebook->slug),
            'title' => $ebook->title,
            'price' => (float) ($ebook->price ?? 0),
            'old_price' => $ebook->old_price ? (float) $ebook->old_price : null,
            'image' => $ebook->coverImageUrl(),
            'subtitle' => $ebook->author ?: 'Unknown author',
            'author' => $ebook->author,
        ];
    }

    private function makeEbookPlanCartItem(EbookAccessPlan $plan, string $key): array
    {
        return [
            'id' => (int) $plan->id,
            'key' => $key,
            'type' => Order::ITEM_TYPE_EBOOK_PLAN,
            'type_label' => Order::itemTypeLabel(Order::ITEM_TYPE_EBOOK_PLAN),
            'slug' => $plan->slug,
            'url' => route('ebook-plans.show', $plan->slug),
            'title' => $plan->name,
            'price' => (float) ($plan->price ?? 0),
            'old_price' => $plan->old_price ? (float) $plan->old_price : null,
            'image' => $plan->image,
            'subtitle' => $plan->scopeLabel(),
            'duration_label' => $plan->durationLabel(),
        ];
    }

    private function makeEbookCollectionCartItem(EbookCollection $collection, string $key): array
    {
        return [
            'id' => (int) $collection->id,
            'key' => $key,
            'type' => Order::ITEM_TYPE_EBOOK_COLLECTION,
            'type_label' => Order::itemTypeLabel(Order::ITEM_TYPE_EBOOK_COLLECTION),
            'slug' => $collection->slug,
            'url' => route('ebook-collections.show', $collection->slug),
            'title' => $collection->name,
            'price' => (float) ($collection->price ?? 0),
            'old_price' => $collection->old_price ? (float) $collection->old_price : null,
            'image' => $collection->coverImageUrl(),
            'subtitle' => $collection->accessLabel(),
            'ebook_count' => $collection->ebooks()->count(),
        ];
    }

    private function findCartKey(array $cart, int $id, string $type): ?string
    {
        $preferredKey = $this->typedCartKey($type, $id);

        if (array_key_exists($preferredKey, $cart)) {
            return $preferredKey;
        }

        foreach ($cart as $existingKey => $item) {
            $itemId = (int) ($item['id'] ?? 0);
            $itemType = (string) ($item['type'] ?? Order::ITEM_TYPE_COURSE);

            if ($itemId === $id && $itemType === $type) {
                return (string) $existingKey;
            }
        }

        return null;
    }

    private function resolveCartKey(array $cart, string $key): ?string
    {
        if (array_key_exists($key, $cart)) {
            return $key;
        }

        foreach ($cart as $existingKey => $item) {
            if ((string) ($item['key'] ?? '') === $key) {
                return (string) $existingKey;
            }

            if ((string) ($item['id'] ?? '') === $key && ! str_contains($key, ':')) {
                return (string) $existingKey;
            }
        }

        return null;
    }

    private function typedCartKey(string $type, int $id): string
    {
        return $type . ':' . $id;
    }
}

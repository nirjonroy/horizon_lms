@extends('frontend.app')

@section('content')
@php
    $cartItems = collect($cart ?? []);
@endphp

<section class="py-5" style="background:#f4f6fb;">
    <div class="container">
        @if($cartItems->isEmpty())
            <div class="alert alert-light border text-center">
                <h4 class="mb-2">Your cart is empty</h4>
                <p class="mb-3">Add a course first, then come back to complete your payment.</p>
                <a href="{{ route('premium-courses') }}" class="btn theme-btn">
                    Browse Courses <i class="la la-arrow-right icon ms-1"></i>
                </a>
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-body p-4 p-md-5">
                            <h2 class="fw-bold text-primary mb-3">Checkout</h2>
                            <h5 class="text-muted mb-4">Order Summary</h5>

                            @if (session('coupon_notice'))
                                <div class="alert alert-warning">
                                    {{ session('coupon_notice') }}
                                </div>
                            @endif

                            @if (!empty($activeCoupon))
                                <div class="alert alert-success d-flex flex-wrap align-items-center justify-content-between">
                                    <div class="me-3">
                                        <div class="text-uppercase fw-semibold">Coupon Applied: {{ $activeCoupon->code }}</div>
                                        <small class="text-muted">Saving ${{ number_format($discount, 2) }} on this purchase.</small>
                                    </div>
                                    <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-danger p-0">Remove</button>
                                    </form>
                                </div>
                            @endif

                            <ul class="list-unstyled mb-4">
                                @foreach($cartItems as $item)
                                    @php
                                        $quantity = max(1, (int) ($item['quantity'] ?? 1));
                                        $lineTotal = (float) $item['price'] * $quantity;
                                    @endphp
                                    <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                        <div>
                                            <p class="mb-1 fw-semibold text-dark">{{ $item['title'] }}</p>
                                            <small class="text-muted">× {{ $quantity }}</small>
                                        </div>
                                        <span class="fw-semibold">${{ number_format($lineTotal, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="pt-2">
                                <div class="d-flex justify-content-between text-muted mb-2">
                                    <span>Subtotal</span>
                                    <span>${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted mb-2">
                                    <span>Tax</span>
                                    <span>${{ number_format($tax, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted mb-2">
                                    <span>
                                        Discount
                                        @if(!empty($activeCoupon))
                                            <span class="badge badge-success text-uppercase ms-2">{{ $activeCoupon->code }}</span>
                                        @endif
                                    </span>
                                    <span>- ${{ number_format($discount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold text-uppercase text-muted">Total</span>
                                    <span class="fw-bold fs-3 text-primary">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('stripe.checkout') }}" class="mt-4">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100" style="border-radius:18px;font-weight:600;">
                                    Pay with Stripe ( ${{ number_format($total, 2) }} )
                                </button>
                            </form>
                            <p class="text-muted text-center small mt-3 mb-0">
                                Secure payments powered by Stripe. You will be redirected to confirm your card details.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

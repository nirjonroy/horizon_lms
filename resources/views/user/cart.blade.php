@extends('frontend.app')

@section('content')
    <section class="breadcrumb-area bg-white py-4">
        <div class="container">
            <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
                <div>
                    <h2 class="section__title mb-1">{{ __('Shopping Cart') }}</h2>
                    <ul class="generic-list-item d-flex align-items-center fs-14 text-gray">
                        <li><a href="{{ route('home.index') }}">{{ __('Home') }}</a></li>
                        <li class="px-2">/</li>
                        <li class="text-black">{{ __('Shopping Cart') }}</li>
                    </ul>
                </div>
                <p class="fs-14 text-gray mb-0">
                    {{ __('Review your selected premium courses and proceed to checkout confidently.') }}
                </p>
            </div>
        </div>
    </section>

    <section class="cart-area section-padding">
        <div class="container">
            @if (!count($cart))
                <div class="bg-gray p-5 rounded-rounded text-center">
                    <h3 class="fs-24 font-weight-bold mb-2">{{ __('Your cart is empty') }}</h3>
                    <p class="text-gray mb-4">{{ __('Browse our premium catalog and add courses to unlock your next milestone.') }}</p>
                    <a href="{{ route('premium-courses') }}" class="btn theme-btn">
                        {{ __('Browse courses') }} <i class="la la-arrow-right icon ms-1"></i>
                    </a>
                </div>
            @else
                <div class="row">
                    <div class="col-lg-8">
                        <div class="table-responsive">
                            <table class="table generic-table">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Image') }}</th>
                                        <th scope="col">{{ __('Product Details') }}</th>
                                        <th scope="col">{{ __('Price') }}</th>
                                        <th scope="col">{{ __('Quantity') }}</th>
                                        <th scope="col">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cart as $id => $item)
                                        @php
                                            $image = $item['image'] ?? null;
                                            if ($image && !\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
                                                $image = asset($image);
                                            }
                                            $image = $image ?: asset('frontend/assets/images/img-loading.png');
                                            $slug = $item['slug'] ?? null;
                                            $courseUrl = $slug ? route('course.show', $slug) : '#';
                                        @endphp
                                        <tr>
                                            <th scope="row">
                                                <div class="media media-card">
                                                    <a href="{{ $courseUrl }}" class="media-img me-0">
                                                        <img src="{{ $image }}" alt="{{ $item['title'] ?? 'Cart image' }}" />
                                                    </a>
                                                </div>
                                            </th>
                                            <td>
                                                <a href="{{ $courseUrl }}" class="text-black font-weight-semi-bold d-block mb-1">
                                                    {{ $item['title'] ?? __('Untitled course') }}
                                                </a>
                                                <p class="fs-14 text-gray lh-20 mb-0">
                                                    {{ __('By') }}
                                                    <span class="text-color">{{ $item['instructor'] ?? __('Horizons Faculty') }}</span>
                                                </p>
                                            </td>
                                            <td>
                                                <ul class="generic-list-item font-weight-semi-bold mb-0">
                                                    <li class="text-black lh-18">
                                                        ${{ number_format((float) ($item['price'] ?? 0), 2) }}
                                                    </li>
                                                    @if (!empty($item['old_price']))
                                                        <li class="before-price lh-18">
                                                            ${{ number_format((float) $item['old_price'], 2) }}
                                                        </li>
                                                    @endif
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="quantity-item d-inline-flex align-items-center">
                                                    <input
                                                        class="qtyInput"
                                                        type="text"
                                                        value="{{ $item['quantity'] ?? 1 }}"
                                                        readonly
                                                    />
                                                </div>
                                            </td>
                                            <td>
                                                <a
                                                    href="{{ route('cart.remove', $id) }}"
                                                    class="icon-element icon-element-xs shadow-sm border-0"
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="{{ __('Remove') }}"
                                                >
                                                    <i class="la la-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-4">
                            <div class="mb-2 w-100 w-md-auto">
                                @if ($activeCoupon)
                                    <div class="alert alert-success d-flex flex-wrap align-items-center justify-content-between mb-2">
                                        <div class="me-3">
                                            <div class="text-uppercase font-weight-bold">{{ __('Coupon Applied') }}: {{ $activeCoupon->code }}</div>
                                            <small class="text-muted d-block">
                                                {{ __('You are saving :amount on this order.', ['amount' => '$' . number_format($discount, 2)]) }}
                                            </small>
                                        </div>
                                        <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-link text-danger p-0">{{ __('Remove') }}</button>
                                        </form>
                                    </div>
                                @else
                                    <form class="mb-2 w-100 w-md-auto" action="{{ route('cart.coupon.apply') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input
                                                class="form-control form--control ps-3"
                                                type="text"
                                                name="code"
                                                value="{{ old('code') }}"
                                                placeholder="{{ __('Enter coupon code') }}"
                                                autocomplete="off"
                                            />
                                            <div class="input-group-append">
                                                <button class="btn theme-btn" type="submit">{{ __('Apply Code') }}</button>
                                            </div>
                                        </div>
                                        @error('code')
                                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                                        @enderror
                                    </form>
                                @endif
                                @if (session('coupon_notice'))
                                    <small class="text-danger d-block mt-2">{{ session('coupon_notice') }}</small>
                                @endif
                            </div>
                            <span class="text-gray fs-14 mb-2">
                                {{ __('Need changes? Remove items or continue shopping anytime.') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 ms-auto">
                        <div class="bg-gray p-4 rounded-rounded mt-40px">
                            <h3 class="fs-18 font-weight-bold pb-3">{{ __('Cart Totals') }}</h3>
                            <div class="divider"><span></span></div>
                            <ul class="generic-list-item pb-4 mb-0">
                                <li class="d-flex align-items-center justify-content-between font-weight-semi-bold">
                                    <span class="text-black">{{ __('Subtotal:') }}</span>
                                    <span>${{ number_format($subtotal, 2) }}</span>
                                </li>
                                <li class="d-flex align-items-center justify-content-between">
                                    <span class="text-black">{{ __('Tax:') }}</span>
                                    <span>${{ number_format($tax, 2) }}</span>
                                </li>
                                <li class="d-flex align-items-center justify-content-between">
                                    <span class="text-black">
                                        {{ __('Discount:') }}
                                        @if ($activeCoupon)
                                            <span class="badge badge-success ms-2 text-uppercase">{{ $activeCoupon->code }}</span>
                                        @endif
                                    </span>
                                    <span>- ${{ number_format($discount, 2) }}</span>
                                </li>
                                <li class="d-flex align-items-center justify-content-between font-weight-bold border-top pt-3 mt-3">
                                    <span class="text-black">{{ __('Total:') }}</span>
                                    <span>${{ number_format($total, 2) }}</span>
                                </li>
                            </ul>
                            <a href="{{ route('checkout') }}" class="btn theme-btn w-100 mt-3">
                                {{ __('Checkout') }} <i class="la la-arrow-right icon ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

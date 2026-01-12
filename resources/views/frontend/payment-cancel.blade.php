@extends('frontend.app')

@section('content')
<section class="bg-red-50 py-16">
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl p-10 text-center">
        <h2 class="text-3xl font-bold text-red-700 mb-4">❌ Payment Canceled</h2>
        <p class="text-gray-600 text-lg mb-6">
            Your payment was canceled. You can return to your cart and try again.
        </p>
        <a href="{{ route('cart.view') }}" 
           class="bg-red-600 text-white px-6 py-3 rounded-lg shadow hover:bg-red-700 transition">
            Back to Cart
        </a>
    </div>
    </section>
@endsection


@extends('frontend.app')

@section('content')
<section class="bg-green-50 py-16">
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl p-10 text-center">
        <h2 class="text-3xl font-bold text-green-700 mb-4">✅ Payment Successful!</h2>
        <p class="text-gray-600 text-lg mb-6">
            Thank you for your purchase. Your payment has been received and your order is being processed.
        </p>
        <a href="{{ url('/') }}" 
           class="bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 transition">
            Go Back to Home
        </a>
    </div>
</section>
@endsection

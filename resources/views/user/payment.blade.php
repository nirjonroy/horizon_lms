@extends('frontend.app')

@section('content')
<section class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        
        <!-- Page Title -->
        <h2 class="text-3xl font-bold text-blue-800 mb-8 text-center">
            Payment
        </h2>

        <div class="bg-white shadow rounded-lg p-6">
            
            <!-- Order Summary -->
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Order Summary</h3>
            <div class="space-y-3 mb-6">
                <div class="flex justify-between text-gray-700">
                    <span>Master in Business</span>
                    <span>$1,299</span>
                </div>
                <div class="flex justify-between text-gray-700">
                    <span>Applied Business Studies</span>
                    <span>$999</span>
                </div>
            </div>
            <hr class="my-3">
            <div class="flex justify-between text-lg font-bold text-blue-800 mb-6">
                <span>Total</span>
                <span>$2,298</span>
            </div>

            <!-- Payment Options -->
            <h3 class="text-lg font-semibold mb-3 text-gray-800">Choose Payment Method</h3>
            <div class="space-y-4 mb-8">
                <label class="flex items-center gap-2">
                    <input type="radio" name="payment_method" value="card" class="text-blue-600 focus:ring-blue-500" checked>
                    <span>Credit / Debit Card</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="payment_method" value="paypal" class="text-blue-600 focus:ring-blue-500">
                    <span>PayPal</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="payment_method" value="bank" class="text-blue-600 focus:ring-blue-500">
                    <span>Bank Transfer</span>
                </label>
            </div>

            <!-- Card Payment Form -->
            <div id="card-payment" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cardholder Name</label>
                    <input type="text" class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="John Smith">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Card Number</label>
                    <input type="text" class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="1234 5678 9012 3456">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
                        <input type="text" class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="MM/YY">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">CVC</label>
                        <input type="text" class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="123">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ZIP</label>
                        <input type="text" class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="12345">
                    </div>
                </div>
            </div>

            <!-- Pay Now Button -->
            <a href="{{url('/user-dashboard')}}" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                <!-- Credit Card Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5m-16.5 6h4.5m-4.5 3h7.5m4.5-3h3m-16.5-9h19.5A2.25 2.25 0 0121.75 6v12a2.25 2.25 0 01-2.25 2.25H4.5A2.25 2.25 0 012.25 18V6A2.25 2.25 0 014.5 3.75z"/>
                </svg>
                Pay $2,298 Now
            </a>
        </div>
    </div>
</section>
@endsection

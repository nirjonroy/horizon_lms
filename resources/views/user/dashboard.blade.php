@extends('frontend.app')

@section('content')
@php
    $ordersCollection = collect($orders);
    $totalOrders = $ordersCollection->count();
    $totalSpent = $ordersCollection->sum('total');
    $lastOrder = $ordersCollection->sortByDesc('created_at')->first();
    $defaultAvatar = public_path('frontend/assets/images/user-placeholder.png');
    $avatar = file_exists($defaultAvatar)
        ? asset('frontend/assets/images/user-placeholder.png')
        : 'https://ui-avatars.com/api/?background=001d42&color=fff&name=' . urlencode(Auth::user()->name);
@endphp

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4 align-items-center mb-4">
            <div class="col-md-3 text-center text-md-start">
                <div class="position-relative d-inline-block">
                    <img src="{{ $avatar }}" alt="{{ Auth::user()->name }}" class="rounded-circle border border-3 border-primary-subtle shadow-sm" style="width:120px;height:120px;object-fit:cover;">
                </div>
            </div>
            <div class="col-md-9">
                <p class="text-uppercase text-muted mb-1">Welcome back</p>
                <h1 class="h3 fw-bold text-primary mb-2">{{ Auth::user()->name }}</h1>
                <p class="text-muted mb-3">{{ Auth::user()->email }}</p>
                <div class="row g-3">
                    <div class="col-sm-4">
                        <div class="border rounded-3 p-3 bg-white shadow-sm h-100">
                            <p class="text-muted mb-1">Total Orders</p>
                            <h4 class="mb-0">{{ $totalOrders }}</h4>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border rounded-3 p-3 bg-white shadow-sm h-100">
                            <p class="text-muted mb-1">Total Spent</p>
                            <h4 class="mb-0">${{ number_format($totalSpent, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border rounded-3 p-3 bg-white shadow-sm h-100">
                            <p class="text-muted mb-1">Last Order</p>
                            <h6 class="mb-0">{{ optional($lastOrder?->created_at)->format('M d, Y') ?? '—' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                    <div>
                        <h2 class="h4 mb-1 text-primary">My Orders</h2>
                        <p class="text-muted mb-0">Track your purchases and enrollment status.</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <a href="{{ route('premium-courses') }}" class="btn theme-btn">
                            Browse Courses <i class="la la-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Order #</th>
                                <th scope="col">Course</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                @php
                                    $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                                    $courseTitle = $items[0]['title'] ?? 'N/A';
                                    $statusClass = match(strtolower($order->status)) {
                                        'completed' => 'bg-success-subtle text-success',
                                        'pending' => 'bg-warning-subtle text-warning',
                                        'failed' => 'bg-danger-subtle text-danger',
                                        default => 'bg-secondary-subtle text-secondary'
                                    };
                                @endphp
                                <tr>
                                    <td class="fw-semibold">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $courseTitle }}</td>
                                    <td class="fw-semibold">${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>{{ optional($order->created_at)->format('M d, Y') ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="mb-2 text-muted">You haven't purchased any courses yet.</p>
                                        <a href="{{ route('premium-courses') }}" class="btn btn-outline-primary btn-sm">Explore Courses</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('backend.app')

@section('content')
<div class="content-wrapper">
    @include('backend.topnav')

    <section class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                <div>
                    <h3 class="mb-0">Coupons</h3>
                    <p class="text-muted mb-0">Create dynamic coupon codes and monitor their performance.</p>
                </div>
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> New Coupon
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Usage</th>
                                <th>Minimum</th>
                                <th>Validity</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                                @php
                                    $amountLabel = $coupon->type === 'percentage'
                                        ? $coupon->amount . '%'
                                        : '$' . number_format($coupon->amount, 2);
                                    $usageLimit = $coupon->usage_limit ? $coupon->usage_limit : '∞';
                                    $validity = [];
                                    if ($coupon->starts_at) {
                                        $validity[] = 'From ' . $coupon->starts_at->format('M d, Y H:i');
                                    }
                                    if ($coupon->expires_at) {
                                        $validity[] = 'Until ' . $coupon->expires_at->format('M d, Y H:i');
                                    }
                                    if (!$validity) {
                                        $validity[] = 'Always on';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $coupon->code }}</strong>
                                        @if($coupon->name)
                                            <div class="text-muted small">{{ $coupon->name }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ ucfirst($coupon->type) }}<br>
                                        <small class="text-muted">{{ $amountLabel }}</small>
                                    </td>
                                    <td>
                                        {{ $coupon->usageCount() }} / {{ $usageLimit }}
                                        @if($coupon->per_user_limit)
                                            <div class="small text-muted">per user: {{ $coupon->per_user_limit }}</div>
                                        @endif
                                    </td>
                                    <td>${{ number_format($coupon->min_subtotal ?? 0, 2) }}</td>
                                    <td class="small text-muted">
                                        {!! implode('<br>', $validity) !!}
                                    </td>
                                    <td>
                                        @if($coupon->isCurrentlyActive())
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this coupon?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No coupons found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $coupons->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

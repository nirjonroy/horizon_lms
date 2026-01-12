@extends('backend.app')

@section('content')
<div class="content-wrapper">
  @include('backend.topnav')

  <section class="content">
    <div class="container-fluid">
      <div class="row mt-3">
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title">Order #{{ $order->id }}</h3>
              <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-secondary">Back</a>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-md-6">
                  <h5>Customer</h5>
                  <p class="mb-1">
                    {{ $user->name ?? 'Guest' }}<br>
                    <small class="text-muted">{{ $user->email ?? '' }}</small>
                  </p>
                </div>
                <div class="col-md-6">
                  <h5>Payment</h5>
                  <ul class="list-unstyled mb-0">
                    <li>Status: <strong>{{ ucfirst($order->status) }}</strong></li>
                    @if($order->coupon_code)
                      <li>
                        Coupon:
                        <span class="badge badge-success">{{ $order->coupon_code }}</span>
                        @if($order->discount > 0)
                          <small class="text-muted">- ${{ number_format($order->discount, 2) }}</small>
                        @endif
                      </li>
                    @endif
                    @if($order->stripe_session_id)
                      <li>Session: <code>{{ $order->stripe_session_id }}</code></li>
                    @endif
                    @if($order->stripe_payment_intent_id)
                      <li>Payment Intent: <code>{{ $order->stripe_payment_intent_id }}</code></li>
                    @endif
                  </ul>
                </div>
              </div>

              <h5 class="mb-2">Items</h5>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Course</th>
                      <th class="text-center">Qty</th>
                      <th class="text-right">Unit Price</th>
                      <th class="text-right">Line Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php($items = is_array($order->items) ? $order->items : [])
                    @forelse($items as $it)
                      @php($qty = $it['quantity'] ?? 1)
                      <tr>
                        <td>{{ $it['title'] ?? ('#'.$it['id']) }}</td>
                        <td class="text-center">{{ $qty }}</td>
                        <td class="text-right">${{ number_format((float)($it['price'] ?? 0), 2) }}</td>
                        <td class="text-right">${{ number_format(((float)($it['price'] ?? 0)) * $qty, 2) }}</td>
                      </tr>
                    @empty
                      <tr><td colspan="4" class="text-center text-muted">No items</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              <div class="row justify-content-end">
                <div class="col-md-4">
                  <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Subtotal</span>
                      <strong>${{ number_format($order->subtotal, 2) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Tax</span>
                      <strong>${{ number_format($order->tax, 2) }}</strong>
                    </li>
                    @if($order->discount > 0)
                      <li class="list-group-item d-flex justify-content-between">
                        <span>Discount</span>
                        <strong>-${{ number_format($order->discount, 2) }}</strong>
                      </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Total</span>
                      <strong>${{ number_format($order->total, 2) }} {{ strtoupper($order->currency) }}</strong>
                    </li>
                  </ul>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

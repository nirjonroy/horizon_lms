@extends('backend.app')

@section('content')
<div class="content-wrapper">
  @include('backend.topnav')

  <section class="content">
    <div class="container-fluid">
      <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title">Orders</h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
              <tr>
                <td>#{{ $order->id }}</td>
                <td>
                  @php($u = $order->user ?? null)
                  {{ $u?->name ?? 'Guest' }}<br>
                  <small class="text-muted">{{ $u?->email }}</small>
                </td>
                <td>${{ number_format($order->total, 2) }} {{ strtoupper($order->currency) }}</td>
                <td><span class="badge badge-{{ $order->status === 'paid' ? 'success' : 'secondary' }}">{{ ucfirst($order->status) }}</span></td>
                <td>{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                <td>
                  <a class="btn btn-sm btn-primary" href="{{ route('admin.orders.show', $order->id) }}">View</a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center text-muted">No orders found.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          {{ $orders->links() }}
        </div>
      </div>
    </div>
  </section>
</div>
@endsection


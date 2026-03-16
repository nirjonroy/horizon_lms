@component('mail::message')
# New Order Received

A new order has been placed.

**Student Name:** {{ $user->name }}
**Student Email:** {{ $user->email }}

**Order Total:** ${{ number_format($order->total, 2) }}

### Ordered Items
@foreach($order->items as $item)
- {{ $item['title'] ?? ('#' . ($item['id'] ?? 'N/A')) }} ({{ $item['type_label'] ?? ucfirst($item['type'] ?? 'Item') }}) x {{ $item['quantity'] ?? 1 }} - ${{ number_format((float) ($item['price'] ?? 0), 2) }}
@endforeach

Thanks,<br>
{{ config('app.name') }}
@endcomponent

@component('mail::message')
# New Course Order 🚀

A new order has been placed.

**Student Name:** {{ $user->name }}  
**Student Email:** {{ $user->email }}  

**Order Total:** ${{ number_format($order->total, 2) }}

### Ordered Items:
@foreach($order->items as $item)
- {{ $item['title'] }} × {{ $item['quantity'] ?? 1 }} – ${{ number_format($item['price'], 2) }}
@endforeach

Thanks,<br>
{{ config('app.name') }}
@endcomponent

@component('mail::message')
# Thanks for Your Purchase

Hi {{ $user->name }},

We have received your order of **{{ count($order->items) }} item(s)**.

**Order Total:** ${{ number_format($order->total, 2) }}

### Ordered Items
@foreach($order->items as $item)
- {{ $item['title'] ?? ('#' . ($item['id'] ?? 'N/A')) }} x {{ $item['quantity'] ?? 1 }}
@endforeach

If your order includes e-books, bundles, or access plans, the matching download and library access is now available while you are logged in.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

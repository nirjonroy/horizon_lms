@component('mail::message')
# Thanks for Purchasing 🎉

Hi {{ $user->name }},

We’ve received your order of **{{ count($order->items) }} course(s)**.

**Order Total:** ${{ number_format($order->total, 2) }}

👉 Your LMS username & password will be sent within **12 hours**.  
Stay connected with us!

Thanks,<br>
{{ config('app.name') }}
@endcomponent

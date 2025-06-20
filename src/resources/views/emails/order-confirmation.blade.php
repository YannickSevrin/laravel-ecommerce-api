@component('mail::message')
# ✅ Order Confirmation

Hi {{ $order->user->name }},  
Thanks for your order #{{ $order->id }} placed on {{ $order->created_at->format('d M Y') }}.

## Order Summary:
@foreach ($order->items as $item)
- {{ $item->product->name }} x {{ $item->quantity }} — {{ number_format($item->price * $item->quantity, 2) }} €
@endforeach

**Total:** {{ number_format($order->total, 2) }} €

@component('mail::button', ['url' => route('my-orders.show', $order)])
View My Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
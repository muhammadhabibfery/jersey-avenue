<x-mail::message>

# The transaction with invoice number {{ $order->invoice_number }} is **Success**.
----------------------------------------------------------------------------------
<br>

## Here detail of the order :

@php
$totalPriceAll = 0;
@endphp
<x-mail::table>
| Jersey | Name |  Size | Quantity |  Price | Nameset Price | Total Price |
| - | :-: | :-: | :-: | :-: | :-: | :-: |
@foreach ($order->jerseys as $jersey)
@php
$totalPriceAll += $jersey->pivot->total_price;
@endphp
| <img src="{{ $jersey->getImage() }}" alt="{{ $jersey->name }} image"> | {{ $jersey->name }} | {{ $jersey->pivot->size }} | {{ $jersey->pivot->quantity }} | {{ currencyFormat($jersey->price) }} | {{ currencyFormat($jersey->price_nameset) }} | {{ currencyFormat($jersey->pivot->total_price) }}
@endforeach
</x-mail::table>

## **Total Price Jerseys** : **{{ currencyFormat($totalPriceAll) }}**
<br>

## Courier service details

@php
    $courier = $order->courier_services
@endphp

<h3>{{ strtoupper($courier['code']) }} - {{ $courier['service'] }} {{ $courier['description'] }} {{ __('etd') }} : {{ $courier['etd'] }}</h3>
<br>

<h2>Total costs of courier services : {{ currencyFormat($courier['value']) }}</h2>

<h2>Total price of the order : {{ currencyFormat($order->total_price) }}</h2>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

<x-mail::message>
# ¡Hola {{ $name }}!

Gracias por tu compra en Tortuga Second Hand 🐢

Aquí está el resumen de tu compra:

<x-mail::table>
| Producto     | Precio (UYU)     |
| ------------ | ---------------: |
@foreach ($items as $item)
| {{ Str::ucfirst($item['name']) }} | ${{ number_format($item['price'], 2, ',', '.') }} |
@endforeach
| **Total**    | **${{ number_format($total, 2, ',', '.') }}** |
</x-mail::table>

Podés ver tu recibo de pago haciendo clic en el siguiente enlace:

<x-mail::button :url="$receiptLink">
Ver Recibo
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
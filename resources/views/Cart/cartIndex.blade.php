<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1 class="text-2xl font-bold mb-6">Tu carrito</h1>

@if(session('pedido'))
    <ul>
        @foreach(session('pedido') as $item)
            <li>{{ $item['nombre'] }} x {{ $item['quantity'] }} = ${{ $item['subtotal'] }}</li>
        @endforeach
    </ul>
    <p>Total: ${{ array_sum(array_column(session('pedido'), 'subtotal')) }}</p>
@else
    <p>No hay productos en el carrito</p>
@endif

</body>
</html>
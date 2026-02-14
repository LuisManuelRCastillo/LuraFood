<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen del Pedido - Menú Coffee</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('faviconn.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <!-- Mensaje de agradecimiento -->
        <h1 class="text-2xl font-extrabold text-center text-green-600 mb-6">
            ¡Gracias por tu compra{{ !empty($cliente['nombre']) ? ', ' . $cliente['nombre'] : '' }}!
        </h1>

        <!-- Resumen del pedido -->
        <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Resumen del Pedido</h2>

        @if(!empty($cliente['nombre']) || !empty($cliente['email']))
        <p class="text-gray-700 text-center mb-6">
            <strong>Cliente:</strong> {{ $cliente['nombre'] ?? 'Cliente Anónimo' }}
            @if(!empty($cliente['email']))
                <br><span class="text-sm text-gray-500">({{ $cliente['email'] }})</span>
            @endif
        </p>
        @endif

        <ul class="divide-y divide-gray-200 mb-6">
            @foreach($pedido as $item)
                <li class="py-2 flex justify-between text-gray-700">
                    <span>{{ $item['nombre'] }} x {{ $item['quantity'] }}</span>
                    <span class="font-semibold">${{ $item['subtotal'] }}</span>
                </li>
                 @if(isset($item['leche']))
                        <p class="text-sm text-gray-500">Leche: {{ $item['leche'] }}</p>
                    @endif
                @if(isset($item['extras']) && count($item['extras']) > 0)
<p class="text-sm text-gray-500">Extras: {{ implode(', ', $item['extras']) }}</p>
@endif
            @endforeach
        </ul>

        <p class="text-lg font-bold text-gray-800 text-right mb-6">
            Total: ${{ array_sum(array_column($pedido, 'subtotal')) }}
        </p>

        <!-- Botón finalizar -->
        <form action="{{ route('pedidos.finalizar') }}" method="POST" class="text-center">
            @csrf
            <button type="submit" 
                    class="w-full px-4 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition">
                Finalizar Pedido
            </button>
        </form>
    </div>

</body>
</html>

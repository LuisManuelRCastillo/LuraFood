<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('faviconn.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full mx-auto p-4">
        <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-800 font-semibold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <div class="text-center py-6">
                <h2 class="text-2xl font-extrabold text-green-600 mb-2">Tu pedido fue confirmado</h2>
                @if(session('pedido_id'))
                    <div class="bg-green-100 border border-green-300 rounded-lg p-4 mt-3">
                        <p class="text-sm text-green-700">Tu numero de pedido es el:</p>
                        <p class="text-3xl font-extrabold text-green-700">#{{ session('pedido_id') }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 my-6">
                <h3 class="font-bold text-blue-800 mb-2">Proximos pasos:</h3>
                <ul class="text-blue-800 space-y-2 text-sm">
                    <li>✓ Tu pedido <strong>#{{ session('pedido_id') }}</strong> esta siendo preparado en la cocina</li>
                    <li>✓ Te lo entregaremos en poco tiempo</li>
                    <li>✓ <strong>Puedes pagar al retirar el pedido</strong></li>
                </ul>
            </div>

            <div class="space-y-3 mt-6">
                <a href="{{ route('categorias.index') }}"
                   class="w-full block text-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition">
                    Hacer otro pedido
                </a>
                <a href="/"
                   class="w-full block text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition">
                    Ir a inicio
                </a>
            </div>

        </div>
    </div>

</body>
</html>

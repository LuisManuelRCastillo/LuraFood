<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Menú Coffee</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('faviconn.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto my-10 px-4">
<h1 class="text-2xl font-bold mb-6">Registro de Cliente</h1>

<form action="{{ route('pedidos.guardarCliente') }}" method="POST" class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
    @csrf
    <div class="mb-4">
        <label class="block mb-1 font-semibold">Nombre</label>
        <input type="text" name="nombre" class="w-full border px-3 py-2 rounded" placeholder="Ej: Juan">
    </div>

    <div class="mb-4">
        <label class="block mb-1 font-semibold">Correo electrónico <span class="text-gray-500 font-normal">(opcional)</span></label>
        <input type="email" name="email" class="w-full border px-3 py-2 rounded" placeholder="Ej: juan@email.com">
    </div>

    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Continuar</button>
</form>
    </div>
</body>  
</html>
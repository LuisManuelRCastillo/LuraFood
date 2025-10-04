<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Menú Coffee</title>
    
    <!-- Directivas de Vite para cargar Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

    <!-- Tu contenido aquí -->
    <div class="flex justify-center items-center min-h-screen">
        <a href="{{ route('categorias.index') }}"
           class="px-8 py-4 bg-green-600 text-white text-lg font-semibold rounded-lg shadow-lg hover:bg-green-700 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            Iniciar Pedido
        </a>
    </div>

</body>
</html>
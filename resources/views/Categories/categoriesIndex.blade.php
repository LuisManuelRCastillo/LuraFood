<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Menú Coffee</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
 
    <div class="max-w-6xl mx-auto my-10 px-4">
        <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">Selecciona una categoría</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categorias as $categoria)
                <a href="{{ route('productos.index', $categoria->id) }}"
                   class="group p-8 bg-white shadow-md rounded-xl text-center hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-2 border-transparent hover:border-green-500">
                    <div class="mb-4">
                        <div class="w-25 h-25 mx-auto bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition-colors">
                              @switch($categoria->id)
            @case(1)
                <img src="{{ asset('assets/img/2.png') }}" alt="Bebidas Calientes" class="w-15 h-15" style="padding: 5px;">
                @break
            @case(2)
                <img src="{{ asset('assets/img/3.png') }}" alt="Bebidas Frías" class="w-15 h-15" style="padding: 5px;">
                @break
            @default
                <img src="{{ asset('assets/img/4.png') }}" alt="Alimentos" class="w-15 h-15" style="padding: 5px;">
        @endswitch
                        </div>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                        {{ $categoria->descripcion }}
                    </h2>
                </a>
            @endforeach
        </div>
    </div>

</body>
</html>
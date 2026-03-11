<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-green-700">Productos</h1>
            <a href="{{ route('admin.productos.create') }}"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo Producto
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Filtro por categoría --}}
            <form method="GET" class="mb-4 flex gap-3 items-center">
                <select name="categoria"
                        onchange="this.form.submit()"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->descripcion }}
                        </option>
                    @endforeach
                </select>
                @if(request('categoria'))
                    <a href="{{ route('admin.productos.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Limpiar filtro</a>
                @endif
            </form>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-green-600 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Categoría</th>
                            <th class="px-4 py-3 text-right">Precio</th>
                            <th class="px-4 py-3 text-right">Cantidad</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($productos as $producto)
                        <tr class="hover:bg-gray-50 {{ !$producto->activo ? 'opacity-50' : '' }}">
                            <td class="px-4 py-3 text-gray-500">{{ $producto->id }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $producto->nombre }}</p>
                                @if($producto->descripcion)
                                    <p class="text-xs text-gray-400 truncate max-w-xs">{{ $producto->descripcion }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $producto->categoria?->descripcion ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-green-700">${{ number_format($producto->precio, 2) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ $producto->qty }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($producto->activo)
                                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full">Activo</span>
                                @else
                                    <span class="bg-gray-100 text-gray-500 text-xs font-bold px-2 py-1 rounded-full">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.productos.edit', $producto->id) }}"
                                       class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition">
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.productos.destroy', $producto->id) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar el producto {{ $producto->nombre }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay productos. Crea el primero.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>

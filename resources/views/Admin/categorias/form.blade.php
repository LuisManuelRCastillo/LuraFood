<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-green-700">
            {{ $categoria ? 'Editar Categoría' : 'Nueva Categoría' }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-lg mx-auto px-4 sm:px-6">
            <div class="bg-white shadow rounded-lg p-6">

                <form action="{{ $categoria ? route('admin.categorias.update', $categoria->id) : route('admin.categorias.store') }}"
                      method="POST">
                    @csrf
                    @if($categoria)
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre de la categoría</label>
                        <input type="text" name="descripcion"
                               value="{{ old('descripcion', $categoria?->descripcion) }}"
                               placeholder="Ej. Bebidas Calientes"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('descripcion') border-red-400 @enderror">
                        @error('descripcion')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tipo" value="bebida"
                                       {{ old('tipo', $categoria?->tipo ?? 'bebida') === 'bebida' ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600">
                                <span class="text-sm text-gray-700">☕ Bebida <span class="text-gray-400 text-xs">(con opciones de tamaño, leche y extras)</span></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tipo" value="alimento"
                                       {{ old('tipo', $categoria?->tipo) === 'alimento' ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600">
                                <span class="text-sm text-gray-700">🍽️ Alimento <span class="text-gray-400 text-xs">(sin opciones extra)</span></span>
                            </label>
                        </div>
                        @error('tipo')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.categorias.index') }}"
                           class="flex-1 text-center py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition">
                            {{ $categoria ? 'Guardar cambios' : 'Crear categoría' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

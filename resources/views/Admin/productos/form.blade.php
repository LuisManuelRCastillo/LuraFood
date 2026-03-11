<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-green-700">
            {{ $producto ? 'Editar Producto' : 'Nuevo Producto' }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto px-4 sm:px-6">
            <div class="bg-white shadow rounded-lg p-6">

                <form action="{{ $producto ? route('admin.productos.update', $producto->id) : route('admin.productos.store') }}"
                      method="POST">
                    @csrf
                    @if($producto)
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre"
                               value="{{ old('nombre', $producto?->nombre) }}"
                               placeholder="Ej. Café Espresso"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('nombre') border-red-400 @enderror">
                        @error('nombre') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="2"
                                  placeholder="Descripción breve del producto"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('descripcion', $producto?->descripcion) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Precio base ($) <span class="text-red-500">*</span></label>
                            <input type="number" name="precio" step="0.01" min="0"
                                   value="{{ old('precio', $producto?->precio) }}"
                                   placeholder="0.00"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('precio') border-red-400 @enderror">
                            @error('precio') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Cantidad (ml o g)</label>
                            <input type="number" name="qty" min="0"
                                   value="{{ old('qty', $producto?->qty) }}"
                                   placeholder="0"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                        <select name="id_cat"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('id_cat') border-red-400 @enderror">
                            <option value="">Selecciona una categoría</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('id_cat', $producto?->id_cat) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->descripcion }} ({{ $cat->tipo === 'bebida' ? '☕ Bebida' : '🍽️ Alimento' }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_cat') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="activo" value="1"
                                   {{ old('activo', $producto?->activo ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-green-600 rounded">
                            <span class="text-sm font-semibold text-gray-700">Producto activo <span class="text-gray-400 font-normal">(visible en el menú)</span></span>
                        </label>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.productos.index') }}"
                           class="flex-1 text-center py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition">
                            {{ $producto ? 'Guardar cambios' : 'Crear producto' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

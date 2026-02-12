<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-green-700">Tu Carrito</h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            @if(session('pedido') && count(session('pedido')) > 0)
                <div class="grid grid-cols-12 gap-6">
                    <!-- Carrito -->
                    <div class="col-span-12 lg:col-span-7">
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-xl font-bold mb-4 text-gray-800">Productos</h2>
                            <div class="space-y-4">
                                @foreach(session('pedido') as $key => $item)
                                    <div class="flex justify-between items-center border-b pb-4">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">{{ $item['nombre'] }}</p>
                                            @if($item['tamano'])
                                                <p class="text-sm text-gray-600">Tamaño: {{ ucfirst($item['tamano']) }}</p>
                                            @endif
                                            @if($item['leche'])
                                                <p class="text-sm text-gray-600">Leche: {{ ucfirst($item['leche']) }}</p>
                                            @endif
                                            @if(isset($item['extras']) && count($item['extras']) > 0)
                                                <p class="text-sm text-gray-600">Extras: {{ implode(', ', array_map('ucfirst', $item['extras'])) }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-green-600">${{ $item['subtotal'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $item['quantity'] }}x @ ${{ $item['precio'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6 pt-4 border-t-2 border-gray-300">
                                <h3 class="text-lg font-bold text-gray-800">
                                    Total: <span class="text-green-600">${{ array_sum(array_column(session('pedido'), 'subtotal')) }}</span>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Checkout -->
                    <div class="col-span-12 lg:col-span-5">
                        <div class="bg-gray-50 shadow rounded-lg p-6 sticky top-6">
                            <h2 class="text-xl font-bold mb-4 text-gray-800">Confirmar Pedido</h2>
                            
                            <form action="{{ route('pedidos.confirmar-rapido') }}" method="POST" class="space-y-4">
                                @csrf
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre <span class="text-gray-500">(opcional)</span>
                                    </label>
                                    <input type="text" name="nombre" placeholder="Ej: Juan" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Email <span class="text-gray-500">(opcional)</span>
                                    </label>
                                    <input type="email" name="email" placeholder="Ej: juan@email.com" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                                    <p><strong>Nota:</strong> Los datos son opcionales. Si no los proporcionas, sera un pedido anonimo.</p>
                                </div>

                                <button type="submit" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition">
                                    Confirmar Pedido
                                </button>

                                <a href="{{ route('categorias.index') }}" 
                                   class="w-full block text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition">
                                    ← Seguir Comprando
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <p class="text-gray-500 text-xl mb-4">Tu carrito está vacío</p>
                    <a href="{{ route('categorias.index') }}" 
                       class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        Explorar Menu
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
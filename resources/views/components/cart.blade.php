<div>
    @if(session('pedido') && count(session('pedido')) > 0)
        <ul class="space-y-4">
            @foreach(session('pedido') as $key => $item)
            <li class="border-b pb-3">
                {{-- Fila 1: Nombre del producto y botón eliminar --}}
                <div class="flex justify-between items-start mb-1">
                    <div class="flex-1 min-w-0 pr-2">
                        <p class="font-semibold text-gray-800 text-sm leading-tight">
                            {{ $item['nombre'] }}
                            @if(isset($item['tamano']) && !empty($item['tamano']))
                                <span class="text-green-600">({{ ucfirst($item['tamano']) }})</span>
                            @endif
                        </p>
                        @if(isset($item['leche']))
                            <p class="text-xs text-gray-400">Leche: {{ ucfirst($item['leche']) }}</p>
                        @endif
                        @if(isset($item['extras']) && count($item['extras']) > 0)
                            <p class="text-xs text-gray-400">Extras: {{ implode(', ', array_map('ucfirst', $item['extras'])) }}</p>
                        @endif
                    </div>
                    <a href="{{ route('pedidos.eliminar', $item['product_id']) }}"
                       class="btn-eliminar w-9 h-9 flex-shrink-0 flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-700 rounded-full text-lg font-bold active:bg-red-200 select-none transition">
                        &times;
                    </a>
                </div>

                {{-- Fila 2: Controles de cantidad y precio --}}
                <div class="flex justify-between items-center mt-2">
                    <div class="flex items-center space-x-3">
                        <form action="{{ route('pedido.menos', $key) }}" method="POST" class="form-menos">
                            @csrf
                            <button type="submit"
                                    class="w-12 h-12 flex items-center justify-center bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-full text-2xl font-bold text-gray-600 select-none transition">
                                &minus;
                            </button>
                        </form>
                        <span class="font-bold text-lg w-8 text-center text-gray-800">{{ $item['quantity'] }}</span>
                        <form action="{{ route('pedido.mas', $key) }}" method="POST" class="form-mas">
                            @csrf
                            <button type="submit"
                                    class="w-12 h-12 flex items-center justify-center bg-green-100 hover:bg-green-200 active:bg-green-300 rounded-full text-2xl font-bold text-green-700 select-none transition">
                                +
                            </button>
                        </form>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-lg text-green-600">${{ number_format($item['subtotal'], 2) }}</span>
                        @if($item['quantity'] > 1)
                            <p class="text-xs text-gray-400">c/u ${{ number_format($item['precio'], 2) }}</p>
                        @endif
                    </div>
                </div>
            </li>
            @endforeach
        </ul>

        <div class="mt-4 pt-3 border-t-2 border-green-200">
            <div class="flex justify-between items-center">
                <span class="text-lg font-bold text-gray-700">Total</span>
                <span class="text-2xl font-bold text-green-700">${{ number_format(array_sum(array_column(session('pedido'), 'subtotal')), 2) }}</span>
            </div>
        </div>

        {{-- Checkout rápido integrado --}}
        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm font-semibold text-gray-700 mb-2">Confirmar pedido</p>
            <form action="{{ route('pedidos.confirmar-rapido') }}" method="POST" class="space-y-2 form-confirmar-pedido">
                @csrf
                <input type="text" name="nombre" placeholder="Nombre (opcional)"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <input type="email" name="email" placeholder="Email (opcional)"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-400">Si no los proporcionas, será un pedido anónimo.</p>
                <button type="submit"
                        class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold transition text-base active:bg-green-800">
                    Confirmar Pedido
                </button>
            </form>
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-400 text-lg">Tu carrito está vacío</p>
            <p class="text-gray-300 text-sm mt-1">Agrega productos para comenzar</p>
        </div>
    @endif
</div>

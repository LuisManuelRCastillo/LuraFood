<div>
    @if(session('pedido') && count(session('pedido')) > 0)
        <ul class="space-y-3">
            @foreach(session('pedido') as $key => $item)
            <li class="flex justify-between items-center border-b pb-2">
                <div>
                    <p class="font-semibold">
                        {{ $item['nombre'] }}  @if(isset($item['tamano']) && !empty($item['tamano']))
                            ({{ ucfirst($item['tamano']) }})
                        @endif x {{ $item['quantity'] }}
                    </p>
                    @if(isset($item['leche']))
                        <p class="text-sm text-gray-500">Leche: {{ $item['leche'] }}</p>
                    @endif
                    @if(isset($item['extras']) && count($item['extras']) > 0)
                        <p class="text-sm text-gray-500">Extras: {{ implode(', ', $item['extras']) }}</p>
                    @endif
                </div>
                <div class="flex items-center space-x-1">
                    <form action="{{ route('pedido.menos', $key) }}" method="POST" class="form-menos">
                        @csrf
                        <button type="submit" class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded-full text-lg font-bold active:bg-gray-300 select-none">-</button>
                    </form>
                    <span class="font-bold w-6 text-center">{{ $item['quantity'] }}</span>
                    <form action="{{ route('pedido.mas', $key) }}" method="POST" class="form-mas">
                        @csrf
                        <button type="submit" class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded-full text-lg font-bold active:bg-gray-300 select-none">+</button>
                    </form>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-green-600">${{ $item['subtotal'] }}</span>
                    <a href="{{ route('pedidos.eliminar', $item['product_id']) }}" class="btn-eliminar w-8 h-8 flex items-center justify-center text-red-600 hover:text-red-800 text-xl active:text-red-900 select-none">&times;</a>
                </div>
            </li>
            @endforeach
        </ul>

        <p class="font-bold mt-4 text-lg">Total: ${{ array_sum(array_column(session('pedido'), 'subtotal')) }}</p>

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
                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold transition">
                    Confirmar Pedido
                </button>
            </form>
        </div>
    @else
        <p>No hay productos en el carrito</p>
    @endif
</div>

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
                <div class="flex items-center space-x-2">
                    <form action="{{ route('pedido.menos', $key) }}" method="POST" class="form-menos">
                        @csrf
                        <button class="px-2 bg-gray-200 rounded">-</button>
                    </form>
                    <span class="font-bold">{{ $item['quantity'] }}</span>
                    <form action="{{ route('pedido.mas', $key) }}" method="POST" class="form-mas">
                        @csrf
                        <button class="px-2 bg-gray-200 rounded">+</button>
                    </form>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-green-600">${{ $item['subtotal'] }}</span>
                    <a href="{{ route('pedidos.eliminar', $item['product_id']) }}" class="btn-eliminar text-red-600 hover:text-red-800 text-xl">&times;</a>
                </div>
            </li>
            @endforeach
        </ul>

        <p class="font-bold mt-4 text-lg">Total: ${{ array_sum(array_column(session('pedido'), 'subtotal')) }}</p>
        <a href="{{ route('pedidos.cliente') }}" class="mt-4 inline-block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Ir al pago
        </a>
    @else
        <p>No hay productos en el carrito</p>
    @endif
</div>

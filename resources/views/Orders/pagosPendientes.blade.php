<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-green-700">Pagos Pendientes</h1>
            <div class="text-right">
                <p class="text-gray-600 text-sm">Total pendiente</p>
                <p class="text-4xl font-bold text-red-600">${{ $totalPendiente }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if($pedidosPendientes->count() > 0)
                <div class="grid gap-4">
                    @foreach($pedidosPendientes as $pedido)
                        <div class="bg-white shadow rounded-lg p-6 border-l-4 border-red-500">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-800">
                                        Pedido #{{ $pedido->id }}
                                    </h2>
                                    <p class="text-gray-600 text-sm">
                                        Cliente: <strong>{{ $pedido->customer_name }}</strong>
                                        @if($pedido->customer_email)
                                            <span class="text-gray-500">({{ $pedido->customer_email }})</span>
                                        @endif
                                    </p>
                                    <p class="text-gray-600 text-sm">
                                        Entregado: {{ $pedido->updated_at?->format('d/m/Y H:i') ?? 'Sin fecha' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-3xl font-bold text-red-600">${{ $pedido->total }}</p>
                                    <p class="text-xs text-gray-500">PENDIENTE DE PAGO</p>
                                </div>
                            </div>

                            <!-- Productos -->
                            <div class="bg-gray-50 rounded p-4 mb-4">
                                <p class="font-semibold text-gray-800 mb-2">Productos:</p>
                                <ul class="space-y-1 text-sm text-gray-700">
                                    @foreach($pedido->items as $item)
                                        <li>
                                            {{ $item->quantity }}x {{ $item->producto->nombre }}
                                            @if($item->tamano) - {{ ucfirst($item->tamano) }} @endif
                                            = <strong>${{ $item->subtotal }}</strong>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Opciones de Pago -->
                            <div class="space-y-3">
                                <p class="font-semibold text-gray-800 text-sm">Registrar Pago:</p>
                                <div class="flex gap-2 flex-wrap">
                                    <form action="{{ route('pedidos.marcar-pagado', $pedido->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="cash">
                                        <button type="submit"
                                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                                            Efectivo
                                        </button>
                                    </form>

                                    <form action="{{ route('pedidos.marcar-pagado', $pedido->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="card">
                                        <button type="submit"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                            Tarjeta
                                        </button>
                                    </form>

                                    <form action="{{ route('pedidos.marcar-pagado', $pedido->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="transfer">
                                        <button type="submit"
                                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition">
                                            Transferencia
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <p class="text-gray-500 text-xl mb-4">No hay pagos pendientes</p>
                    <a href="{{ route('pedidos.pendientes') }}"
                       class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        Ver Ordenes en Preparacion
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

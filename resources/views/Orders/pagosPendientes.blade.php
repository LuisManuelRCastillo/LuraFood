<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl sm:text-3xl font-bold text-green-700">Pagos Pendientes</h1>
            <div class="text-right">
                <p class="text-gray-600 text-xs sm:text-sm">Total pendiente</p>
                <p class="text-2xl sm:text-4xl font-bold text-red-600">${{ $totalPendiente }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if($pedidosPendientes->count() > 0)
                <div class="grid gap-4">
                    @foreach($pedidosPendientes as $pedido)
                        <div class="bg-white shadow rounded-lg p-4 sm:p-6 border-l-4 border-red-500">
                            <div class="flex justify-between items-start mb-4">
                                <div class="min-w-0 flex-1 pr-3">
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-800">
                                        Pedido #{{ $pedido->id }}
                                    </h2>
                                    <p class="text-gray-600 text-sm truncate">
                                        Cliente: <strong>{{ $pedido->customer_name }}</strong>
                                        @if($pedido->customer_email)
                                            <span class="text-gray-500 hidden sm:inline">({{ $pedido->customer_email }})</span>
                                        @endif
                                    </p>
                                    @if($pedido->customer_email)
                                        <p class="text-gray-500 text-xs sm:hidden">{{ $pedido->customer_email }}</p>
                                    @endif
                                    <p class="text-gray-600 text-sm">
                                        Entregado: {{ $pedido->updated_at?->format('d/m/Y H:i') ?? 'Sin fecha' }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-xl sm:text-3xl font-bold text-red-600">${{ $pedido->total }}</p>
                                    <p class="text-xs text-gray-500">PENDIENTE DE PAGO</p>
                                </div>
                            </div>

                            <!-- Productos -->
                            <div class="bg-gray-50 rounded p-3 sm:p-4 mb-4">
                                <p class="font-semibold text-gray-800 mb-2 text-sm">Productos:</p>
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
                            <div>
                                <p class="font-semibold text-gray-800 text-xs mb-2">Registrar Pago:</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <form action="{{ route('pedidos.marcar-pagado', $pedido->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="cash">
                                        <button type="submit"
                                                class="w-full px-2 py-3 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-bold rounded-lg transition flex flex-col items-center gap-1 text-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            <span>Efectivo</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('pedidos.marcar-pagado', $pedido->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="card">
                                        <button type="submit"
                                                class="w-full px-2 py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold rounded-lg transition flex flex-col items-center gap-1 text-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            <span>Tarjeta</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('pedidos.marcar-pagado', $pedido->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="transfer">
                                        <button type="submit"
                                                class="w-full px-2 py-3 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-bold rounded-lg transition flex flex-col items-center gap-1 text-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                            <span>Transfer.</span>
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

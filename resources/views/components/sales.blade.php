@props(['pedidos', 'totalVentas', 'productosMasVendidos', 'ventasPorDia', 'productoMasVendido', 'metodosPago', 'totalPagado', 'totalSinPagar', 'pedidosSinPagar'])

<div class="p-6 max-w-7xl mx-auto">

    {{-- Tarjetas resumen --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <div class="bg-white shadow rounded-lg p-4 border-t-4 border-green-500">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Ventas Totales</p>
            <h2 class="text-2xl font-bold text-green-600 mt-1">${{ number_format($totalVentas, 2) }}</h2>
        </div>
        <div class="bg-white shadow rounded-lg p-4 border-t-4 border-green-500">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Pedidos Entregados</p>
            <h2 class="text-2xl font-bold text-green-600 mt-1">{{ $pedidos->count() }}</h2>
        </div>
        <div class="bg-white shadow rounded-lg p-4 border-t-4 border-green-500">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Ticket Promedio</p>
            <h2 class="text-2xl font-bold text-green-600 mt-1">
                ${{ $pedidos->count() > 0 ? number_format($pedidos->avg('total'), 2) : '0.00' }}
            </h2>
        </div>
        <div class="bg-white shadow rounded-lg p-4 border-t-4 border-blue-500">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Producto Top</p>
            <h2 class="text-lg font-bold text-blue-600 mt-1">{{ $productoMasVendido ?? 'N/A' }}</h2>
        </div>
        <div class="bg-white shadow rounded-lg p-4 border-t-4 border-emerald-500">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Cobrado</p>
            <h2 class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($totalPagado, 2) }}</h2>
        </div>
        <div class="bg-white shadow rounded-lg p-4 border-t-4 border-red-500">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Por Cobrar</p>
            <h2 class="text-2xl font-bold text-red-600 mt-1">${{ number_format($totalSinPagar, 2) }}</h2>
            @if($pedidosSinPagar > 0)
                <p class="text-xs text-red-400 mt-1">{{ $pedidosSinPagar }} pedido{{ $pedidosSinPagar > 1 ? 's' : '' }}</p>
            @endif
        </div>
    </div>

    {{-- Graficas fila 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ventas por Dia</h3>
            <canvas id="ventasPorDia"></canvas>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Productos Mas Vendidos</h3>
            <canvas id="productosMasVendidos"></canvas>
        </div>
    </div>

    {{-- Graficas fila 2: Metodos de pago --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Metodos de Pago (cantidad)</h3>
            <div class="flex justify-center" style="max-height: 300px;">
                <canvas id="metodosPagoCant"></canvas>
            </div>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ingresos por Metodo de Pago</h3>
            <canvas id="metodosPagoTotal"></canvas>
        </div>
    </div>

    {{-- Tabla de pedidos mejorada --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Historial de Ventas</h3>
            <p class="text-sm text-gray-500">{{ $pedidos->count() }} pedidos</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-green-600 text-white">
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">#</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Cliente</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Mesa</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Productos</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Total</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Pago</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pedidos->sortByDesc('id') as $pedido)
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $pedido->id }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $pedido->customer_name ?? 'Anonimo' }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $pedido->mesa ?? '-' }}</td>
                        <td class="py-3 px-4 text-sm">
                            <ul class="space-y-0.5">
                                @foreach($pedido->items as $item)
                                    <li class="text-gray-600">
                                        <span class="font-medium">{{ $item->quantity }}x</span>
                                        {{ $item->producto->nombre ?? 'Producto' }}
                                        @if($item->tamano)
                                            <span class="text-gray-400">({{ ucfirst($item->tamano) }})</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="py-3 px-4 text-sm font-bold text-green-600">${{ number_format($pedido->total, 2) }}</td>
                        <td class="py-3 px-4 text-sm">
                            @if($pedido->payment_status === 'paid')
                                @switch($pedido->payment_method)
                                    @case('cash')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Efectivo</span>
                                        @break
                                    @case('card')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Tarjeta</span>
                                        @break
                                    @case('transfer')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Transferencia</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $pedido->payment_method ?? '-' }}</span>
                                @endswitch
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Sin pagar</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm">
                            @if($pedido->payment_status === 'paid')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Pagado</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pendiente</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-500">{{ $pedido->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ventasPorDia = @json($ventasPorDia->toArray());
    const productosMasVendidos = @json($productosMasVendidos->toArray());
    const metodosPago = @json($metodosPago->toArray());

    // Ventas por dia - Linea
    new Chart(document.getElementById('ventasPorDia').getContext('2d'), {
        type: 'line',
        data: {
            labels: ventasPorDia.map(v => v.fecha),
            datasets: [{
                label: 'Ventas ($)',
                data: ventasPorDia.map(v => v.total),
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,0.1)',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#16a34a',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => '$' + v } }
            }
        }
    });

    // Productos mas vendidos - Barras horizontales
    new Chart(document.getElementById('productosMasVendidos').getContext('2d'), {
        type: 'bar',
        data: {
            labels: productosMasVendidos.map(p => p.nombre),
            datasets: [{
                label: 'Cantidad',
                data: productosMasVendidos.map(p => p.cantidad),
                backgroundColor: ['#16a34a', '#22c55e', '#4ade80', '#86efac', '#bbf7d0']
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Metodos de pago - Dona
    const coloresMetodo = {
        'Efectivo': '#16a34a',
        'Tarjeta': '#2563eb',
        'Transferencia': '#9333ea',
        'Sin registro': '#9ca3af'
    };

    if (metodosPago.length > 0) {
        new Chart(document.getElementById('metodosPagoCant').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: metodosPago.map(m => m.metodo),
                datasets: [{
                    data: metodosPago.map(m => m.cantidad),
                    backgroundColor: metodosPago.map(m => coloresMetodo[m.metodo] || '#6b7280'),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Ingresos por metodo - Barras
        new Chart(document.getElementById('metodosPagoTotal').getContext('2d'), {
            type: 'bar',
            data: {
                labels: metodosPago.map(m => m.metodo),
                datasets: [{
                    label: 'Ingresos ($)',
                    data: metodosPago.map(m => m.total),
                    backgroundColor: metodosPago.map(m => coloresMetodo[m.metodo] || '#6b7280'),
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => '$' + v } }
                }
            }
        });
    } else {
        document.getElementById('metodosPagoCant').parentElement.innerHTML += '<p class="text-center text-gray-400 mt-4">Sin datos de pago registrados</p>';
        document.getElementById('metodosPagoTotal').parentElement.innerHTML += '<p class="text-center text-gray-400 mt-4">Sin datos de pago registrados</p>';
    }
});
</script>

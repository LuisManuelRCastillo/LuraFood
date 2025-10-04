<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Ventas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">

<div class="p-6 max-w-7xl mx-auto">
    

    {{-- Tarjetas resumen --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-600 text-sm">Ventas Totales</p>
            <h2 class="text-2xl font-bold text-green-600">${{ number_format($totalVentas, 2) }}</h2>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-600 text-sm">Pedidos Entregados</p>
            <h2 class="text-2xl font-bold text-green-600">{{ $pedidos->count() }}</h2>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-600 text-sm">Ticket Promedio</p>
            <h2 class="text-2xl font-bold text-green-600">
                ${{ number_format($pedidos->avg('total'), 2) }}
            </h2>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-600 text-sm">Producto Más Vendido</p>
            <h2 class="text-xl font-bold text-green-600">{{ $productoMasVendido ?? 'N/A' }}</h2>
        </div>
    </div>

    {{-- Gráficas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ventas por Día</h3>
            <canvas id="ventasPorDia"></canvas>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Productos Más Vendidos</h3>
            <canvas id="productosMasVendidos"></canvas>
        </div>
    </div>

    {{-- Tabla de pedidos --}}
    
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="py-2 px-4 text-left">ID Pedido</th>
                    <th class="py-2 px-4 text-left">Cliente</th>
                    <th class="py-2 px-4 text-left">Productos</th>
                    <th class="py-2 px-4 text-left">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedidos as $pedido)
                <tr class="border-b hover:bg-green-50">
                    <td class="py-2 px-4">{{ $pedido->id }}</td>
                    <td class="py-2 px-4">{{ $pedido->customer_name }}</td>
                    <td class="py-2 px-4 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($pedido->items as $item)
                                <li>{{ $item->quantity }}x {{ $item->producto->nombre ?? 'Producto' }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="py-2 px-4 text-green-600 font-bold">${{ number_format($pedido->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Gráficas --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ventasPorDia = @json( isset($ventasPorDia) ? $ventasPorDia->toArray() : [] );
    const productosMasVendidos = @json( isset($productosMasVendidos) ? $productosMasVendidos->toArray() : [] );

    console.log('ventasPorDia', ventasPorDia);
    console.log('productosMasVendidos', productosMasVendidos);

    const ctx1 = document.getElementById('ventasPorDia').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ventasPorDia.map(v => v.fecha),
            datasets: [{
                label: 'Ventas ($)',
                data: ventasPorDia.map(v => v.total),
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,0.2)',
                fill: true,
                tension: 0.3
            }]
        }
    });

    const ctx2 = document.getElementById('productosMasVendidos').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: productosMasVendidos.map(p => p.nombre),
            datasets: [{
                label: 'Cantidad vendida',
                data: productosMasVendidos.map(p => p.cantidad),
                backgroundColor: '#16a34a'
            }]
        }
    });
});
</script>


</body>
</html>

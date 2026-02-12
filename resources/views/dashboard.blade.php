<x-app-layout>
    <x-slot name="header">
       <div class="flex justify-between items-center mb-4">
    <h1 class="text-3xl font-bold text-green-700">Dashboard de Ventas</h1>
    <a href="{{ route('dashboard.exportar') }}" 
       class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Exportar a Excel
    </a>
</div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-sales :pedidos="$pedidos"
                         :totalVentas="$totalVentas"
                         :productosMasVendidos="$productosMasVendidos"
                         :ventasPorDia="$ventasPorDia"
                         :productoMasVendido="$productoMasVendido"
                         :metodosPago="$metodosPago"
                         :totalPagado="$totalPagado"
                         :totalSinPagar="$totalSinPagar"
                         :pedidosSinPagar="$pedidosSinPagar"/>     

            </div>
        </div>
    </div>
</x-app-layout>

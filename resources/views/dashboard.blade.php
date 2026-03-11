<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <h1 class="text-3xl font-bold text-green-700">Dashboard de Ventas</h1>
            <div class="flex flex-wrap items-center gap-3">
                {{-- Filtro de fechas --}}
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1.5">
                        <label class="text-sm text-gray-600 whitespace-nowrap">Desde:</label>
                        <input type="date" name="desde" value="{{ $desde }}"
                               class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="flex items-center gap-1.5">
                        <label class="text-sm text-gray-600 whitespace-nowrap">Hasta:</label>
                        <input type="date" name="hasta" value="{{ $hasta }}"
                               class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <button type="submit"
                            class="px-4 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition">
                        Filtrar
                    </button>
                </form>
                {{-- Exportar Excel (pasa las mismas fechas) --}}
                <a href="{{ route('dashboard.exportar', ['desde' => $desde, 'hasta' => $hasta]) }}"
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-1.5 px-4 rounded-lg inline-flex items-center text-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exportar Excel
                </a>
            </div>
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

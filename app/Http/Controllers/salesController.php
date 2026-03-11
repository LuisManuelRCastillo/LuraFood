<?php

namespace App\Http\Controllers;
use App\Models\Pedido as sales;
use App\Exports\PedidosExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class salesController extends Controller
{
   
    public function index(Request $request)
    {
        $desde = $request->query('desde', \Carbon\Carbon::now()->startOfMonth()->toDateString());
        $hasta = $request->query('hasta', \Carbon\Carbon::now()->toDateString());

        $pedidos = Sales::with('items.producto')
            ->where('status', 'delivered')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->get();

        $totalVentas = $pedidos->sum('total');

        $ventasPorDia = $pedidos->groupBy(fn($p) => \Carbon\Carbon::parse($p->created_at)->format('Y-m-d'))
            ->map(fn($dia) => [
                'fecha' => \Carbon\Carbon::parse($dia->first()->created_at)->format('d M'),
                'total' => $dia->sum('total')
            ])->values();

        $productosMasVendidos = $pedidos->flatMap->items
            ->groupBy(fn($i) => $i->producto->nombre ?? 'Producto')
            ->map(fn($g) => [
                'nombre' => $g->first()->producto->nombre ?? 'Producto',
                'cantidad' => $g->sum('quantity')
            ])->values()->sortByDesc('cantidad')->take(5)->values();

        $productoMasVendido = $productosMasVendidos->first()['nombre'] ?? null;

        // Metodos de pago (solo pedidos pagados)
        $pedidosPagados = $pedidos->where('payment_status', 'paid');
        $metodosPago = $pedidosPagados->groupBy('payment_method')
            ->map(fn($grupo, $metodo) => [
                'metodo' => match($metodo) {
                    'cash' => 'Efectivo',
                    'card' => 'Tarjeta',
                    'transfer' => 'Transferencia',
                    default => $metodo ?? 'Sin registro',
                },
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('total'),
            ])->values();

        $totalPagado = $pedidosPagados->sum('total');
        $totalSinPagar = $pedidos->where('payment_status', '!=', 'paid')->sum('total');

        // Pedidos pendientes de pago
        $pedidosSinPagar = $pedidos->where('payment_status', '!=', 'paid')->count();

        return view('dashboard', compact(
            'pedidos', 'totalVentas', 'ventasPorDia', 'productosMasVendidos', 'productoMasVendido',
            'metodosPago', 'totalPagado', 'totalSinPagar', 'pedidosSinPagar', 'desde', 'hasta'
        ));
    }
    public function exportarExcel(Request $request)
    {
        $desde = $request->query('desde', \Carbon\Carbon::now()->startOfMonth()->toDateString());
        $hasta = $request->query('hasta', \Carbon\Carbon::now()->toDateString());
        return Excel::download(new PedidosExport($desde, $hasta), 'pedidos-' . $desde . '_' . $hasta . '.xlsx');
    }

}

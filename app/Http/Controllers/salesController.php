<?php

namespace App\Http\Controllers;
use App\Models\Pedido as sales;
use App\Exports\PedidosExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class salesController extends Controller
{
   
    public function index()
{
    $pedidos = Sales::with('items.producto')
        ->where('status', 'delivered')
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
    // dd(['ventasPorDia' => $ventasPorDia, 'productosMasVendidos' => $productosMasVendidos]);
    return view('dashboard', compact(
        'pedidos', 'totalVentas', 'ventasPorDia', 'productosMasVendidos', 'productoMasVendido'
    ));
}
 public function exportarExcel()
    {
        return Excel::download(new PedidosExport, 'pedidos-' . date('Y-m-d') . '.xlsx');
    }

}

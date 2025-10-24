<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\salesController;
use App\Http\Controllers\QrController;


Route::get('/', function (Illuminate\Http\Request $request) {

    if($request->has('mesa')) {
        session(['mesa' => $request->mesa]);
    }
    return view('welcome');
});

// Ruta del dashboard usando el salesController
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->get('/dashboard', [salesController::class, 'index'])
    ->name('dashboard');

Route::get('/categorias', [CategoryController::class, 'index'])->name('categorias.index');
Route::get('/productos/{id}', [ProductsController::class, 'index'])->name('productos.index');
Route::get('/carrito', [PedidoController::class, 'carrito'])->name('pedidos.carrito');
Route::post('/agregar/{productId}', [PedidoController::class, 'agregar'])->name('pedidos.agregar');
Route::get('/eliminar/{productId}', [PedidoController::class, 'eliminar'])->name('pedidos.eliminar');

Route::get('/cliente', [PedidoController::class, 'registroCliente'])->name('pedidos.cliente');
Route::post('/cliente', [PedidoController::class, 'guardarCliente'])->name('pedidos.guardarCliente');

Route::get('/resumen', [PedidoController::class, 'resumen'])->name('pedidos.resumen');
Route::post('/resumen', [PedidoController::class, 'finalizar'])->name('pedidos.finalizar');

Route::post('carrito/mas/{key}',[PedidoController::class, 'sumar'])->name('pedido.mas');
Route::post('carrito/menos/{key}',[PedidoController::class, 'restar'])->name('pedido.menos');
Route::get('/carrito/contenido', function (Illuminate\Http\Request $request) {
    if ($request->has('count')) {
        return response()->json([
            'count' => count(session('pedido', []))
        ]);
    }
    return view('components.cart');
})->name('carrito.contenido');

Route::get('/ordenes-pendientes', [PedidoController::class, 'pendientes'])->name('pedidos.pendientes');
Route::get('/api/ordenes-pendientes', [PedidoController::class, 'apiPendientes'])->name('api.ordenes.pendientes');
Route::post('/pedidos/{id}/deliver', [PedidoController::class, 'deliver']);
Route::get('/dashboard/exportar-excel', [salesController::class, 'exportarExcel'])
    ->name('dashboard.exportar');


Route::get('/qr',[QrController::class, 'index'])->name('qr');
 

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\salesController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ProductoController;


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
Route::get('/carrito', [PedidoController::class, 'cart'])->name('pedidos.carrito');
Route::post('/confirmar-pedido', [PedidoController::class, 'confirmarRapido'])->name('pedidos.confirmar-rapido');
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

Route::get('/ordenes-confirmadas', function() { return view('Orders.orderConfirmed'); })->name('ordenes.confirmadas');

// PIN de acceso staff
Route::get('/staff/pin', [StaffAuthController::class, 'showPin'])->name('staff.pin');
Route::post('/staff/pin', [StaffAuthController::class, 'verifyPin'])->name('staff.pin.verify');
Route::post('/staff/logout', [StaffAuthController::class, 'logout'])->name('staff.logout');

// Rutas de staff protegidas con PIN
Route::middleware('staff.pin')->group(function () {
    Route::get('/ordenes-pendientes', [PedidoController::class, 'pendientes'])->name('pedidos.pendientes');
    Route::post('/pedidos/{id}/deliver', [PedidoController::class, 'deliver']);
    Route::post('/pedidos/{id}/marcar-pagado', [PedidoController::class, 'marcarPagado'])->name('pedidos.marcar-pagado');
    Route::get('/api/ordenes-pendientes', [PedidoController::class, 'apiPendientes'])->name('api.ordenes.pendientes');
    Route::get('/api/pagos-pendientes', [PedidoController::class, 'apiPagosPendientes'])->name('api.pagos.pendientes');
    Route::get('/api/historial', [PedidoController::class, 'apiHistorial'])->name('api.historial');
});

// Rutas de admin (requieren login - usan layout auth)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/pagos-pendientes', [PedidoController::class, 'pagosPendientes'])->name('pagos.pendientes');
    Route::get('/dashboard/exportar-excel', [salesController::class, 'exportarExcel'])->name('dashboard.exportar');
    Route::get('/qr', [QrController::class, 'index'])->name('qr');

    // CRUD Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('categorias', CategoriaController::class);
        Route::resource('productos', ProductoController::class);
    });
});

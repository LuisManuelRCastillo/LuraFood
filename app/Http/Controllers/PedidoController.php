<?php
namespace App\Http\Controllers;

use App\Models\Pedido as Pedido;
use App\Models\ProductsModel as Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PedidoConfirmacion;

class PedidoController extends Controller
{
    //
    public function cart()
    {
        $pedido = session('pedido', []);
        return view('Cart.cartIndex', compact('pedido'));
    }

    
    public function agregar(Request $request, $productId)
    {
        $producto = Product::findOrFail($productId);
        $tamano   = $request->input('tamano', 'mediano'); 
        if($producto->id_cat == 3){
            $tamano = $request->input('tamano', null);
        }
        $leche    = $request->input('leche', null);
        $extras   = (array) $request->input('extras', []);
        sort($extras);

      
        $precioBase = $producto->precio;
        switch ($tamano) {
            case 'chico':
                $precioBase -= 5; 
                break;
            case 'grande':
                $precioBase += 10;
                break;
        }

        // Clave única
        $key = implode('|', [
            $productId,
            $tamano,
            $leche ?: '-',
            implode('+', $extras),
        ]);

        $pedido = session('pedido', []);

        if (isset($pedido[$key])) {
            $pedido[$key]['quantity']++;
            $pedido[$key]['subtotal'] = $pedido[$key]['quantity'] * $pedido[$key]['precio'];
        } else {
            $pedido[$key] = [
                'product_id' => $productId,
                'nombre'     => $producto->nombre,
                'tamano'     => $tamano,
                'quantity'   => 1,
                'precio'     => $precioBase,
                'subtotal'   => $precioBase,
                'leche'      => $leche,
                'extras'     => $extras,
            ];
        }

        session(['pedido' => $pedido]);
        if ($request->ajax()) {
            return response()->json([
                'html'  => view('components.cart')->render(),
                'count' => count(session('pedido', []))
            ]);
        }

        return back()->with('success', 'Producto agregado al cart');
    }
    public function sumar($key)
    {
        $pedido = session('pedido', []);
        if (isset($pedido[$key])) {
            $pedido[$key]['quantity']++;
            $pedido[$key]['subtotal'] = $pedido[$key]['quantity'] * $pedido[$key]['precio'];
            session(['pedido' => $pedido]);
        }
        if ($request->ajax()) {
            return response()->json([
                'html'  => view('components.cart')->render(),
                'count' => count(session('pedido', []))
            ]);
        }
        return redirect()->back();
    }
    public function restar($key)
    {
        $pedido = session('pedido', []);
        if (isset($pedido[$key])) {
            if ($pedido[$key]['quantity'] > 1) {
                $pedido[$key]['quantity']--;
                $pedido[$key]['subtotal'] = $pedido[$key]['quantity'] * $pedido[$key]['precio'];
            } else {
                unset($pedido[$key]);
            }
            session(['pedido' => $pedido]);
        }
        if ($request->ajax()) {
            return response()->json([
                'html'  => view('components.cart')->render(),
                'count' => count(session('pedido', []))
            ]);
        }
        return redirect()->back();
    }
    public function eliminar($productId)
    {
        $pedido = session('pedido', []);
        $pedido = array_filter($pedido, fn($item) => $item['product_id'] != $productId);
        session(['pedido' => $pedido]);
        if ($request->ajax()) {
            return response()->json([
                'html'  => view('components.cart')->render(),
                'count' => count(session('pedido', []))
            ]);
        }
        return redirect()->back();
    }

    // Registro del cliente
    public function registroCliente()
    {
        return view('Cart.clientRegister');
    }

    public function guardarCliente(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email'  => 'required|email|max:255',
        ]);

        session(['cliente' => $request->only('nombre', 'email')]);

        return redirect()->route('pedidos.resumen');
    }

    // Resumen del pedido
    public function resumen()
    {
        $pedido  = session('pedido', []);
        $cliente = session('cliente', []);
        return view('Orders.resumeOrder', compact('pedido', 'cliente'));
    }

public function finalizar(Request $request)
{
    $pedidoSesion = session('pedido', []);
    $cliente      = session('cliente', []);

    if (!$pedidoSesion || !$cliente) {
        return redirect('/')->with('error', 'No hay pedido para procesar');
    }

    try {
        // Crear el pedido en la base de datos
        $pedido = Pedido::create([
            'customer_name'  => $cliente['nombre'],
            'customer_email' => $cliente['email'],
            'status'         => 'pending',
            'total'          => array_sum(array_column($pedidoSesion, 'subtotal')),
        ]);

        Log::info('Pedido creado con ID: ' . $pedido->id);

        foreach ($pedidoSesion as $item) {
            $pedido->items()->create([
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'subtotal'   => $item['subtotal'],
                'tamano'     => $item['tamano'] ?? null,
                'leche'      => $item['leche'] ?? null,
                'extras'     => isset($item['extras']) && !empty($item['extras']) ? json_encode($item['extras']) : null,
            ]);
        }

        Log::info('Items del pedido guardados correctamente');

        // Generar PDF y enviar correo
        try {
            Log::info('Iniciando generación de PDF...');
            
            $pdf = Pdf::loadView('Orders.orderPdf', [
                'cliente' => $cliente,
                'pedido' => $pedidoSesion,
                'numeroPedido' => $pedido->id
            ]);

            Log::info('PDF generado correctamente');

            // Enviar correo con PDF adjunto
            Log::info('Intentando enviar correo a: ' . $cliente['email']);
            
            Mail::to($cliente['email'])->send(
                new PedidoConfirmacion($cliente, $pedidoSesion, $pdf, $pedido->id)
            );

            Log::info('Correo enviado correctamente');

            $mensaje = '¡Pedido finalizado! Te hemos enviado un correo con los detalles.';
            
        } catch (\Exception $e) {
            Log::error('Error al enviar correo: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            $mensaje = 'Pedido finalizado. Error al enviar correo: ' . $e->getMessage();
        }

        // Limpiar sesión
        session()->forget(['pedido', 'cliente']);

        return redirect('/')->with('success', $mensaje);

    } catch (\Exception $e) {
        Log::error('Error general al finalizar pedido: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
    public function pendientes()
    {
        return view('Orders.pendingOrder');
    }

    public function apiPendientes()
    {
         $pedidos = Pedido::with('items.producto')
        ->where('status', 'pending')
        ->latest()
        ->get();
         $mesa = session('mesa');

   
    $pedidos->transform(function($pedido) use ($mesa) {
        $pedido->mesa = $mesa; 
        return $pedido;
    });
        
    return response()->json($pedidos);
    }
    public function deliver($id)
{
    $pedido = Pedido::findOrFail($id);
    $pedido->status = 'delivered';
    $pedido->save();

    return response()->json(['success' => true, 'message' => 'Pedido marcado como entregado']);
}

}
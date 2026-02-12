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

        $mesa = session('mesa', null);

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
                'mesa'		 => $mesa,
       
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

    // Nuevo: Confirmación rápida sin registro obligatorio
    public function confirmarRapido(Request $request)
    {
        $pedidoSesion = session('pedido', []);
        $mesa = session('mesa');

        if (!$pedidoSesion) {
            return redirect('/')->with('error', 'No hay pedido para procesar');
        }

        try {
            // Crear el pedido sin requerir cliente
            $nombre = $request->input('nombre') ?: 'Cliente Anónimo';
            $email = $request->input('email') ?: null;

            $total = array_sum(array_column($pedidoSesion, 'subtotal'));

            $pedido = Pedido::create([
                'customer_name'  => $nombre,
                'customer_email' => $email,
                'status'         => 'pending',
                'total'          => $total,
                'mesa'           => $mesa,
            ]);

            Log::info("Pedido #$pedido->id creado - Cliente: $nombre - Total: $total");

            // Agregar items
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

            // Enviar correo si hay email
            if ($email) {
                try {
                    $pdf = Pdf::loadView('Orders.orderPdf', [
                        'cliente' => ['nombre' => $nombre, 'email' => $email],
                        'pedido' => $pedidoSesion,
                        'numeroPedido' => $pedido->id
                    ]);

                    Mail::to($email)->send(
                        new PedidoConfirmacion(['nombre' => $nombre, 'email' => $email], $pedidoSesion, $pdf, $pedido->id)
                    );
                    
                    Log::info("Correo de confirmación enviado a $email");
                } catch (\Exception $e) {
                    Log::warning("No se pudo enviar correo a $email: " . $e->getMessage());
                }
            }

            // Limpiar sesión
            session()->forget(['pedido', 'cliente', 'mesa']);

            return redirect('/ordenes-confirmadas')
                ->with('success', "Pedido #$pedido->id confirmado. Sera preparado en poco tiempo.")
                ->with('pedido_id', $pedido->id);

        } catch (\Exception $e) {
            Log::error("Error al confirmar pedido: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

public function finalizar(Request $request)
{
    $pedidoSesion = session('pedido', []);
    $cliente      = session('cliente', []);
    $mesa = session('mesa');

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
            'mesa'			 => $mesa,
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
        session()->forget(['pedido', 'cliente','mesa']);

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
        
    return response()->json($pedidos);
    }
    public function deliver($id)
{
    $pedido = Pedido::findOrFail($id);
    $pedido->status = 'delivered';
    $pedido->save();

    return response()->json(['success' => true, 'message' => 'Pedido marcado como entregado']);
}

    // Panel de pagos pendientes - Lista de pedidos entregados sin pagar
    public function pagosPendientes()
    {
        $pedidosPendientes = Pedido::with('items.producto')
            ->where('status', 'delivered')
            ->where('payment_status', '!=', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPendiente = $pedidosPendientes->sum('total');

        return view('Orders.pagosPendientes', compact('pedidosPendientes', 'totalPendiente'));
    }

    // API: pedidos entregados sin pagar (para AJAX)
    public function apiPagosPendientes()
    {
        $pedidos = Pedido::with('items.producto')
            ->where('status', 'delivered')
            ->where('payment_status', '!=', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pedidos);
    }

    // Marcar un pedido como pagado
    public function marcarPagado(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->payment_status = 'paid';
        $pedido->payment_method = $request->input('payment_method', 'cash');
        $pedido->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pago registrado']);
        }

        return redirect()->back()->with('success', "Pago del pedido #$id registrado");
    }
}
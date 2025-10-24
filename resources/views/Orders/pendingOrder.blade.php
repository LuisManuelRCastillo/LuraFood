
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes pendientes</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-green-700">Órdenes pendientes</h1>
    <div id="lista-pedidos" class="space-y-4"></div>
</div>

<script>
async function cargarPedidos() {
    const res = await fetch("{{ route('api.ordenes.pendientes') }}");
    const pedidos = await res.json();

    const cont = document.getElementById("lista-pedidos");
     if (pedidos.length === 0) {
        cont.innerHTML = `
            <div class="text-center py-10">
                <p class="text-gray-500 text-lg">No hay pedidos pendientes ☕</p>
            </div>`;
        return;
    }

    cont.innerHTML = pedidos.map(p => `
    <div class="space-y-2">
        <div class="border p-4 rounded-lg shadow bg-white">
            <h2 class="font-bold text-lg text-green-600">Pedido #${p.id} - Mesa: ${p.mesa}</h2>
            <p><strong>Cliente:</strong> ${p.customer_name ?? 'N/A'}</p>
            <p><strong>Total:</strong> $${p.total}</p>
            <ul class="mt-2 text-sm list-disc list-inside">
                 ${p.items.map(i => `
                        <li class="border-l-4 border-green-500 pl-3 py-2 bg-gray-50">
                            <p class="font-semibold text-gray-800">
                                ${i.quantity}x ${i.producto?.nombre ?? 'Producto'}
                            </p>
                            
                            ${i.tamano ? `
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>Tamaño:</strong> ${i.tamano.charAt(0).toUpperCase() + i.tamano.slice(1)}
                                </p>
                            ` : ''}
                            
                            ${i.leche ? `
                                <p class="text-sm text-gray-600">
                                    <strong>Leche:</strong> ${i.leche.charAt(0).toUpperCase() + i.leche.slice(1)}
                                </p>
                            ` : ''}
                            
                            ${i.extras && i.extras.length > 0 ? `
    <p class="text-sm text-gray-600">
        <strong>Extras:</strong> ${(typeof i.extras === 'string' ? JSON.parse(i.extras) : i.extras).map(e => e.charAt(0).toUpperCase() + e.slice(1)).join(', ')}
    </p>
` : ''}
                        </li>
                    `).join("")}
            </ul>
        </div>
         <button onclick="marcarEntregado(${p.id})"
                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Marcar como entregado
            </button>
    </div>
    `).join("");
}
async function marcarEntregado(pedidoId) {
    const res = await fetch(`/pedidos/${pedidoId}/deliver`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    });

    if (res.ok) {
        cargarPedidos(); 
    } else {
        alert("No se pudo actualizar el estado");
    }
}
// refresca cada 5 segundos
setInterval(cargarPedidos, 5000);
cargarPedidos();
</script>
</body>
</html>
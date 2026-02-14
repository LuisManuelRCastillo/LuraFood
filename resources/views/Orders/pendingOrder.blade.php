
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes pendientes</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('faviconn.ico') }}">
    @vite(['resources/css/app.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">

<div class="max-w-4xl mx-auto p-4 sm:p-6">
    {{-- Tabs de navegación --}}
    <div class="flex border-b border-gray-300 mb-6">
        <button id="tabPendientes" onclick="cambiarTab('pendientes')"
                class="flex-1 sm:flex-none px-4 sm:px-6 py-3 font-bold text-green-700 border-b-2 border-green-600 transition text-sm sm:text-base">
            En Preparación
        </button>
        <button id="tabPagos" onclick="cambiarTab('pagos')"
                class="flex-1 sm:flex-none px-4 sm:px-6 py-3 font-bold text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition text-sm sm:text-base">
            Pagos Pendientes (<span id="pagosCount">0</span>)
        </button>
    </div>

    {{-- Sección: Órdenes en preparación --}}
    <div id="seccionPendientes">
        <h1 class="text-xl sm:text-2xl font-bold mb-6 text-green-700">Órdenes pendientes</h1>
        <div id="lista-pedidos" class="space-y-4"></div>
    </div>

    {{-- Sección: Pagos pendientes --}}
    <div id="seccionPagos" class="hidden">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-green-700">Pagos Pendientes</h1>
            <div class="text-right">
                <p class="text-gray-600 text-xs sm:text-sm">Total pendiente</p>
                <p id="totalPendiente" class="text-2xl sm:text-3xl font-bold text-red-600">$0</p>
            </div>
        </div>
        <div id="lista-pagos" class="space-y-4"></div>
    </div>
</div>

<script>
    const mesaActual = "{{ session('mesa', 'N/A') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ── Tabs ──
    function cambiarTab(tab) {
        const pendientes = document.getElementById('seccionPendientes');
        const pagos = document.getElementById('seccionPagos');
        const btnPend = document.getElementById('tabPendientes');
        const btnPagos = document.getElementById('tabPagos');

        if (tab === 'pendientes') {
            pendientes.classList.remove('hidden');
            pagos.classList.add('hidden');
            btnPend.classList.add('text-green-700', 'border-green-600');
            btnPend.classList.remove('text-gray-500', 'border-transparent');
            btnPagos.classList.remove('text-green-700', 'border-green-600');
            btnPagos.classList.add('text-gray-500', 'border-transparent');
        } else {
            pendientes.classList.add('hidden');
            pagos.classList.remove('hidden');
            btnPagos.classList.add('text-green-700', 'border-green-600');
            btnPagos.classList.remove('text-gray-500', 'border-transparent');
            btnPend.classList.remove('text-green-700', 'border-green-600');
            btnPend.classList.add('text-gray-500', 'border-transparent');
        }
    }

    // ── Órdenes pendientes (en preparación) ──
    async function cargarPedidos() {
        try {
            const res = await fetch("/api/ordenes-pendientes");
            if (!res.ok) throw new Error("Error al cargar pedidos");
            const pedidos = await res.json();

            const cont = document.getElementById("lista-pedidos");
            if (pedidos.length === 0) {
                cont.innerHTML = `
                    <div class="text-center py-10">
                        <p class="text-gray-500 text-lg">No hay pedidos pendientes</p>
                    </div>`;
                return;
            }

            cont.innerHTML = pedidos.map(p => `
            <div class="space-y-2">
                <div class="border p-4 rounded-lg shadow bg-white">
                    <h2 class="font-bold text-lg text-green-600">
                       Pedido #${p.id} - Mesa: ${p.mesa || mesaActual}
                    </h2>
                    <p><strong>Cliente:</strong> ${p.customer_name ?? 'N/A'}</p>
                    <p><strong>Total:</strong> $${p.total}</p>
                    <ul class="mt-2 text-sm list-disc list-inside">
                        ${p.items.map(i => `
                            <li class="border-l-4 border-green-500 pl-3 py-2 bg-gray-50">
                                <p class="font-semibold text-gray-800">
                                    ${i.quantity}x ${i.producto?.nombre ?? 'Producto'}
                                </p>
                                ${i.tamano ? `<p class="text-sm text-gray-600 mt-1"><strong>Tamaño:</strong> ${i.tamano.charAt(0).toUpperCase() + i.tamano.slice(1)}</p>` : ''}
                                ${i.leche ? `<p class="text-sm text-gray-600"><strong>Leche:</strong> ${i.leche.charAt(0).toUpperCase() + i.leche.slice(1)}</p>` : ''}
                                ${i.extras && i.extras.length > 0 ? `<p class="text-sm text-gray-600"><strong>Extras:</strong> ${(typeof i.extras === 'string' ? JSON.parse(i.extras) : i.extras).map(e => e.charAt(0).toUpperCase() + e.slice(1)).join(', ')}</p>` : ''}
                            </li>
                        `).join("")}
                    </ul>
                </div>
                <button onclick="marcarEntregado(${p.id})"
                        class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition font-bold text-base">
                        Marcar como entregado
                </button>
            </div>
            `).join("");

        } catch (error) {
            console.error("Error cargando pedidos:", error);
        }
    }

    async function marcarEntregado(pedidoId) {
        try {
           const res = await fetch(`/pedidos/${pedidoId}/deliver`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                }
            });
           if (res.ok) {
                cargarPedidos();
                cargarPagosPendientes();
           } else {
                const errorText = await res.text();
                console.error("Error al marcar entregado:", errorText);
                alert("No se pudo actualizar el estado");
           }
        } catch (error) {
            console.error("Error en marcarEntregado:", error);
            alert("No se pudo actualizar el estado");
        }
    }

    // ── Pagos pendientes ──
    async function cargarPagosPendientes() {
        try {
            const res = await fetch("/api/pagos-pendientes");
            if (!res.ok) throw new Error("Error al cargar pagos");
            const pedidos = await res.json();

            document.getElementById("pagosCount").innerText = pedidos.length;

            const total = pedidos.reduce((sum, p) => sum + parseFloat(p.total), 0);
            document.getElementById("totalPendiente").innerText = `$${total.toFixed(2)}`;

            const cont = document.getElementById("lista-pagos");
            if (pedidos.length === 0) {
                cont.innerHTML = `
                    <div class="text-center py-10">
                        <p class="text-gray-500 text-lg">No hay pagos pendientes</p>
                    </div>`;
                return;
            }

            cont.innerHTML = pedidos.map(p => `
            <div class="bg-white border-l-4 border-red-500 shadow rounded-lg p-4 sm:p-5">
                <div class="flex justify-between items-start mb-3">
                    <div class="min-w-0 flex-1 pr-3">
                        <h2 class="text-base sm:text-lg font-bold text-gray-800">Pedido #${p.id}</h2>
                        <p class="text-sm text-gray-600 truncate">Cliente: <strong>${p.customer_name ?? 'Anónimo'}</strong></p>
                        ${p.mesa ? `<p class="text-sm text-gray-600">Mesa: ${p.mesa}</p>` : ''}
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xl sm:text-2xl font-bold text-red-600">$${parseFloat(p.total).toFixed(2)}</p>
                        <p class="text-xs text-gray-500">PENDIENTE</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded p-3 mb-4">
                    <p class="font-semibold text-gray-700 text-sm mb-1">Productos:</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        ${p.items.map(i => `
                            <li>${i.quantity}x ${i.producto?.nombre ?? 'Producto'}
                                ${i.tamano ? `- ${i.tamano.charAt(0).toUpperCase() + i.tamano.slice(1)}` : ''}
                                = <strong>$${parseFloat(i.subtotal).toFixed(2)}</strong>
                            </li>
                        `).join("")}
                    </ul>
                </div>

                <p class="font-semibold text-gray-700 text-xs mb-2">Registrar pago:</p>
                <div class="grid grid-cols-3 gap-2">
                    <button onclick="marcarPagado(${p.id}, 'cash')"
                            class="px-2 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 text-sm font-bold transition flex flex-col items-center gap-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Efectivo</span>
                    </button>
                    <button onclick="marcarPagado(${p.id}, 'card')"
                            class="px-2 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 text-sm font-bold transition flex flex-col items-center gap-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Tarjeta</span>
                    </button>
                    <button onclick="marcarPagado(${p.id}, 'transfer')"
                            class="px-2 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 active:bg-purple-800 text-sm font-bold transition flex flex-col items-center gap-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <span>Transfer.</span>
                    </button>
                </div>
            </div>
            `).join("");

        } catch (error) {
            console.error("Error cargando pagos:", error);
        }
    }

    async function marcarPagado(pedidoId, metodo) {
        try {
            const res = await fetch(`/pedidos/${pedidoId}/marcar-pagado`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ payment_method: metodo })
            });
            if (res.ok) {
                cargarPagosPendientes();
            } else {
                alert("No se pudo registrar el pago");
            }
        } catch (error) {
            console.error("Error al marcar pagado:", error);
            alert("No se pudo registrar el pago");
        }
    }

    // Refresca cada 5 segundos ambas secciones
    setInterval(() => {
        cargarPedidos();
        cargarPagosPendientes();
    }, 5000);
    cargarPedidos();
    cargarPagosPendientes();
</script>

</body>
</html>

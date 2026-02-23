
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

    {{-- Modal Efectivo --}}
    <div id="modalEfectivo" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pago en Efectivo</h2>
            <div class="mb-3">
                <p class="text-sm text-gray-600">Total a cobrar:</p>
                <p id="modalTotal" class="text-3xl font-extrabold text-green-600">$0.00</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">¿Con cuánto paga el cliente?</label>
                <input type="number" id="montoPagado" min="0" step="0.01" placeholder="Ej. 200.00"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div id="cambioContainer" class="mb-4 hidden">
                <p class="text-sm text-gray-600">Cambio a devolver:</p>
                <p id="montoCambio" class="text-3xl font-extrabold text-blue-600">$0.00</p>
            </div>
            <div id="cambioError" class="mb-4 hidden">
                <p class="text-sm text-red-600 font-semibold">El monto es insuficiente para cubrir el total.</p>
            </div>
            <div class="flex gap-3 mt-4">
                <button onclick="cerrarModalEfectivo()"
                        class="flex-1 px-4 py-3 bg-gray-200 text-gray-800 rounded-lg font-bold hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button id="btnConfirmarEfectivo" onclick="confirmarPagoEfectivo()"
                        class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition">
                    Confirmar Pago
                </button>
            </div>
        </div>
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
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h2 class="font-bold text-lg text-green-600">Pedido #${p.id}</h2>
                        ${p.para_llevar
                            ? `<span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-1 rounded-full"> Para llevar</span>`
                            : `<span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full">Mesa: ${p.mesa || mesaActual}</span>`
                        }
                    </div>
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
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h2 class="text-base sm:text-lg font-bold text-gray-800">Pedido #${p.id}</h2>
                            ${p.para_llevar
                                ? `<span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full"> Para llevar</span>`
                                : (p.mesa ? `<span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full"> Mesa: ${p.mesa}</span>` : '')
                            }
                        </div>
                        <p class="text-sm text-gray-600 truncate">Cliente: <strong>${p.customer_name ?? 'Anónimo'}</strong></p>
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
                    <button onclick="mostrarModalEfectivo(${p.id}, ${parseFloat(p.total).toFixed(2)})"
                            class="px-2 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 text-sm font-bold transition flex items-center justify-center gap-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Efectivo</span>
                    </button>
                    <button onclick="marcarPagado(${p.id}, 'card')"
                            class="px-2 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 text-sm font-bold transition flex items-center justify-center gap-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Tarjeta</span>
                    </button>
                    <button onclick="marcarPagado(${p.id}, 'transfer')"
                            class="px-2 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 active:bg-purple-800 text-sm font-bold transition flex items-center justify-center gap-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <span>Transf.</span>
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

    // ── Modal Efectivo ──
    let pedidoIdEfectivo = null;
    let totalEfectivo = 0;

    function mostrarModalEfectivo(pedidoId, total) {
        pedidoIdEfectivo = pedidoId;
        totalEfectivo = parseFloat(total);
        document.getElementById('modalTotal').textContent = '$' + totalEfectivo.toFixed(2);
        document.getElementById('montoPagado').value = '';
        document.getElementById('cambioContainer').classList.add('hidden');
        document.getElementById('cambioError').classList.add('hidden');
        document.getElementById('modalEfectivo').classList.remove('hidden');
        setTimeout(() => document.getElementById('montoPagado').focus(), 100);
    }

    function cerrarModalEfectivo() {
        document.getElementById('modalEfectivo').classList.add('hidden');
        pedidoIdEfectivo = null;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('montoPagado').addEventListener('input', function() {
            const pagado = parseFloat(this.value) || 0;
            const cambioContainer = document.getElementById('cambioContainer');
            const cambioError = document.getElementById('cambioError');
            const montoCambio = document.getElementById('montoCambio');
            const btnConfirmar = document.getElementById('btnConfirmarEfectivo');

            if (this.value === '') {
                cambioContainer.classList.add('hidden');
                cambioError.classList.add('hidden');
                return;
            }

            if (pagado >= totalEfectivo) {
                const cambio = pagado - totalEfectivo;
                montoCambio.textContent = '$' + cambio.toFixed(2);
                cambioContainer.classList.remove('hidden');
                cambioError.classList.add('hidden');
                btnConfirmar.disabled = false;
                btnConfirmar.classList.remove('opacity-50');
            } else {
                cambioContainer.classList.add('hidden');
                cambioError.classList.remove('hidden');
                btnConfirmar.disabled = true;
                btnConfirmar.classList.add('opacity-50');
            }
        });

        document.getElementById('modalEfectivo').addEventListener('click', function(e) {
            if (e.target === this) cerrarModalEfectivo();
        });
    });

    async function confirmarPagoEfectivo() {
        if (!pedidoIdEfectivo) return;
        await marcarPagado(pedidoIdEfectivo, 'cash');
        cerrarModalEfectivo();
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


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
    <div class="flex items-end border-b border-gray-300 mb-6">
        <button id="tabPendientes" onclick="cambiarTab('pendientes')"
                class="flex-1 sm:flex-none px-4 sm:px-6 py-3 font-bold text-green-700 border-b-2 border-green-600 transition text-sm sm:text-base">
            En Preparación
        </button>
        <button id="tabPagos" onclick="cambiarTab('pagos')"
                class="flex-1 sm:flex-none px-4 sm:px-6 py-3 font-bold text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition text-sm sm:text-base">
            Pagos Pendientes (<span id="pagosCount">0</span>)
        </button>
        <button id="tabHistorial" onclick="cambiarTab('historial')"
                class="flex-1 sm:flex-none px-4 sm:px-6 py-3 font-bold text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition text-sm sm:text-base">
            Historial
        </button>
        <div class="ml-auto pb-2">
            <form action="{{ route('staff.logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="px-3 py-1.5 text-xs text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Salir
                </button>
            </form>
        </div>
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

    {{-- Sección: Historial --}}
    <div id="seccionHistorial" class="hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-green-700">Historial de Ventas</h1>
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Desde:</label>
                    <input type="date" id="filtroDesde"
                           class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Hasta:</label>
                    <input type="date" id="filtroHasta"
                           class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <button onclick="cargarHistorial()"
                        class="px-4 py-1.5 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition">
                    Filtrar
                </button>
            </div>
        </div>

        {{-- Resumen de ventas --}}
        <div id="resumenHistorial" class="hidden grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Total ventas</p>
                <p id="resTotal" class="text-2xl font-extrabold text-green-600">$0.00</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Efectivo</p>
                <p id="resEfectivo" class="text-xl font-bold text-gray-800">$0.00</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Tarjeta</p>
                <p id="resTarjeta" class="text-xl font-bold text-gray-800">$0.00</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Transferencia</p>
                <p id="resTransferencia" class="text-xl font-bold text-gray-800">$0.00</p>
            </div>
        </div>

        <div id="listaHistorial" class="space-y-3"></div>
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

    // ── Alertas de nuevos pedidos ──
    let pedidosConocidos = new Set();
    let primeraVez = true;

    function reproducirAlerta() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const tiempos = [0, 0.2, 0.4];
            tiempos.forEach(t => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 880;
                osc.type = 'sine';
                gain.gain.setValueAtTime(0.4, ctx.currentTime + t);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + t + 0.15);
                osc.start(ctx.currentTime + t);
                osc.stop(ctx.currentTime + t + 0.15);
            });
        } catch(e) {}
    }

    // ── Tabs ──
    function cambiarTab(tab) {
        const secciones = {
            pendientes: document.getElementById('seccionPendientes'),
            pagos:      document.getElementById('seccionPagos'),
            historial:  document.getElementById('seccionHistorial'),
        };
        const btns = {
            pendientes: document.getElementById('tabPendientes'),
            pagos:      document.getElementById('tabPagos'),
            historial:  document.getElementById('tabHistorial'),
        };

        Object.keys(secciones).forEach(k => {
            secciones[k].classList.toggle('hidden', k !== tab);
            btns[k].classList.toggle('text-green-700', k === tab);
            btns[k].classList.toggle('border-green-600', k === tab);
            btns[k].classList.toggle('text-gray-500', k !== tab);
            btns[k].classList.toggle('border-transparent', k !== tab);
        });

        if (tab === 'historial') cargarHistorial();
    }

    // ── Órdenes pendientes (en preparación) ──
    async function cargarPedidos() {
        try {
            const res = await fetch("/api/ordenes-pendientes");
            if (!res.ok) throw new Error("Error al cargar pedidos");
            const pedidos = await res.json();

            // Detectar pedidos nuevos
            const nuevos = pedidos.filter(p => !pedidosConocidos.has(p.id));
            if (!primeraVez && nuevos.length > 0) {
                reproducirAlerta();
                // Actualizar título de la pestaña
            }
            primeraVez = false;
            pedidos.forEach(p => pedidosConocidos.add(p.id));

            // Badge en tab
            const badge = pedidos.length > 0
                ? `<span class="ml-1 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">${pedidos.length}</span>`
                : '';
            document.getElementById('tabPendientes').innerHTML = `En Preparación${badge}`;

            // Título de la pestaña
            document.title = pedidos.length > 0 ? `(${pedidos.length}) Órdenes pendientes` : 'Órdenes pendientes';

            const cont = document.getElementById("lista-pedidos");
            if (pedidos.length === 0) {
                cont.innerHTML = `
                    <div class="text-center py-10">
                        <p class="text-gray-500 text-lg">No hay pedidos pendientes</p>
                    </div>`;
                return;
            }

            // Agrupar por mesa (para llevar va en su propio grupo)
            const grupos = {};
            pedidos.forEach(p => {
                const key = p.para_llevar ? '__para_llevar__' : (p.mesa || mesaActual || 'Sin mesa');
                if (!grupos[key]) grupos[key] = [];
                grupos[key].push(p);
            });

            // Ordenar: primero mesas, al final "Para llevar"
            const keysOrdenados = Object.keys(grupos).sort((a, b) => {
                if (a === '__para_llevar__') return 1;
                if (b === '__para_llevar__') return -1;
                return a.localeCompare(b, undefined, { numeric: true });
            });

            const renderPedido = p => {
                const esNuevo = nuevos.some(n => n.id === p.id);
                return `
                <div class="space-y-2">
                    <div class="border p-4 rounded-lg shadow bg-white ${esNuevo ? 'ring-2 ring-green-400 animate-pulse' : ''}">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h2 class="font-bold text-lg text-green-600">Pedido #${p.id}</h2>
                        </div>
                        <p><strong>Cliente:</strong> ${p.customer_name ?? 'N/A'}</p>
                        <p><strong>Total:</strong> $${parseFloat(p.total).toFixed(2)}</p>
                        ${p.notas ? `<div class="mt-2 bg-yellow-50 border border-yellow-300 rounded-lg px-3 py-2 text-sm text-yellow-800"><span class="font-bold">Notas:</span> ${p.notas}</div>` : ''}
                        <ul class="mt-2 text-sm list-disc list-inside">
                            ${p.items.map(i => `
                                <li class="border-l-4 border-green-500 pl-3 py-2 bg-gray-50">
                                    <p class="font-semibold text-gray-800">${i.quantity}x ${i.producto?.nombre ?? 'Producto'}</p>
                                    ${i.tamano ? `<p class="text-sm text-gray-600 mt-1"><strong>Tamano:</strong> ${i.tamano.charAt(0).toUpperCase() + i.tamano.slice(1)}</p>` : ''}
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
                </div>`;
            };

            cont.innerHTML = keysOrdenados.map(key => {
                const grupo = grupos[key];
                const esPaLlevar = key === '__para_llevar__';
                const etiqueta = esPaLlevar ? 'Para llevar' : `Mesa ${key}`;
                const totalGrupo = grupo.reduce((s, p) => s + parseFloat(p.total), 0);
                const hayNuevos = grupo.some(p => nuevos.some(n => n.id === p.id));
                const color = esPaLlevar ? 'border-orange-400 text-orange-700 bg-orange-50' : 'border-blue-400 text-blue-700 bg-blue-50';

                const ids = grupo.map(p => p.id);
                return `
                <div class="mb-8">
                    <div class="flex items-center justify-between px-3 py-2 rounded-lg mb-3 ${color} border">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-base">${etiqueta}</span>
                            <span class="text-xs font-semibold opacity-75">${grupo.length} pedido${grupo.length > 1 ? 's' : ''}</span>
                            ${hayNuevos ? `<span class="text-xs font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-700 animate-pulse">Nuevo</span>` : ''}
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold">$${totalGrupo.toFixed(2)}</span>
                            <button onclick="entregarGrupo(${JSON.stringify(ids)})"
                                    class="px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition">
                                Entregar todo
                            </button>
                        </div>
                    </div>
                    <div class="space-y-4 pl-3 border-l-2 ${esPaLlevar ? 'border-orange-300' : 'border-blue-300'}">
                        ${grupo.map(renderPedido).join('')}
                    </div>
                </div>`;
            }).join('');

        } catch (error) {
            console.error("Error cargando pedidos:", error);
        }
    }

    async function entregarGrupo(ids) {
        try {
            const res = await fetch('/pedidos/deliver-grupo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ ids })
            });
            if (res.ok) { cargarPedidos(); cargarPagosPendientes(); }
            else alert('No se pudo entregar el grupo');
        } catch (e) { alert('Error al entregar el grupo'); }
    }

    async function cobrarGrupo(ids, total) {
        pedidoIdEfectivo = ids;
        totalEfectivo = parseFloat(total);
        document.getElementById('modalTotal').textContent = '$' + totalEfectivo.toFixed(2);
        document.getElementById('montoPagado').value = '';
        document.getElementById('cambioContainer').classList.add('hidden');
        document.getElementById('cambioError').classList.add('hidden');
        document.getElementById('modalEfectivo').classList.remove('hidden');
        setTimeout(() => document.getElementById('montoPagado').focus(), 100);
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

            // Agrupar pagos por mesa
            const gruposPago = {};
            pedidos.forEach(p => {
                const key = p.para_llevar ? '__para_llevar__' : (p.mesa || 'Sin mesa');
                if (!gruposPago[key]) gruposPago[key] = [];
                gruposPago[key].push(p);
            });

            const keysOrdenadosPago = Object.keys(gruposPago).sort((a, b) => {
                if (a === '__para_llevar__') return 1;
                if (b === '__para_llevar__') return -1;
                return a.localeCompare(b, undefined, { numeric: true });
            });

            const renderPedidoPago = p => `
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <p class="font-bold text-gray-800">Pedido #${p.id} <span class="font-normal text-sm text-gray-500">· ${p.customer_name ?? 'Anónimo'}</span></p>
                    <p class="font-bold text-red-600">$${parseFloat(p.total).toFixed(2)}</p>
                </div>
                <ul class="text-sm text-gray-600 mb-3 space-y-0.5">
                    ${p.items.map(i => `<li>${i.quantity}x ${i.producto?.nombre ?? 'Producto'}${i.tamano ? ` · ${i.tamano.charAt(0).toUpperCase() + i.tamano.slice(1)}` : ''} = <strong>$${parseFloat(i.subtotal).toFixed(2)}</strong></li>`).join('')}
                </ul>
                <p class="text-xs text-gray-500 mb-1">Pago individual:</p>
                <div class="grid grid-cols-3 gap-1.5">
                    <button onclick="mostrarModalEfectivo(${p.id}, ${parseFloat(p.total).toFixed(2)})"
                            class="py-2 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-green-700 transition">Efectivo</button>
                    <button onclick="marcarPagado(${p.id}, 'card')"
                            class="py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition">Tarjeta</button>
                    <button onclick="marcarPagado(${p.id}, 'transfer')"
                            class="py-2 bg-purple-600 text-white rounded-lg text-xs font-bold hover:bg-purple-700 transition">Transf.</button>
                </div>
            </div>`;

            cont.innerHTML = keysOrdenadosPago.map(key => {
                const grupo = gruposPago[key];
                const esPaLlevar = key === '__para_llevar__';
                const etiqueta = esPaLlevar ? 'Para llevar' : `Mesa ${key}`;
                const totalGrupo = grupo.reduce((s, p) => s + parseFloat(p.total), 0);
                const ids = grupo.map(p => p.id);
                const colorHeader = esPaLlevar
                    ? 'bg-orange-50 border-orange-400 text-orange-700'
                    : 'bg-red-50 border-red-400 text-red-700';

                return `
                <div class="mb-6">
                    <div class="flex items-center justify-between px-3 py-2.5 rounded-lg mb-3 border ${colorHeader}">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold">${etiqueta}</span>
                            <span class="text-xs opacity-75">${grupo.length} pedido${grupo.length > 1 ? 's' : ''}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold">$${totalGrupo.toFixed(2)}</span>
                            ${grupo.length > 1 ? `
                            <button onclick="cobrarGrupo(${JSON.stringify(ids)}, ${totalGrupo.toFixed(2)})"
                                    class="px-2 py-1 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition">
                                Cobrar todo
                            </button>
                            <button onclick="fetch('/pedidos/cobrar-grupo',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({ids:${JSON.stringify(ids)},payment_method:'card'})}).then(()=>cargarPagosPendientes())"
                                    class="px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition">
                                Tarjeta
                            </button>
                            <button onclick="fetch('/pedidos/cobrar-grupo',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({ids:${JSON.stringify(ids)},payment_method:'transfer'})}).then(()=>cargarPagosPendientes())"
                                    class="px-2 py-1 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 transition">
                                Transf.
                            </button>` : ''}
                        </div>
                    </div>
                    <div class="space-y-3 pl-3 border-l-2 ${esPaLlevar ? 'border-orange-300' : 'border-red-300'}">
                        ${grupo.map(renderPedidoPago).join('')}
                    </div>
                </div>`;
            }).join('');

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
        if (Array.isArray(pedidoIdEfectivo)) {
            await fetch('/pedidos/cobrar-grupo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ ids: pedidoIdEfectivo, payment_method: 'cash' })
            });
            cargarPagosPendientes();
        } else {
            await marcarPagado(pedidoIdEfectivo, 'cash');
        }
        cerrarModalEfectivo();
    }

    // ── Historial ──
    function hoy() {
        return new Date().toISOString().split('T')[0];
    }

    async function cargarHistorial() {
        const desde = document.getElementById('filtroDesde').value || hoy();
        const hasta = document.getElementById('filtroHasta').value || hoy();

        const cont = document.getElementById('listaHistorial');
        cont.innerHTML = `<div class="text-center py-8 text-gray-400">Cargando...</div>`;

        try {
            const res = await fetch(`/api/historial?desde=${desde}&hasta=${hasta}`);
            if (!res.ok) throw new Error();
            const { pedidos, resumen } = await res.json();

            // Resumen
            document.getElementById('resTotal').textContent = '$' + parseFloat(resumen.total).toFixed(2);
            document.getElementById('resEfectivo').textContent = '$' + parseFloat(resumen.efectivo).toFixed(2);
            document.getElementById('resTarjeta').textContent = '$' + parseFloat(resumen.tarjeta).toFixed(2);
            document.getElementById('resTransferencia').textContent = '$' + parseFloat(resumen.transferencia).toFixed(2);
            document.getElementById('resumenHistorial').classList.remove('hidden');

            if (pedidos.length === 0) {
                cont.innerHTML = `<div class="text-center py-10"><p class="text-gray-500">Sin ventas en ese período</p></div>`;
                return;
            }

            const metodoBadge = { cash: 'bg-green-100 text-green-700', card: 'bg-blue-100 text-blue-700', transfer: 'bg-purple-100 text-purple-700' };
            const metodoLabel = { cash: 'Efectivo', card: 'Tarjeta', transfer: 'Transferencia' };

            cont.innerHTML = pedidos.map(p => {
                const metodo = p.payment_method || 'cash';
                const badge = metodoBadge[metodo] || 'bg-gray-100 text-gray-700';
                const label = metodoLabel[metodo] || metodo;
                const fecha = new Date(p.created_at).toLocaleString('es-MX', { hour: '2-digit', minute: '2-digit', hour12: true });
                return `
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex justify-between items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="font-bold text-gray-800">Pedido #${p.id}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold ${badge}">${label}</span>
                            ${p.para_llevar ? `<span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full">Para llevar</span>` : ''}
                        </div>
                        <p class="text-sm text-gray-500">${p.customer_name ?? 'Anónimo'} · ${fecha}</p>
                        <p class="text-xs text-gray-400 mt-1">${p.items.map(i => `${i.quantity}x ${i.producto?.nombre ?? '?'}`).join(', ')}</p>
                    </div>
                    <p class="text-lg font-extrabold text-gray-800 flex-shrink-0">$${parseFloat(p.total).toFixed(2)}</p>
                </div>`;
            }).join('');

        } catch (e) {
            cont.innerHTML = `<div class="text-center py-8 text-red-500">Error al cargar historial</div>`;
        }
    }

    // Inicializar fechas del filtro de historial
    document.getElementById('filtroDesde').value = hoy();
    document.getElementById('filtroHasta').value = hoy();

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

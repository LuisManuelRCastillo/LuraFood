<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Menú Coffee</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('faviconn.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto my-10 px-4 flex justify-between items-center">
        <a href="{{ route('categorias.index') }}" class="text-green-600 hover:underline">Volver a categorías</a>
    </div>

    <!-- Botón carrito -->
    <button id="abrirCarrito" 
        class="fixed top-10 right-10 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-green-700 z-50">
        Carrito (<span id="carritoCount">{{ count(session('pedido', [])) }}</span>)
    </button>

    <!-- Productos -->
    <div class="max-w-9xl mx-auto my-10 px-4">
        <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">
            Explora 
            @switch($categoria->id)
                @case(1) nuestras bebidas calientes @break
                @case(2) nuestras bebidas frías @break
                @default nuestros alimentos
            @endswitch
        </h1>
        
        <div class="max-w-7xl mx-auto px-4 mb-6">
            <input id="buscador" 
                   type="text" 
                   placeholder="Buscar producto..." 
                   class="w-full md:w-1/2 mx-auto block border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        </div>

        <div class="overflow-x-auto whitespace-nowrap space-x-4 pb-4 flex">
            @foreach($productos as $producto)
            <div class="tarjeta-producto inline-block align-top bg-white shadow-md rounded-xl w-64 flex-shrink-0 hover:shadow-xl transition-all duration-300">
                <div class="p-4" style="text-align: center; color:#4d4d4d">
                    <div class="flex justify-center mb-3">
                        <img src="{{ asset('assets/img/café.png') }}"
                             alt="{{ $producto->nombre }}"
                             class="w-28 h-28 object-cover rounded-full border-2 border-green-100">
                    </div>
                
                    <h2 class="text-lg font-bold text-gray-800 text-center mb-2">{{ $producto->nombre }}</h2>
                    
                    @if($categoria->id == 1 || $categoria->id == 2)
                        <span style="text-align:center; color:#4d4d4d">{{ $producto->qty }}ml</span>
                    @else
                        <span>{{ $producto->qty }}g</span>
                    @endif
            
                    <p class="text-sm text-gray-600 text-center mb-3 h-12 overflow-hidden text-ellipsis">{{ $producto->descripcion }}</p>
                    <p class="text-lg font-bold text-green-600 text-center mb-3">${{ number_format($producto->precio, 2) }}</p>
            
                    <!-- Formulario para BEBIDAS -->
                    @if($categoria->id == 1 || $categoria->id == 2)
                    <form action="{{ route('pedidos.agregar', $producto->id) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Tamaño:</label>
                            <div class="flex justify-center gap-3">
                                @foreach(['Chico','Mediano','Grande'] as $tamano)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="tamano" value="{{ strtolower($tamano) }}"
                                            class="hidden peer" {{ $loop->first ? 'checked' : '' }}>
                                        <div class="px-3 py-2 border rounded-lg text-sm text-gray-700 bg-white hover:bg-green-100 peer-checked:bg-green-600 peer-checked:text-white shadow-sm">
                                            {{ $tamano }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Leche -->
                        <div class="mb-3">
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Leche:</label>
                            <select name="leche" class="w-full border-gray-300 rounded-lg px-3 py-2">
                                <option value="entera">Entera</option>
                                <option value="deslactosada">Deslactosada</option>
                                <option value="almendra">Almendra</option>
                                <option value="soya">Soya</option>
                            </select>
                        </div>

                        <!-- Extras -->
                        <div class="mb-3">
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Extras:</label>
                            <div class="flex justify-center flex-wrap gap-2">
                                @foreach(['caramelo','vainilla','chocolate'] as $extra)
                                    <label class="flex items-center space-x-1 bg-gray-100 px-2 py-1 rounded-lg cursor-pointer hover:bg-green-100">
                                        <input type="checkbox" name="extras[]" value="{{ $extra }}" class="form-checkbox h-4 w-4 text-green-600">
                                        <span class="text-gray-700 text-sm capitalize">{{ $extra }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Agregar al carrito
                            </button>
                        </div>
                    </form>
                    
                    <!-- Formulario para ALIMENTOS (sin opciones adicionales) -->
                    @else
                    <form action="{{ route('pedidos.agregar', $producto->id) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="text-center mt-3">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Agregar al carrito
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Drawer Carrito -->
    <div id="carritoDrawer" class="fixed top-0 right-0 h-full w-96 bg-white shadow-lg transform translate-x-full transition-transform duration-300 z-40 overflow-y-auto p-6">
        <div class="flex justify-between items-center border-b pb-2 mb-4">
            <h2 class="text-2xl font-bold text-green-700">Tu pedido</h2>
            <button id="cerrarCarrito" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
        </div>
        
        <div id="contenidoCarrito">
            @include('components.cart')
        </div>
    </div>

    <script>
        const abrir = document.getElementById('abrirCarrito');
        const cerrar = document.getElementById('cerrarCarrito');
        const drawer = document.getElementById('carritoDrawer');

        abrir.addEventListener('click', () => {
            drawer.classList.remove('translate-x-full');
            abrir.style.display = 'none'; 
        });

        cerrar.addEventListener('click', () => {
            drawer.classList.add('translate-x-full');
            abrir.style.display = 'block'; 
        });

        const buscador = document.getElementById('buscador');
        const tarjetas = document.querySelectorAll('.tarjeta-producto');

        buscador.addEventListener('input', () => {
            const texto = buscador.value.toLowerCase();
            tarjetas.forEach(card => {
                const nombre = card.querySelector('h2').textContent.toLowerCase();
                const descripcion = card.querySelector('p').textContent.toLowerCase();
                if (nombre.includes(texto) || descripcion.includes(texto)) {
                    card.style.display = 'inline-block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        document.addEventListener("DOMContentLoaded", () => {
            function actualizarCarrito() {
                fetch("{{ route('carrito.contenido') }}")
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById("contenidoCarrito").innerHTML = html;
                        bindEventos();
                    });

                fetch("{{ route('carrito.contenido') }}?count=1")
                    .then(res => res.json())
                    .then(data => {
                        if (data.count !== undefined) {
                            document.getElementById("carritoCount").innerText = data.count;
                        }
                    });
            }

            function bindEventos() {
                document.querySelectorAll(".form-mas, .form-menos").forEach(form => {
                    form.addEventListener("submit", e => {
                        e.preventDefault();
                        fetch(form.action, {
                            method: "POST",
                            body: new FormData(form),
                            headers: { "X-Requested-With": "XMLHttpRequest" }
                        }).then(() => actualizarCarrito());
                    });
                });

                document.querySelectorAll(".btn-eliminar").forEach(btn => {
                    btn.addEventListener("click", e => {
                        e.preventDefault();
                        fetch(btn.href, {
                            method: "GET",
                            headers: { "X-Requested-With": "XMLHttpRequest" }
                        }).then(() => actualizarCarrito());
                    });
                });

                // Interceptar formulario de confirmar pedido
                const formConfirmar = document.querySelector(".form-confirmar-pedido");
                if (formConfirmar) {
                    formConfirmar.addEventListener("submit", e => {
                        e.preventDefault();
                        const btn = formConfirmar.querySelector("button[type='submit']");
                        btn.disabled = true;
                        btn.innerText = "Procesando...";

                        fetch(formConfirmar.action, {
                            method: "POST",
                            body: new FormData(formConfirmar),
                            headers: { "X-Requested-With": "XMLHttpRequest" },
                            redirect: "follow"
                        }).then(res => {
                            // Redirigir a la página de confirmación
                            window.location.href = "/ordenes-confirmadas";
                        }).catch(() => {
                            btn.disabled = false;
                            btn.innerText = "Confirmar Pedido";
                            alert("Error al confirmar el pedido");
                        });
                    });
                }
            }

            bindEventos();
        });
    </script>
</body>
</html>
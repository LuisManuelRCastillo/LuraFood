<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Staff</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('faviconn.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="max-w-sm w-full mx-auto p-4">
        <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">

            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Panel de Staff</h1>
                <p class="text-gray-500 text-sm mt-1">Ingresa el PIN para continuar</p>
            </div>

            @if($errors->has('pin'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                    <p class="text-red-700 text-sm text-center">{{ $errors->first('pin') }}</p>
                </div>
            @endif

            <form action="{{ route('staff.pin.verify') }}" method="POST">
                @csrf
                <div class="mb-5">
                    <input type="password"
                           name="pin"
                           id="pin"
                           inputmode="numeric"
                           maxlength="10"
                           placeholder="••••"
                           autofocus
                           class="w-full text-center text-3xl tracking-widest font-bold px-4 py-4 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
                </div>
                <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-bold rounded-xl transition text-lg">
                    Entrar
                </button>
            </form>

        </div>
    </div>

</body>
</html>

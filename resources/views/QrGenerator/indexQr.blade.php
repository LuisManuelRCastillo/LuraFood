<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-green-700">Genera tu QR</h1>
    </x-slot>

    <div class="max-w-5xl mx-auto py-10">
        <div class="flex flex-col md:flex-row gap-8">
            {{-- Formulario izquierda --}}
            <div class="md:w-1/2 bg-white p-6 rounded-lg shadow">
                <h2 class="text-2xl font-bold mb-6">Generar QR por Mesa</h2>

                <form method="GET" action="{{ route('qr') }}">
                    <label for="mesa" class="block text-gray-700 font-semibold mb-2">Número o nombre de mesa</label>
                    <input type="text" name="mesa" id="mesa" value="{{ old('mesa', $mesa) }}"
                           placeholder="Ejemplo: 1, Terraza, VIP-2..."
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-green-300 mb-4">

                    <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
                        Generar QR
                    </button>
                </form>
            </div>

            {{-- QR derecha --}}
            @if($qr)
                <div class="md:w-1/2 flex flex-col items-center justify-center bg-white p-6 rounded-lg shadow">
                   
                    <div class="inline-block p-4 bg-white rounded-lg shadow mb-4">
                        <img src="data:image/png;base64,{{ $qr }}" alt="QR Mesa {{ $mesa }}">
                    </div>
                    <p class="text-blue-600 text-sm break-all mb-2">
                         <h2 class="text-lg font-bold mb-2">QR para la mesa: {{ $mesa }}</h2>
                    </p>
                    <a href="data:image/png;base64,{{ $qr }}"
                       download="qr-mesa-{{ $mesa }}.png"
                       class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Descargar QR
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

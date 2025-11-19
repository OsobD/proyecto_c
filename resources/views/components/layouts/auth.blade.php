<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Login' }} - EEMQ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/EEMQ@3x.png') }}" alt="Logo EEMQ" class="h-20 w-auto">
            </div>
            <p class="mt-2 text-lg font-bold text-gray-800">
                Sistema de Gestión de Inventario
            </p>
        </div>

        <div class="bg-white shadow-2xl rounded-lg px-8 py-10">
            {{ $slot }}
        </div>

        {{-- Footer con logos institucionales --}}
        <div class="mt-12 bg-white shadow-lg rounded-lg px-6 py-6">
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/%23EstamosTrabajando@3x.png') }}"
                         alt="Estamos Trabajando"
                         class="h-14 sm:h-16 w-auto opacity-90 hover:opacity-100 transition-opacity">
                </div>
                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/Administración 24-28@3x.png') }}"
                         alt="Administración 2024-2028"
                         class="h-14 sm:h-16 w-auto opacity-90 hover:opacity-100 transition-opacity">
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>

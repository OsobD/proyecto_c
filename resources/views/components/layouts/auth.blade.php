<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Autenticación' }} - EEMQ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="text-4xl font-extrabold text-gray-900">
                EEMQ
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Sistema de Gestión de Inventario
            </p>
        </div>

        <div class="bg-white shadow-2xl rounded-lg px-8 py-10">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>
</html>

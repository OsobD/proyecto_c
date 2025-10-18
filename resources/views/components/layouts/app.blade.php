<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EEMQ - Sistema de Inventario</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    {{-- Placeholder for EEMQ Logo --}}
                    <a href="/" class="text-xl font-semibold text-gray-700">EEMQ</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('requisiciones') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('requisiciones') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">Requisiciones</a>
                    <a href="{{ route('devoluciones') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('devoluciones') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">Devoluciones</a>
                    <a href="{{ route('bodegas') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('bodegas') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">Bodegas</a>
                    <a href="{{ route('productos') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('productos') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">Productos</a>
                    <a href="{{ route('compras.nueva') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('compras.nueva') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">Registrar Compra</a>
                    <a href="{{ route('usuarios') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('usuarios') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">Usuarios</a>
                    <a href="{{ route('proveedores') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('proveedores') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">Proveedores</a>
                    <a href="{{ route('bitacora') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('bitacora') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">Bitácora</a>
                    <a href="{{ route('reportes') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('reportes') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">Reportes</a>
                    <a href="{{ route('configuracion') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('configuracion') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">Configuración</a>
                </div>

                <div class="flex items-center">
                    <div class="relative">
                        <button class="flex items-center text-gray-700 focus:outline-none">
                            <span>David Bautista</span>
                            <svg class="h-5 w-5 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8">
        {{ $slot }}
    </main>

    @livewireScripts
    @vite('resources/js/app.js')
</body>
</html>

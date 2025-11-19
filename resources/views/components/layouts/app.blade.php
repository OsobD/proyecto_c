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
                    {{-- Logo de EEMQ --}}
                    <a href="/" class="text-xl font-semibold text-gray-700">EEMQ</a>
                </div>
                <div class="hidden md:flex items-center space-x-4">

                    {{-- Dropdown de Compras --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs(['compras', 'compras.*']) ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }} flex items-center font-bold">
                            Compras
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg z-10">
                            <a href="{{ route('compras') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('compras') && !request()->routeIs('compras.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                Inicio
                            </a>
                            <a href="{{ route('compras.nueva') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('compras.nueva') ? 'bg-gray-100 font-semibold' : '' }}">
                                Nueva Compra
                            </a>
                            <a href="{{ route('compras.historial') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('compras.historial') ? 'bg-gray-100 font-semibold' : '' }}">
                                Historial
                            </a>
                        </div>
                    </div>

                    {{-- Ruta hacia reportes --}}
                    <a href="{{ route('reportes') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('reportes') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }} font-bold">Reportes</a>

                    {{-- Dropdown de Traslados --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs(['traslados', 'traslados.*', 'requisiciones', 'devoluciones']) ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }} flex items-center font-bold">
                            Traslados
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg z-10">
                            <a href="{{ route('traslados') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('traslados') && !request()->routeIs('traslados.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                Inicio
                            </a>
                            <a href="{{ route('requisiciones') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('requisiciones') ? 'bg-gray-100 font-semibold' : '' }}">
                                Requisición
                            </a>
                            <a href="{{ route('devoluciones') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('devoluciones') ? 'bg-gray-100 font-semibold' : '' }}">
                                Devolución
                            </a>
                            <a href="{{ route('traslados.nuevo') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('traslados.nuevo') ? 'bg-gray-100 font-semibold' : '' }}">
                                Nuevo Traslado
                            </a>
                            <a href="{{ route('traslados.historial') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('traslados.historial') ? 'bg-gray-100 font-semibold' : '' }}">
                                Historial
                            </a>
                        </div>
                    </div>

                    {{-- Dropdown de Productos y derivados--}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs(['proveedores','productos', 'productos.*']) ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }} flex items-center font-bold">
                            Catálogo
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg z-10">
                            <a href="{{ route('productos') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('productos') && !request()->routeIs('productos.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                Productos
                            </a>
                            <a href="{{ route('productos.categorias') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('productos.categorias') ? 'bg-gray-100 font-semibold' : '' }}">
                                Categorías
                            </a>
                            <a href="{{ route('proveedores') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('proveedores') ? 'bg-gray-100 font-semibold' : '' }}">
                                Proveedores
                            </a>
                        </div>
                    </div>

                    {{-- Dropdown de gestión de personas, usuarios y tarjetas --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs(['personas', 'usuarios', 'tarjetas.responsabilidad']) ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }} flex items-center font-bold">
                            Colaboradores
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-64 bg-white border border-gray-300 rounded-md shadow-lg z-10">
                             <a href="{{ route('personas') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('personas') ? 'bg-gray-100 font-semibold' : '' }}">
                                Personas
                            </a>
                            <a href="{{ route('usuarios') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('usuarios') ? 'bg-gray-100 font-semibold' : '' }}">
                                Usuarios
                            </a>
                            <a href="{{ route('tarjetas.responsabilidad') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('tarjetas.responsabilidad') ? 'bg-gray-100 font-semibold' : '' }}">
                                Tarjetas de Responsabilidad
                            </a>
                        </div>
                    </div>

                    {{-- Dropdown de Almacenes (Bodegas y Puestos) --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs(['bodegas', 'puestos']) ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }} flex items-center font-bold">
                            Almacenes
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg z-10">
                            <a href="{{ route('bodegas') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('bodegas') ? 'bg-gray-100 font-semibold' : '' }}">
                                Bodegas
                            </a>
                            <a href="{{ route('puestos') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('puestos') ? 'bg-gray-100 font-semibold' : '' }}">
                                Puestos
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('bitacora') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('bitacora') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }} font-bold">Bitácora</a>
                    <a href="{{ route('configuracion') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('configuracion') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }} font-bold">Configuración</a>
                </div>

                <div class="flex items-center">
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center text-gray-700 focus:outline-none hover:text-gray-900">
                            <span>{{ auth()->user()->persona->nombres }} {{ auth()->user()->persona->apellidos }}</span>
                            <svg class="h-5 w-5 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg z-10">
                            <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                <p class="font-semibold">{{ auth()->user()->persona->nombres }} {{ auth()->user()->persona->apellidos }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->persona->correo }}</p>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>

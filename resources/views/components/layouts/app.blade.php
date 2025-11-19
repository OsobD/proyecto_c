<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EEMQ - Sistema de Inventario</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[var(--color-eemq-bg)]">
    <nav class="bg-[var(--color-eemq-primary)] shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    {{-- Logo de EEMQ --}}
                    <a href="/" class="flex items-center">
                        <img src="{{ asset('images/EEMQ@3x.png') }}" alt="Logo EEMQ" class="h-10 w-auto">
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-4">

                    {{-- Dropdown de Compras --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs(['compras', 'compras.*']) ? 'bg-[var(--color-eemq-interactive)] text-white' : 'text-white hover:bg-[var(--color-eemq-primary-dark)]' }} flex items-center font-bold transition-colors">
                            Compras
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-48 bg-white border border-[var(--color-eemq-secondary)] rounded-md shadow-lg z-10">
                            <a href="{{ route('compras') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('compras') && !request()->routeIs('compras.*') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Inicio
                            </a>
                            <a href="{{ route('compras.nueva') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('compras.nueva') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Nueva Compra
                            </a>
                            <a href="{{ route('compras.historial') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('compras.historial') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Historial
                            </a>
                        </div>
                    </div>

                    {{-- Ruta hacia reportes --}}
                    <a href="{{ route('reportes') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('reportes') ? 'bg-[var(--color-eemq-interactive)] text-white' : 'text-white hover:bg-[var(--color-eemq-primary-dark)]' }} font-bold transition-colors">Reportes</a>

                    {{-- Dropdown de Traslados --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs(['traslados', 'traslados.*', 'requisiciones', 'devoluciones']) ? 'bg-[var(--color-eemq-interactive)] text-white' : 'text-white hover:bg-[var(--color-eemq-primary-dark)]' }} flex items-center font-bold transition-colors">
                            Traslados
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-48 bg-white border border-[var(--color-eemq-secondary)] rounded-md shadow-lg z-10">
                            <a href="{{ route('traslados') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('traslados') && !request()->routeIs('traslados.*') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Inicio
                            </a>
                            <a href="{{ route('requisiciones') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('requisiciones') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Requisición
                            </a>
                            <a href="{{ route('devoluciones') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('devoluciones') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Devolución
                            </a>
                            <a href="{{ route('traslados.nuevo') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('traslados.nuevo') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Nuevo Traslado
                            </a>
                            <a href="{{ route('traslados.historial') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('traslados.historial') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Historial
                            </a>
                        </div>
                    </div>

                    {{-- Dropdown de Productos y derivados--}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs(['proveedores','productos', 'productos.*']) ? 'bg-[var(--color-eemq-interactive)] text-white' : 'text-white hover:bg-[var(--color-eemq-primary-dark)]' }} flex items-center font-bold transition-colors">
                            Catálogo
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-48 bg-white border border-[var(--color-eemq-secondary)] rounded-md shadow-lg z-10">
                            <a href="{{ route('productos') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('productos') && !request()->routeIs('productos.*') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Productos
                            </a>
                            <a href="{{ route('productos.categorias') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('productos.categorias') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Categorías
                            </a>
                            <a href="{{ route('proveedores') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('proveedores') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Proveedores
                            </a>
                        </div>
                    </div>

                    {{-- Dropdown de gestión de personas, usuarios y tarjetas --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs(['personas', 'usuarios', 'tarjetas.responsabilidad']) ? 'bg-[var(--color-eemq-interactive)] text-white' : 'text-white hover:bg-[var(--color-eemq-primary-dark)]' }} flex items-center font-bold transition-colors">
                            Colaboradores
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-64 bg-white border border-[var(--color-eemq-secondary)] rounded-md shadow-lg z-10">
                             <a href="{{ route('personas') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('personas') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Personas
                            </a>
                            <a href="{{ route('usuarios') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('usuarios') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Usuarios
                            </a>
                            <a href="{{ route('tarjetas.responsabilidad') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('tarjetas.responsabilidad') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Tarjetas de Responsabilidad
                            </a>
                        </div>
                    </div>

                    {{-- Dropdown de Almacenes (Bodegas y Puestos) --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs(['bodegas', 'puestos']) ? 'bg-[var(--color-eemq-interactive)] text-white' : 'text-white hover:bg-[var(--color-eemq-primary-dark)]' }} flex items-center font-bold transition-colors">
                            Almacenes
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-48 bg-white border border-[var(--color-eemq-secondary)] rounded-md shadow-lg z-10">
                            <a href="{{ route('bodegas') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('bodegas') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Bodegas
                            </a>
                            <a href="{{ route('puestos') }}" class="block px-4 py-2 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors {{ request()->routeIs('puestos') ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : '' }}">
                                Puestos
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('bitacora') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('bitacora') ? 'bg-[var(--color-eemq-interactive)] text-white' : 'text-white hover:bg-[var(--color-eemq-primary-dark)]' }} font-bold transition-colors">Bitácora</a>
                    <a href="{{ route('configuracion') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('configuracion') ? 'bg-[var(--color-eemq-interactive)] text-white' : 'text-white hover:bg-[var(--color-eemq-primary-dark)]' }} font-bold transition-colors">Configuración</a>
                </div>

                <div class="flex items-center">
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center text-white focus:outline-none hover:bg-[var(--color-eemq-primary-dark)] px-3 py-2 rounded-md transition-colors">
                            <span class="font-semibold">{{ auth()->user()->persona->nombres }} {{ auth()->user()->persona->apellidos }}</span>
                            <svg class="h-5 w-5 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute right-0 mt-2 w-56 bg-white border border-[var(--color-eemq-secondary)] rounded-md shadow-lg z-10">
                            <div class="px-4 py-3 text-sm bg-[var(--color-eemq-bg)] border-b border-[var(--color-eemq-secondary)]">
                                <p class="font-semibold text-[var(--color-eemq-primary)]">{{ auth()->user()->persona->nombres }} {{ auth()->user()->persona->apellidos }}</p>
                                <p class="text-xs text-gray-600">{{ auth()->user()->persona->correo }}</p>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-3 text-gray-700 hover:bg-[var(--color-eemq-bg)] transition-colors">
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

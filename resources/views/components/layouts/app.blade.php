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
                    <a href="/" class="flex items-center bg-white px-3 py-2 rounded-md shadow-sm hover:shadow-md transition-shadow">
                        <img src="{{ asset('images/EEMQ@3x.png') }}" alt="Logo EEMQ" class="h-8 w-auto">
                    </a>
                </div>

                {{-- NAVEGACIÓN DINÁMICA BASADA EN PERMISOS --}}
                <div class="hidden md:flex items-center space-x-4">
                    @php
                        $menuItems = \App\Services\NavigationService::simplifyDropdowns(
                            \App\Services\NavigationService::getMenuItems()
                        );
                    @endphp

                    @foreach($menuItems as $item)
                        @if(isset($item['children']) && count($item['children']) > 0)
                            {{-- Dropdown con múltiples opciones --}}
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                <button @click="open = !open" class="px-3 py-2 rounded-md {{ request()->routeIs($item['route_pattern'] ?? []) ? 'bg-[var(--color-eemq-primary-dark)] text-white' : 'text-white hover:bg-[var(--color-eemq-primary-dark)]' }} flex items-center font-bold transition-colors">
                                    {{ $item['label'] }}
                                    <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="open"
                                     x-cloak
                                     x-transition
                                     class="absolute left-0 mt-2 w-64 bg-white border border-[var(--color-eemq-secondary)] rounded-md shadow-lg z-10 overflow-hidden">
                                    @foreach($item['children'] as $child)
                                        <a href="{{ route($child['route']) }}{{ $child['route_param'] ?? '' }}"
                                           class="block px-4 py-2 transition-colors {{ request()->routeIs($child['route']) ? 'bg-[var(--color-eemq-interactive)] text-white font-semibold' : 'text-gray-700 hover:bg-[var(--color-eemq-bg)]' }}">
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            {{-- Link directo (sin dropdown o dropdown simplificado) --}}
                            <a href="{{ route($item['route']) }}{{ $item['route_param'] ?? '' }}"
                               class="px-3 py-2 rounded-md {{ request()->routeIs($item['route_pattern'] ?? $item['route']) ? 'bg-[var(--color-eemq-primary-dark)] text-white' : 'text-white hover:bg-[var(--color-eemq-primary-dark)]' }} font-bold transition-colors">
                                {{ $item['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Menú de usuario --}}
                <div class="flex items-center">
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center justify-center text-white focus:outline-none hover:bg-[var(--color-eemq-primary-dark)] p-2 rounded-md transition-colors">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute right-0 mt-2 w-56 bg-white border border-[var(--color-eemq-secondary)] rounded-md shadow-lg z-10 overflow-hidden">
                            <div class="px-4 py-3 text-sm bg-[var(--color-eemq-bg)] border-b border-[var(--color-eemq-secondary)]">
                                <p class="font-semibold text-[var(--color-eemq-primary)]">{{ auth()->user()->persona->nombres }} {{ auth()->user()->persona->apellidos }}</p>
                                <p class="text-xs text-gray-600">{{ auth()->user()->persona->correo }}</p>
                                @if(auth()->user()->rol)
                                    <p class="text-xs text-blue-600 font-medium mt-1">{{ auth()->user()->rol->nombre }}</p>
                                @endif
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
    @stack('scripts')
</body>
</html>

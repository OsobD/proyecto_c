{{--
    Vista: Detalle de Lote
    Descripción: Muestra todas las ubicaciones (bodegas y tarjetas) donde está un lote específico
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Catálogo', 'url' => '#'],
        ['label' => 'Productos', 'url' => route('productos')],
        ['label' => 'Lote #' . $lote->id],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Ubicaciones: Lote #{{ $lote->id }}</h1>
            <p class="text-gray-600 text-sm mt-1">
                Producto: <span class="font-semibold">{{ $lote->producto->descripcion }}</span> ({{ $lote->producto->id }})
            </p>
        </div>
        <a href="{{ route('productos') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    {{-- Información del Lote --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-blue-600 uppercase font-bold tracking-wider">Cantidad Total</p>
                    <p class="text-2xl font-bold text-blue-800">{{ $lote->cantidad_disponible }}</p>
                    <p class="text-xs text-blue-500 mt-1">de {{ $lote->cantidad_inicial }} iniciales</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-purple-600 uppercase font-bold tracking-wider">En Bodegas</p>
                    <p class="text-2xl font-bold text-purple-800">{{ $cantidadEnBodegas }}</p>
                    <p class="text-xs text-purple-500 mt-1">{{ $totalBodegas }} {{ $totalBodegas == 1 ? 'bodega' : 'bodegas' }}</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>

        @if(!$lote->producto->es_consumible)
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-green-600 uppercase font-bold tracking-wider">En Tarjetas</p>
                    <p class="text-2xl font-bold text-green-800">{{ $cantidadEnTarjetas }}</p>
                    <p class="text-xs text-green-500 mt-1">{{ $totalTarjetas }} {{ $totalTarjetas == 1 ? 'tarjeta' : 'tarjetas' }}</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
            </div>
        </div>
        @endif

        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-indigo-600 uppercase font-bold tracking-wider">Precio Ingreso</p>
                    <p class="text-2xl font-bold text-indigo-800">Q{{ number_format($lote->precio_ingreso, 2) }}</p>
                    <p class="text-xs text-indigo-500 mt-1">{{ \Carbon\Carbon::parse($lote->fecha_ingreso)->format('d/m/Y') }}</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Búsqueda y filtros --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar ubicación</label>
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        placeholder="Buscar bodega o responsable..."
                        autocomplete="off">
                </div>
                <button
                    wire:click="openFilterModal"
                    class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg border-2 border-gray-300 transition-all duration-200 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span class="font-medium">Filtros</span>
                    @if($tipoUbicacion !== '')
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                        </span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Tabla de Ubicaciones --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('nombre')" class="flex items-center gap-1 hover:text-gray-700">
                                Ubicación / Responsable
                                @if($sortField === 'nombre')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('cantidad')" class="flex items-center gap-1 hover:text-gray-700 justify-center w-full">
                                Cantidad
                                @if($sortField === 'cantidad')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ubicaciones as $ubicacion)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ubicacion->tipo === 'bodega')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Bodega
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                        Tarjeta
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $ubicacion->nombre }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $ubicacion->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-lg font-bold {{ $ubicacion->cantidad > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $ubicacion->cantidad }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="mt-2">No se encontraron ubicaciones para este lote.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $ubicaciones->links() }}
        </div>
    </div>

    {{-- Modal de Filtros --}}
    @if($showFilterModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                {{-- Header --}}
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>
                    <button wire:click="closeFilterModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-4 space-y-4">
                    {{-- Tipo de Ubicación --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de ubicación:</label>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="radio" wire:model.live="tipoUbicacion" value="" class="mr-2">
                                <span>Todos</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="radio" wire:model.live="tipoUbicacion" value="bodega" class="mr-2">
                                <span>Solo Bodegas</span>
                            </label>
                            @if(!$lote->producto->es_consumible)
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="radio" wire:model.live="tipoUbicacion" value="tarjeta" class="mr-2">
                                <span>Solo Tarjetas</span>
                            </label>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between p-4 border-t gap-2">
                    <button wire:click="clearFilters" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        Limpiar filtros
                    </button>
                    <button wire:click="closeFilterModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

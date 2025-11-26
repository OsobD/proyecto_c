<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Bodegas', 'url' => route('bodegas')],
        ['label' => $bodega->nombre],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Inventario: {{ $bodega->nombre }}</h1>
            <p class="text-gray-600 text-sm mt-1">Gestión detallada de productos y existencias</p>
        </div>
        <a href="{{ route('bodegas') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Búsqueda y filtros --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar producto</label>
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
                        placeholder="Código o descripción..."
                        autocomplete="off">
                </div>
                <button
                    wire:click="openFilterModal"
                    class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg border-2 border-gray-300 transition-all duration-200 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span class="font-medium">Filtros / Ajustes</span>
                    @if($categoriaId || $estado !== '' || $tipoProducto !== '')
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                        </span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Resumen de Totales --}}
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-blue-900">Resumen de Inventario</h3>
                <p class="text-blue-700 text-sm">Total valorizado de los productos listados</p>
            </div>
            <div class="text-right">
                <span class="block text-xs text-blue-600 uppercase font-bold tracking-wider">Total Precio</span>
                <span class="text-2xl font-bold text-blue-800">Q{{ number_format($totalPrecio, 2) }}</span>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('producto_id')" class="flex items-center gap-1 hover:text-gray-700">
                                Código
                                @if($sortField === 'producto_id')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('producto_descripcion')" class="flex items-center gap-1 hover:text-gray-700">
                                Producto
                                @if($sortField === 'producto_descripcion')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('id')" class="flex items-center gap-1 hover:text-gray-700 justify-center w-full">
                                Lote ID
                                @if($sortField === 'id')
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
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('fecha_ingreso')" class="flex items-center gap-1 hover:text-gray-700 justify-center w-full">
                                Fecha Ingreso
                                @if($sortField === 'fecha_ingreso')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lotes as $lote)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-gray-900">
                                {{ $lote->producto_id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $lote->producto_descripcion }}
                                @if($lote->observaciones)
                                    <p class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ $lote->observaciones }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $lote->categoria_nombre ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    #{{ $lote->id }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                                    {{ $lote->cantidad }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-indigo-600">
                                Q{{ number_format($lote->precio_ingreso, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $lote->fecha_ingreso ? \Carbon\Carbon::parse($lote->fecha_ingreso)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($lote->estado)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                No se encontraron productos que coincidan con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $lotes->links() }}
        </div>
    </div>

    {{-- Modal: Filtros y Ajustes --}}
    <div x-data="{
            show: @entangle('showFilterModal').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeFilterModal"
         wire:ignore.self>
        <div class="relative border w-full max-w-lg shadow-2xl rounded-xl bg-white max-h-[85vh] flex flex-col overflow-hidden"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            
            {{-- Header (Fijo) --}}
            <div class="flex justify-between items-center p-5 border-b border-gray-100 shrink-0">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Filtros y Ajustes
                </h3>
                <button wire:click="closeFilterModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body (Scrollable) --}}
            <div class="p-5 overflow-y-auto flex-1">
                {{-- Sección: Ordenar por --}}
                <div class="mb-6">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Ordenar por</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            'producto_id' => 'Código',
                            'producto_descripcion' => 'Producto',
                            'id' => 'Lote ID',
                            'cantidad' => 'Cantidad',
                            'fecha_ingreso' => 'Fecha'
                        ] as $field => $label)
                            <button
                                wire:click="sortBy('{{ $field }}')"
                                class="flex items-center justify-between px-3 py-2 rounded-lg border {{ $sortField === $field ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }} transition-all text-sm font-medium">
                                <span>{{ $label }}</span>
                                @if($sortField === $field)
                                    <span class="text-xs font-bold">{{ $sortDirection === 'asc' ? 'ASC' : 'DESC' }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Sección: Filtrar por --}}
                <div class="mb-6">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Filtrar por</h4>
                    
                    {{-- Tipo de Producto (Consumible / No Consumible) --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Producto</label>
                        <select wire:model.live="tipoProducto" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Todos</option>
                            <option value="activo">Activos (No Consumibles)</option>
                            <option value="consumible">Consumibles</option>
                        </select>
                    </div>

                    {{-- Categoría --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <select wire:model.live="categoriaId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select wire:model.live="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Todos</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Footer (Fijo) --}}
            <div class="flex justify-between items-center p-5 border-t border-gray-100 bg-gray-50 shrink-0">
                <button
                    type="button"
                    wire:click="clearFilters"
                    class="text-sm text-red-600 hover:text-red-800 font-medium hover:underline">
                    Limpiar filtros
                </button>
                
                <button
                    type="button"
                    wire:click="closeFilterModal"
                    class="bg-gray-900 hover:bg-black text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    Listo
                </button>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Animaciones de entrada */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Animaciones de salida */
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @keyframes slideUp {
            from { transform: translateY(0); opacity: 1; }
            to { transform: translateY(20px); opacity: 0; }
        }
    </style>
</div>

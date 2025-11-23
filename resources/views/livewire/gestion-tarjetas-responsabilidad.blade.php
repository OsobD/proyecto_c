<div>
    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Tarjetas de Responsabilidad</h1>
        <button wire:click="$dispatch('abrirModalPersona')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
            <span>+ Nueva Persona</span>
        </button>
    </div>

    {{-- Mensajes --}}
    @if (session()->has('message'))
        <div class="relative fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg z-50 animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="relative fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg z-50 animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    {{-- Contenedor principal --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Búsqueda y filtros --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar tarjeta</label>
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           wire:model.live="search"
                           class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="Buscar por nombre de persona...">
                </div>
                <button wire:click="openFilterModal"
                        class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg border-2 border-gray-300 transition-all duration-200 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span class="font-medium">Filtros / Ajustes</span>
                </button>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">
                            <button
                                wire:click="sortBy('id')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                ID
                                @if($sortField === 'id')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-left">Persona</th>
                        <th class="py-3 px-6 text-left">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($tarjetas as $tarjeta)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $tarjeta->id }}</td>
                            <td class="py-3 px-6 text-left">
                                @if($tarjeta->persona)
                                    <strong>{{ $tarjeta->persona->nombres }} {{ $tarjeta->persona->apellidos }}</strong>
                                    @if($tarjeta->persona->correo || $tarjeta->persona->telefono)
                                        <div class="text-xs text-gray-500 mt-1">
                                            @if($tarjeta->persona->correo)
                                                <div>{{ $tarjeta->persona->correo }}</div>
                                            @endif
                                            @if($tarjeta->persona->telefono)
                                                <div>{{ $tarjeta->persona->telefono }}</div>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-red-500 text-sm">Sin persona asignada</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left">
                                @if($tarjeta->activo)
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Activa</span>
                                @else
                                    <span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs">Inactiva</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    {{-- Botón para ver productos asignados --}}
                                    <button
                                        wire:click="toggleProductos({{ $tarjeta->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 {{ $tarjetaIdExpandida === $tarjeta->id ? 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        title="Ver productos asignados">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </button>

                                    @if($tarjeta->activo)
                                        <x-action-button
                                            type="delete"
                                            wire:click="confirmDelete({{ $tarjeta->id }})"
                                            title="Desactivar tarjeta" />
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Expansión de productos de la tarjeta (acordeón) --}}
                        @if($tarjetaIdExpandida === $tarjeta->id)
                            <tr>
                                <td colspan="4" class="bg-gray-50 p-6">
                                    <div class="mb-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                Productos asignados a {{ $tarjeta->persona ? $tarjeta->persona->nombres . ' ' . $tarjeta->persona->apellidos : 'Tarjeta #' . $tarjeta->id }}
                                            </h3>
                                        </div>

                                        @php
                                            // Obtener solo los productos NO CONSUMIBLES asignados a esta tarjeta
                                            $tarjetaProductos = $tarjeta->tarjetasProducto()
                                                ->with(['producto', 'lote.bodega'])
                                                ->whereHas('producto', function($query) {
                                                    $query->where('es_consumible', 0);
                                                })
                                                ->get();
                                        @endphp

                                        @if($tarjetaProductos->count() > 0)
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full bg-white border border-gray-300">
                                                    <thead class="bg-indigo-100 text-gray-700 text-sm">
                                                        <tr>
                                                            <th class="py-3 px-4 text-left">Código</th>
                                                            <th class="py-3 px-4 text-left">Producto</th>
                                                            <th class="py-3 px-4 text-center">Lote ID</th>
                                                            <th class="py-3 px-4 text-center">Cant. Asignada</th>
                                                            <th class="py-3 px-4 text-right">Precio Unitario</th>
                                                            <th class="py-3 px-4 text-right">Total Asignado</th>
                                                            <th class="py-3 px-4 text-center">Fecha Ingreso</th>
                                                            <th class="py-3 px-4 text-left">Bodega</th>
                                                            <th class="py-3 px-4 text-center">Estado</th>
                                                            <th class="py-3 px-4 text-left">Observaciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 text-sm">
                                                        @foreach($tarjetaProductos as $tp)
                                                            @php
                                                                $lote = $tp->lote;
                                                                $producto = $tp->producto;

                                                                // Calcular la cantidad asignada
                                                                $cantidadAsignada = 0;
                                                                if ($lote && $lote->precio_ingreso > 0) {
                                                                    $cantidadAsignada = round($tp->precio_asignacion / $lote->precio_ingreso);
                                                                }
                                                            @endphp
                                                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors" wire:key="tp-{{ $tp->id }}">
                                                                <td class="py-3 px-4 text-left">
                                                                    <span class="font-mono text-gray-700 font-semibold">
                                                                        {{ $producto ? $producto->id : 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-left">
                                                                    <span class="font-medium text-gray-800">
                                                                        {{ $producto ? $producto->descripcion : 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-center">
                                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">
                                                                        #{{ $lote ? $lote->id : 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-center">
                                                                    <span class="bg-indigo-100 text-indigo-800 py-1 px-3 rounded-full text-xs font-bold">
                                                                        {{ $cantidadAsignada }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-right">
                                                                    <span class="font-medium text-gray-700">
                                                                        Q{{ $lote ? number_format($lote->precio_ingreso, 2) : '0.00' }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-right">
                                                                    <span class="font-semibold text-indigo-600">
                                                                        Q{{ number_format($tp->precio_asignacion, 2) }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-center text-xs">
                                                                    {{ $lote && $lote->fecha_ingreso ? \Carbon\Carbon::parse($lote->fecha_ingreso)->format('d/m/Y') : 'N/A' }}
                                                                </td>
                                                                <td class="py-3 px-4 text-left">
                                                                    <span class="text-gray-700">
                                                                        {{ $lote && $lote->bodega ? $lote->bodega->nombre : 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-center">
                                                                    @if($lote && $lote->estado)
                                                                        <span class="bg-green-200 text-green-800 py-1 px-2 rounded-full text-xs">Activo</span>
                                                                    @else
                                                                        <span class="bg-red-200 text-red-800 py-1 px-2 rounded-full text-xs">Inactivo</span>
                                                                    @endif
                                                                </td>
                                                                <td class="py-3 px-4 text-left text-xs text-gray-500 max-w-xs truncate">
                                                                    {{ $lote ? ($lote->observaciones ?? '-') : '-' }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- Resumen --}}
                                            <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-gray-700 font-medium">Total de productos asignados:</span>
                                                    <span class="text-2xl font-bold text-indigo-600">{{ $tarjetaProductos->count() }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-8 text-gray-500">
                                                <p>No hay productos asignados a esta tarjeta.</p>
                                                <p class="text-sm mt-2">Los productos aparecerán aquí cuando se realicen asignaciones.</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">
                                @if($mostrarDesactivadas)
                                    No se encontraron tarjetas desactivadas.
                                @else
                                    No se encontraron tarjetas de responsabilidad activas.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $tarjetas->links() }}
        </div>
    </div>

    {{-- Incluir el modal de creación de persona --}}
    @livewire('modal-persona')

    <style>
        /* Ocultar elementos hasta que Alpine.js esté listo */
        [x-cloak] {
            display: none !important;
        }

        /* Animaciones de entrada */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Animaciones de salida */
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @keyframes slideUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(20px);
                opacity: 0;
            }
        }

        /* Animación de mensajes flash */
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>

    {{-- Modal de Filtros / Ajustes --}}
    @if($showFilterModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                {{-- Header --}}
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Filtros / Ajustes</h3>
                    <button wire:click="closeFilterModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-4 space-y-4">
                    {{-- Ordenamiento --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar por:</label>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="radio" wire:model.live="sortField" value="id" class="mr-2">
                                <span>ID</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="radio" wire:model.live="sortField" value="fecha_creacion" class="mr-2">
                                <span>Fecha de Creación</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="radio" wire:model.live="sortField" value="total" class="mr-2">
                                <span>Total</span>
                            </label>
                        </div>
                    </div>

                    {{-- Dirección --}}
                    @if($sortField)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección:</label>
                            <div class="flex gap-2">
                                <label class="flex-1 flex items-center justify-center cursor-pointer hover:bg-gray-50 p-2 rounded border {{ $sortDirection === 'asc' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input type="radio" wire:model.live="sortDirection" value="asc" class="mr-2">
                                    <span>Ascendente ↑</span>
                                </label>
                                <label class="flex-1 flex items-center justify-center cursor-pointer hover:bg-gray-50 p-2 rounded border {{ $sortDirection === 'desc' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input type="radio" wire:model.live="sortDirection" value="desc" class="mr-2">
                                    <span>Descendente ↓</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    {{-- Mostrar inactivas --}}
                    <div class="border-t pt-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="showInactive" class="w-4 h-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm font-medium text-gray-700">Mostrar tarjetas desactivadas</span>
                        </label>
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

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('confirm-delete', () => {
            if (confirm('¿Está seguro de que desea desactivar esta tarjeta de responsabilidad?')) {
                @this.call('delete');
            }
        });
    });
</script>
@endpush

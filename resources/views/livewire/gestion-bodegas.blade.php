{{--
    Vista: Gestión de Bodegas
    Descripción: CRUD de bodegas físicas con visualización y gestión de productos (lotes)
                 por bodega. Permite crear, editar y desactivar bodegas, así como
                 gestionar el inventario (lotes) de cada bodega.
--}}
<div>
    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Bodegas Físicas</h1>
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            + Nueva Bodega
        </button>
    </div>

    {{-- Mensajes --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Contenedor principal --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Búsqueda --}}
        <div class="mb-4">
            <input type="text" wire:model.live="search" class="w-full md:w-1/2 px-4 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Buscar bodega por nombre...">
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Nombre</th>
                        <th class="py-3 px-6 text-left">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($bodegas as $bodega)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $bodega->id }}</td>
                            <td class="py-3 px-6 text-left">
                                <span class="font-medium">{{ $bodega->nombre }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Activa</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    <button
                                        wire:click="toggleProductos({{ $bodega->id }})"
                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                        title="Ver productos de la bodega">
                                        {{ $bodegaIdProductosExpandido === $bodega->id ? '▼ Productos' : '▶ Productos' }}
                                    </button>
                                    <x-action-button
                                        type="edit"
                                        wire:click="edit({{ $bodega->id }})"
                                        title="Editar bodega" />
                                    <x-action-button
                                        type="delete"
                                        wire:click="confirmDelete({{ $bodega->id }})"
                                        title="Desactivar bodega" />
                                </div>
                            </td>
                        </tr>

                        {{-- Expansión de productos de la bodega --}}
                        @if($bodegaIdProductosExpandido === $bodega->id)
                            <tr>
                                <td colspan="4" class="bg-gray-50 p-6">
                                    <div class="mb-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-semibold text-gray-800">Productos en {{ $bodega->nombre }}</h3>
                                            <button
                                                wire:click="abrirModalCrearLote({{ $bodega->id }})"
                                                class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg">
                                                + Agregar Producto/Lote
                                            </button>
                                        </div>

                                        @php
                                            // Obtener los lotes de esta bodega agrupados por producto
                                            $lotes = $bodega->lotes()
                                                ->with(['producto.categoria'])
                                                ->orderBy('fecha_ingreso', 'desc')
                                                ->get();

                                            $productosAgrupados = $lotes->groupBy('id_producto');
                                        @endphp

                                        @if($productosAgrupados->count() > 0)
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full bg-white border border-gray-300">
                                                    <thead class="bg-indigo-100 text-gray-700 text-sm">
                                                        <tr>
                                                            <th class="py-3 px-4 text-left">Código</th>
                                                            <th class="py-3 px-4 text-left">Descripción</th>
                                                            <th class="py-3 px-4 text-left">Categoría</th>
                                                            <th class="py-3 px-4 text-center">Lote ID</th>
                                                            <th class="py-3 px-4 text-center">Cantidad</th>
                                                            <th class="py-3 px-4 text-right">Precio Ingreso</th>
                                                            <th class="py-3 px-4 text-center">Fecha Ingreso</th>
                                                            <th class="py-3 px-4 text-left">Observaciones</th>
                                                            <th class="py-3 px-4 text-center">Estado</th>
                                                            <th class="py-3 px-4 text-center">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 text-sm">
                                                        @foreach($productosAgrupados as $productoId => $lotesProducto)
                                                            @foreach($lotesProducto as $index => $lote)
                                                                <tr class="border-b border-gray-200 hover:bg-gray-50" wire:key="lote-{{ $lote->id }}">
                                                                    {{-- Solo mostrar código y descripción en la primera fila del producto --}}
                                                                    @if($index === 0)
                                                                        <td class="py-3 px-4 font-mono font-semibold" rowspan="{{ $lotesProducto->count() }}">
                                                                            {{ $lote->producto->id }}
                                                                        </td>
                                                                        <td class="py-3 px-4" rowspan="{{ $lotesProducto->count() }}">
                                                                            {{ $lote->producto->descripcion }}
                                                                        </td>
                                                                        <td class="py-3 px-4 text-sm text-gray-500" rowspan="{{ $lotesProducto->count() }}">
                                                                            {{ $lote->producto->categoria->nombre ?? 'Sin categoría' }}
                                                                        </td>
                                                                    @endif

                                                                    <td class="py-3 px-4 text-center">
                                                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">
                                                                            #{{ $lote->id }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="py-3 px-4 text-center">
                                                                        <span class="font-semibold {{ $lote->cantidad > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                            {{ $lote->cantidad }}
                                                                        </span>
                                                                        <span class="text-gray-400 text-xs">/ {{ $lote->cantidad_inicial }}</span>
                                                                    </td>
                                                                    <td class="py-3 px-4 text-right">
                                                                        Q{{ number_format($lote->precio_ingreso, 2) }}
                                                                    </td>
                                                                    <td class="py-3 px-4 text-center text-xs">
                                                                        {{ $lote->fecha_ingreso ? $lote->fecha_ingreso->format('d/m/Y') : 'N/A' }}
                                                                    </td>
                                                                    <td class="py-3 px-4 text-xs text-gray-500 max-w-xs truncate">
                                                                        {{ $lote->observaciones ?? '-' }}
                                                                    </td>
                                                                    <td class="py-3 px-4 text-center">
                                                                        @if($lote->estado)
                                                                            <span class="bg-green-200 text-green-800 py-1 px-2 rounded-full text-xs">Activo</span>
                                                                        @else
                                                                            <span class="bg-red-200 text-red-800 py-1 px-2 rounded-full text-xs">Inactivo</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="py-3 px-4 text-center">
                                                                        <div class="flex items-center justify-center gap-2">
                                                                            <button
                                                                                wire:click="editarLote({{ $lote->id }})"
                                                                                class="text-blue-600 hover:text-blue-800 font-medium text-xs"
                                                                                title="Editar lote">
                                                                                Editar
                                                                            </button>
                                                                            @if($lote->estado)
                                                                                <button
                                                                                    wire:click="eliminarLote({{ $lote->id }})"
                                                                                    class="text-red-600 hover:text-red-800 font-medium text-xs"
                                                                                    title="Desactivar lote">
                                                                                    Desactivar
                                                                                </button>
                                                                            @else
                                                                                <button
                                                                                    wire:click="activarLote({{ $lote->id }})"
                                                                                    class="text-green-600 hover:text-green-800 font-medium text-xs"
                                                                                    title="Activar lote">
                                                                                    Activar
                                                                                </button>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-8 text-gray-500">
                                                <p>No hay productos en esta bodega.</p>
                                                <p class="text-sm mt-2">Haz clic en "Agregar Producto/Lote" para comenzar.</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">No se encontraron bodegas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $bodegas->links() }}
        </div>
    </div>

    {{-- Modal de Bodega --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 flex items-center justify-center"
             x-data
             @click.self="$wire.closeModal()">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="text-xl font-semibold text-gray-800">
                        {{ $editMode ? 'Editar Bodega' : 'Nueva Bodega' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
                </div>

                <form wire:submit.prevent="save" class="mt-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la Bodega <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nombre"
                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nombre') border-red-500 @enderror">
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" wire:click="closeModal"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                            {{ $editMode ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal de Lote (Crear/Editar Producto en Bodega) --}}
    <div x-data="{
            show: @entangle('showModalLote').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalLote">
        <div class="relative p-6 border w-full max-w-2xl shadow-lg rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">
                    {{ $editingLoteId ? 'Editar Lote' : 'Agregar Producto/Lote a Bodega' }}
                </h3>
                <button wire:click="closeModalLote" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="guardarLote">
                {{-- Selección de Producto --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Producto</label>
                    <div class="relative">
                        @if($selectedProducto)
                            <div wire:click="clearProducto" class="flex items-center justify-between mt-1 w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm cursor-pointer hover:border-indigo-400 transition-colors">
                                <div class="flex flex-col gap-0.5 justify-center">
                                    <span class="font-medium">{{ $selectedProducto['descripcion'] }}</span>
                                    <span class="text-xs text-gray-500 mt-0.5">Código: {{ $selectedProducto['codigo'] }} | {{ $selectedProducto['categoria'] }}</span>
                                </div>
                                <span class="text-gray-400 text-xl">⟲</span>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showProductoDropdown').live }" @click.outside="open = false">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchProducto"
                                    @click="open = true"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar producto por código o descripción...">
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                    <ul>
                                        @foreach ($this->productoResults as $producto)
                                            <li wire:click.prevent="selectProducto('{{ $producto['id'] }}')"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center">
                                                <span class="font-mono text-gray-500 mr-2">#{{ $producto['codigo'] }}</span>
                                                <span>{{ $producto['descripcion'] }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('loteProductoId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campos del lote en grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="loteCantidad" class="block text-sm font-medium text-gray-700">Cantidad:</label>
                        <input
                            type="number"
                            id="loteCantidad"
                            wire:model="loteCantidad"
                            min="0"
                            class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm @error('loteCantidad') border-red-500 @enderror"
                            placeholder="Ej: 100">
                        @error('loteCantidad')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lotePrecioIngreso" class="block text-sm font-medium text-gray-700">Precio de Ingreso:</label>
                        <input
                            type="number"
                            step="0.01"
                            id="lotePrecioIngreso"
                            wire:model="lotePrecioIngreso"
                            min="0"
                            class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm @error('lotePrecioIngreso') border-red-500 @enderror"
                            placeholder="Ej: 15.50">
                        @error('lotePrecioIngreso')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="loteFechaIngreso" class="block text-sm font-medium text-gray-700">Fecha de Ingreso:</label>
                        <input
                            type="date"
                            id="loteFechaIngreso"
                            wire:model="loteFechaIngreso"
                            class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm @error('loteFechaIngreso') border-red-500 @enderror">
                        @error('loteFechaIngreso')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="loteObservaciones" class="block text-sm font-medium text-gray-700">Observaciones:</label>
                    <textarea
                        id="loteObservaciones"
                        wire:model="loteObservaciones"
                        rows="3"
                        class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm"
                        placeholder="Observaciones del lote..."></textarea>
                </div>

                <div class="flex justify-between mt-6">
                    <button
                        type="button"
                        wire:click="closeModalLote"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                        {{ $editingLoteId ? 'Actualizar Lote' : 'Crear Lote' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Ocultar elementos hasta que Alpine.js esté listo */
        [x-cloak] {
            display: none !important;
        }

        /* Animaciones de entrada */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
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
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
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
    </style>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('confirm-delete', () => {
            if (confirm('¿Está seguro de que desea desactivar esta bodega?')) {
                @this.call('delete');
            }
        });
    });
</script>
@endpush

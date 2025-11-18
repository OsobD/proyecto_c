{{--
    Vista: Gestión de Productos
    Descripción: Interfaz CRUD para productos del inventario con búsqueda en tiempo real,
                 modal de edición, visualización de lotes en modal dedicado y CRUD completo de lotes
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Catálogo', 'url' => '#'],
        ['label' => 'Productos'],
    ]" />

    {{-- Encabezado con título e información sobre creación de productos --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
            <p class="text-sm text-gray-600 mt-1">
                Administra tu catálogo de productos y sus lotes de inventario
            </p>
        </div>
        <button
            wire:click="abrirModal"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-colors duration-150">
            + Nuevo Producto
        </button>
    </div>

    {{-- Alertas de éxito y error para operaciones CRUD --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Contenedor principal --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Campo de búsqueda con filtrado reactivo --}}
        <div class="mb-6">
            <input
                type="text"
                wire:model.live.debounce.300ms="searchProducto"
                class="w-full md:w-1/2 px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                placeholder="Buscar por código, descripción o categoría...">
        </div>

        {{-- Tabla de listado de productos --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Código</th>
                        <th class="py-3 px-6 text-left">Descripción</th>
                        <th class="py-3 px-6 text-left">Categoría</th>
                        <th class="py-3 px-6 text-center">Tipo</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($productos as $producto)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <span class="font-medium font-mono">{{ $producto->id }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $producto->descripcion }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($producto->es_consumible)
                                    <span class="bg-orange-200 text-orange-700 py-1 px-3 rounded-full text-xs font-semibold">Consumible</span>
                                @else
                                    <span class="bg-purple-200 text-purple-700 py-1 px-3 rounded-full text-xs font-semibold">No Consumible</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($producto->activo)
                                    <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">Activo</span>
                                @else
                                    <span class="bg-red-200 text-red-700 py-1 px-3 rounded-full text-xs font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    {{-- Ver Lotes --}}
                                    <button
                                        wire:click="toggleLotes('{{ $producto->id }}')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1.5 px-3 rounded"
                                        title="Ver lotes">
                                        Lotes ({{ $producto->lotes->count() }})
                                    </button>
                                    {{-- Editar --}}
                                    <x-action-button
                                        type="edit"
                                        wire:click="editarProducto('{{ $producto->id }}')"
                                        title="Editar producto" />
                                    {{-- Toggle Estado --}}
                                    @if($producto->activo)
                                        <x-action-button
                                            type="delete"
                                            wire:click="toggleEstado('{{ $producto->id }}')"
                                            title="Desactivar producto" />
                                    @else
                                        <x-action-button
                                            type="activate"
                                            wire:click="toggleEstado('{{ $producto->id }}')"
                                            title="Activar producto" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">
                                No se encontraron productos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Ver Lotes del Producto --}}
    @if($productoIdLotesExpandido)
        @php
            $productoSeleccionado = $productos->firstWhere('id', $productoIdLotesExpandido);
        @endphp
        <div x-data="{ show: true, animatingOut: false }"
             x-show="show || animatingOut"
             x-init="$watch('show', value => { if (!value) animatingOut = true; })"
             @animationend="if (!show) animatingOut = false"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
             :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
             wire:click.self="toggleLotes('')">
            <div class="relative p-6 border w-full max-w-4xl shadow-lg rounded-lg bg-white max-h-[90vh] overflow-hidden"
                 :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
                 @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Lotes de Inventario</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $productoSeleccionado->descripcion ?? '' }}</p>
                        <p class="text-xs text-gray-500 font-mono">Código: {{ $productoSeleccionado->id ?? '' }}</p>
                    </div>
                    <button wire:click="toggleLotes('')" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto max-h-[calc(90vh-140px)]">
                    @if($productoSeleccionado && $productoSeleccionado->lotes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                                    <tr>
                                        <th class="py-3 px-4 text-left">Bodega</th>
                                        <th class="py-3 px-4 text-center">Cantidad Disponible</th>
                                        <th class="py-3 px-4 text-center">Cantidad Inicial</th>
                                        <th class="py-3 px-4 text-right">Precio Ingreso</th>
                                        <th class="py-3 px-4 text-left">Fecha Ingreso</th>
                                        <th class="py-3 px-4 text-left">Observaciones</th>
                                        <th class="py-3 px-4 text-center">Estado</th>
                                        <th class="py-3 px-4 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm">
                                    @foreach($productoSeleccionado->lotes as $lote)
                                        <tr class="border-b border-gray-200 {{ $editingLoteId === $lote->id ? 'bg-blue-50' : 'hover:bg-gray-50' }} {{ $lote->estado ? '' : 'opacity-50' }}">
                                            {{-- Bodega --}}
                                            <td class="py-3 px-4 text-left">
                                                @if($editingLoteId === $lote->id)
                                                    <select wire:model="loteBodegaId" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                        @foreach($bodegas as $bodega)
                                                            <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    {{ $lote->bodega->nombre ?? 'Sin bodega' }}
                                                @endif
                                            </td>

                                            {{-- Cantidad Disponible --}}
                                            <td class="py-3 px-4 text-center font-semibold">
                                                @if($editingLoteId === $lote->id)
                                                    <input type="number" wire:model="loteCantidad" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center" min="0">
                                                @else
                                                    {{ $lote->cantidad }}
                                                @endif
                                            </td>

                                            {{-- Cantidad Inicial --}}
                                            <td class="py-3 px-4 text-center">{{ $lote->cantidad_inicial }}</td>

                                            {{-- Precio Ingreso --}}
                                            <td class="py-3 px-4 text-right font-semibold text-green-600">
                                                @if($editingLoteId === $lote->id)
                                                    <input type="number" wire:model="lotePrecioIngreso" step="0.01" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm text-right" min="0">
                                                @else
                                                    Q{{ number_format($lote->precio_ingreso, 2) }}
                                                @endif
                                            </td>

                                            {{-- Fecha Ingreso --}}
                                            <td class="py-3 px-4 text-left">
                                                @if($editingLoteId === $lote->id)
                                                    <input type="date" wire:model="loteFechaIngreso" class="w-32 px-2 py-1 border border-gray-300 rounded text-sm">
                                                @else
                                                    {{ $lote->fecha_ingreso ? \Carbon\Carbon::parse($lote->fecha_ingreso)->format('d/m/Y') : '-' }}
                                                @endif
                                            </td>

                                            {{-- Observaciones --}}
                                            <td class="py-3 px-4 text-left text-xs text-gray-500">
                                                @if($editingLoteId === $lote->id)
                                                    <input type="text" wire:model="loteObservaciones" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" placeholder="Observaciones">
                                                @else
                                                    {{ Str::limit($lote->observaciones ?? '-', 30) }}
                                                @endif
                                            </td>

                                            {{-- Estado --}}
                                            <td class="py-3 px-4 text-center">
                                                @if($lote->estado)
                                                    <span class="bg-green-200 text-green-700 py-1 px-2 rounded-full text-xs font-semibold">Activo</span>
                                                @else
                                                    <span class="bg-gray-300 text-gray-700 py-1 px-2 rounded-full text-xs font-semibold">Inactivo</span>
                                                @endif
                                            </td>

                                            {{-- Acciones --}}
                                            <td class="py-3 px-4 text-center">
                                                @if($editingLoteId === $lote->id)
                                                    {{-- Modo edición: mostrar guardar y cancelar --}}
                                                    <div class="flex item-center justify-center gap-1">
                                                        <button wire:click="guardarLote" class="bg-green-600 hover:bg-green-700 text-white p-1.5 rounded" title="Guardar cambios">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <button wire:click="cancelarEdicionLote" class="bg-gray-400 hover:bg-gray-500 text-white p-1.5 rounded" title="Cancelar">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @else
                                                    {{-- Modo normal: mostrar editar, activar/desactivar --}}
                                                    <div class="flex item-center justify-center gap-1">
                                                        <x-action-button
                                                            type="edit"
                                                            wire:click="editarLote({{ $lote->id }})"
                                                            title="Editar lote" />
                                                        @if($lote->estado)
                                                            <x-action-button
                                                                type="delete"
                                                                wire:click="eliminarLote({{ $lote->id }})"
                                                                title="Desactivar lote" />
                                                        @else
                                                            <x-action-button
                                                                type="activate"
                                                                wire:click="activarLote({{ $lote->id }})"
                                                                title="Activar lote" />
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>No hay lotes registrados para este producto.</p>
                            <p class="text-xs mt-1">Crea el primer lote usando el botón de arriba.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Crear/Editar Producto --}}
    <div x-data="{
            show: @entangle('showModal'),
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModal">
        <div class="relative p-6 border w-full max-w-lg shadow-lg rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    {{ $editingId ? 'Editar Producto' : 'Crear Producto' }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="guardarProducto">
                {{-- Código --}}
                <div class="mb-4">
                    <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">
                        Código del Producto
                    </label>
                    <input
                        type="text"
                        id="codigo"
                        wire:model="codigo"
                        {{ $editingId ? 'disabled' : '' }}
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('codigo') border-red-500 @enderror {{ $editingId ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                        placeholder="Ej: PROD-001">
                    @error('codigo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="mb-4">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <input
                        type="text"
                        id="descripcion"
                        wire:model="descripcion"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('descripcion') border-red-500 @enderror"
                        placeholder="Ej: Tornillos de acero inoxidable">
                    @error('descripcion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Categoría --}}
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label for="categoriaId" class="block text-sm font-medium text-gray-700">
                            Categoría
                        </label>
                        <button
                            type="button"
                            wire:click="abrirSubModalCategoria"
                            class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            + Crear Categoría
                        </button>
                    </div>
                    <select
                        id="categoriaId"
                        wire:model="categoriaId"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('categoriaId') border-red-500 @enderror">
                        <option value="">Seleccione una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoriaId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Es Consumible --}}
                <div class="mb-4">
                    <label class="flex items-center space-x-3">
                        <input
                            type="checkbox"
                            wire:model="esConsumible"
                            class="w-5 h-5 text-blue-600 border-2 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">
                            Este producto es consumible
                        </span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-8">
                        Marca esta opción si el producto se agota con el uso (ej: materiales, insumos)
                    </p>
                </div>

                <div class="flex justify-between mt-6">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                        {{ $editingId ? 'Actualizar' : 'Crear' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sub-modal Crear Categoría --}}
    <div x-data="{
            show: @entangle('showSubModalCategoria'),
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center"
         style="z-index: 9999 !important;"
         :style="(!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')) + ' z-index: 9999 !important;'"
         wire:click.self="closeSubModalCategoria">
        <div class="relative p-6 border w-full max-w-sm shadow-lg rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Nueva Categoría</h3>
                <button wire:click="closeSubModalCategoria" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="guardarNuevaCategoria">
                <div class="mb-6">
                    <label for="nuevaCategoriaNombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de la Categoría
                    </label>
                    <input
                        type="text"
                        id="nuevaCategoriaNombre"
                        wire:model="nuevaCategoriaNombre"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nuevaCategoriaNombre') border-red-500 @enderror"
                        placeholder="Ej: Equipos de Protección">
                    @error('nuevaCategoriaNombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between">
                    <button
                        type="button"
                        wire:click="closeSubModalCategoria"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                        Crear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
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

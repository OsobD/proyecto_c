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
            class="bg-eemq-horizon hover:bg-eemq-horizon-600 text-white font-semibold py-2.5 px-5 rounded-lg shadow-md transition-all duration-150 hover:shadow-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Nuevo Producto
        </button>
    </div>

    {{-- Alertas de éxito y error para operaciones CRUD --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-md mb-4 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-md mb-4 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Contenedor principal --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        {{-- Campo de búsqueda con filtrado reactivo --}}
        <div class="p-6 border-b border-gray-200">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchProducto"
                    class="w-full md:w-2/3 lg:w-1/2 pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent transition-all"
                    placeholder="Buscar por código, descripción o categoría...">
            </div>
        </div>

        {{-- Tabla de listado de productos --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Categoría</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($productos as $producto)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-semibold text-gray-900">{{ $producto->id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $producto->descripcion }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($producto->es_consumible)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
                                        </svg>
                                        Consumible
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7 2a1 1 0 00-.707 1.707L7 4.414v3.758a1 1 0 01-.293.707l-4 4C.817 14.769 2.156 18 4.828 18h10.343c2.673 0 4.012-3.231 2.122-5.121l-4-4A1 1 0 0113 8.172V4.414l.707-.707A1 1 0 0013 2H7zm2 6.172V4h2v4.172a3 3 0 00.879 2.12l1.027 1.028a4 4 0 00-2.171.102l-.47.156a4 4 0 01-2.53 0l-.563-.187a1.993 1.993 0 00-.114-.035l1.063-1.063A3 3 0 009 8.172z" clip-rule="evenodd" />
                                        </svg>
                                        No Consumible
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($producto->activo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-green-400 rounded-full"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-gray-400 rounded-full"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Ver Lotes --}}
                                    <button
                                        wire:click="toggleLotes('{{ $producto->id }}')"
                                        class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md text-xs font-medium transition-colors duration-150"
                                        title="Ver lotes">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                        </svg>
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="mt-3 text-sm text-gray-500">No se encontraron productos</p>
                                <p class="text-xs text-gray-400 mt-1">Intenta ajustar los filtros de búsqueda o crea un nuevo producto</p>
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
             class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
             :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
             wire:click.self="toggleLotes('')">
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden"
                 :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
                 @click.stop>
                {{-- Header del modal --}}
                <div class="bg-gradient-to-r from-eemq-horizon to-blue-600 px-6 py-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold">Lotes de Inventario</h3>
                            <p class="text-blue-100 text-sm mt-1">{{ $productoSeleccionado->descripcion ?? '' }}</p>
                            <p class="text-blue-200 text-xs mt-0.5 font-mono">Código: {{ $productoSeleccionado->id ?? '' }}</p>
                        </div>
                        <button wire:click="toggleLotes('')" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-4">
                        <button
                            wire:click="abrirModalCrearLote('{{ $productoIdLotesExpandido }}')"
                            class="inline-flex items-center px-4 py-2 bg-white text-eemq-horizon hover:bg-gray-50 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Crear Nuevo Lote
                        </button>
                    </div>
                </div>

                {{-- Contenido del modal --}}
                <div class="overflow-y-auto max-h-[calc(90vh-200px)]">
                    @if($productoSeleccionado && $productoSeleccionado->lotes->count() > 0)
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($productoSeleccionado->lotes as $lote)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-white {{ $lote->estado ? '' : 'opacity-60' }}">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                                                    </svg>
                                                    <h4 class="font-semibold text-gray-900">{{ $lote->bodega->nombre ?? 'Sin bodega' }}</h4>
                                                </div>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">Cantidad disponible:</span>
                                                        <span class="font-bold text-gray-900">{{ $lote->cantidad }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">Cantidad inicial:</span>
                                                        <span class="font-medium text-gray-700">{{ $lote->cantidad_inicial }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">Precio ingreso:</span>
                                                        <span class="font-semibold text-green-600">Q{{ number_format($lote->precio_ingreso, 2) }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">Fecha ingreso:</span>
                                                        <span class="text-gray-700">{{ $lote->fecha_ingreso ? \Carbon\Carbon::parse($lote->fecha_ingreso)->format('d/m/Y') : '-' }}</span>
                                                    </div>
                                                    @if($lote->observaciones)
                                                        <div class="pt-2 border-t border-gray-100">
                                                            <p class="text-xs text-gray-600"><span class="font-medium">Observaciones:</span> {{ $lote->observaciones }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                @if($lote->estado)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <span class="w-1.5 h-1.5 mr-1 bg-green-400 rounded-full"></span>
                                                        Activo
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <span class="w-1.5 h-1.5 mr-1 bg-gray-400 rounded-full"></span>
                                                        Inactivo
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex gap-2 pt-3 border-t border-gray-100">
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
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No hay lotes registrados</h3>
                            <p class="mt-2 text-sm text-gray-500">Este producto aún no tiene lotes de inventario. Crea el primero para comenzar.</p>
                            <div class="mt-6">
                                <button
                                    wire:click="abrirModalCrearLote('{{ $productoIdLotesExpandido }}')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-eemq-horizon hover:bg-eemq-horizon-600">
                                    Crear Primer Lote
                                </button>
                            </div>
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
         class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModal">
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="bg-gradient-to-r from-eemq-horizon to-blue-600 px-6 py-5 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">
                        {{ $editingId ? 'Editar Producto' : 'Crear Producto' }}
                    </h3>
                    <button wire:click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="guardarProducto" class="p-6">
                {{-- Código --}}
                <div class="mb-4">
                    <label for="codigo" class="block text-sm font-semibold text-gray-700 mb-2">
                        Código del Producto <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="codigo"
                        wire:model="codigo"
                        {{ $editingId ? 'disabled' : '' }}
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('codigo') border-red-500 @enderror {{ $editingId ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                        placeholder="Ej: PROD-001">
                    @error('codigo')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="mb-4">
                    <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-2">
                        Descripción <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="descripcion"
                        wire:model="descripcion"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('descripcion') border-red-500 @enderror"
                        placeholder="Ej: Tornillos de acero inoxidable">
                    @error('descripcion')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Categoría --}}
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label for="categoriaId" class="block text-sm font-semibold text-gray-700">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <button
                            type="button"
                            wire:click="abrirSubModalCategoria"
                            class="text-eemq-horizon hover:text-eemq-horizon-700 text-xs font-semibold flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Crear Categoría
                        </button>
                    </div>
                    <select
                        id="categoriaId"
                        wire:model="categoriaId"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('categoriaId') border-red-500 @enderror">
                        <option value="">Seleccione una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoriaId')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Es Consumible --}}
                <div class="mb-6">
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input
                            type="checkbox"
                            wire:model="esConsumible"
                            class="mr-3 h-4 w-4 text-eemq-horizon focus:ring-eemq-horizon border-gray-300 rounded">
                        <div>
                            <span class="text-sm font-semibold text-gray-700">Es producto consumible</span>
                            <p class="text-xs text-gray-500 mt-0.5">Los productos consumibles se agotan con el uso (ej. material de oficina)</p>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg font-semibold transition-colors">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-eemq-horizon hover:bg-eemq-horizon-600 text-white rounded-lg font-semibold shadow-md transition-all hover:shadow-lg">
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
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="bg-gradient-to-r from-eemq-horizon to-blue-600 px-6 py-5 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Nueva Categoría</h3>
                    <button wire:click="closeSubModalCategoria" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="guardarNuevaCategoria" class="p-6">
                <div class="mb-6">
                    <label for="nuevaCategoriaNombre" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nombre de la Categoría
                    </label>
                    <input
                        type="text"
                        id="nuevaCategoriaNombre"
                        wire:model="nuevaCategoriaNombre"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('nuevaCategoriaNombre') border-red-500 @enderror"
                        placeholder="Ej: Equipos de Protección">
                    @error('nuevaCategoriaNombre')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        wire:click="closeSubModalCategoria"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg font-semibold transition-colors">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-eemq-horizon hover:bg-eemq-horizon-600 text-white rounded-lg font-semibold shadow-md transition-all hover:shadow-lg">
                        Crear
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Crear/Editar Lote --}}
    <div x-data="{
            show: @entangle('showModalLotes').live || @entangle('showModalEditarLote').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalLotes">
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="bg-gradient-to-r from-eemq-horizon to-blue-600 px-6 py-5 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">
                        {{ $editingLoteId ? 'Editar Lote' : 'Crear Lote' }}
                    </h3>
                    <button wire:click="closeModalLotes" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="guardarLote" class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Cantidad --}}
                    <div class="mb-4">
                        <label for="loteCantidad" class="block text-sm font-semibold text-gray-700 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="loteCantidad"
                            wire:model="loteCantidad"
                            min="0"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('loteCantidad') border-red-500 @enderror"
                            placeholder="0">
                        @error('loteCantidad')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Precio de Ingreso --}}
                    <div class="mb-4">
                        <label for="lotePrecioIngreso" class="block text-sm font-semibold text-gray-700 mb-2">
                            Precio de Ingreso <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Q</span>
                            <input
                                type="number"
                                id="lotePrecioIngreso"
                                wire:model="lotePrecioIngreso"
                                step="0.01"
                                min="0"
                                class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('lotePrecioIngreso') border-red-500 @enderror"
                                placeholder="0.00">
                        </div>
                        @error('lotePrecioIngreso')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha de Ingreso --}}
                    <div class="mb-4">
                        <label for="loteFechaIngreso" class="block text-sm font-semibold text-gray-700 mb-2">
                            Fecha de Ingreso <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="loteFechaIngreso"
                            wire:model="loteFechaIngreso"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('loteFechaIngreso') border-red-500 @enderror">
                        @error('loteFechaIngreso')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bodega --}}
                    <div class="mb-4">
                        <label for="loteBodegaId" class="block text-sm font-semibold text-gray-700 mb-2">
                            Bodega <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="loteBodegaId"
                            wire:model="loteBodegaId"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('loteBodegaId') border-red-500 @enderror">
                            <option value="">Seleccione una bodega</option>
                            @foreach($bodegas as $bodega)
                                <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                            @endforeach
                        </select>
                        @error('loteBodegaId')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="mb-6">
                    <label for="loteObservaciones" class="block text-sm font-semibold text-gray-700 mb-2">
                        Observaciones
                    </label>
                    <textarea
                        id="loteObservaciones"
                        wire:model="loteObservaciones"
                        rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('loteObservaciones') border-red-500 @enderror"
                        placeholder="Ingrese observaciones adicionales (opcional)"></textarea>
                    @error('loteObservaciones')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        wire:click="closeModalLotes"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg font-semibold transition-colors">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-eemq-horizon hover:bg-eemq-horizon-600 text-white rounded-lg font-semibold shadow-md transition-all hover:shadow-lg">
                        {{ $editingLoteId ? 'Actualizar' : 'Crear' }}
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

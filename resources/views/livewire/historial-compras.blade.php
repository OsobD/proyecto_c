<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Compras', 'url' => route('compras')],
        ['label' => 'Historial'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Historial de Compras</h1>
        <a href="{{ route('compras.nueva') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            + Nueva Compra
        </a>
    </div>

    {{-- Mensajes de éxito --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Mensajes de error --}}
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Filtros de Búsqueda</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Búsqueda general --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input
                    type="text"
                    id="search"
                    wire:model.live.debounce.300ms="search"
                    class="block w-full py-2.5 px-4 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400"
                    placeholder="No. Factura o Proveedor...">
            </div>

            {{-- Filtro de Proveedor con búsqueda --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                <div class="relative">
                    @if($selectedProveedorFiltro)
                        <div wire:click="clearProveedorFiltro"
                             class="flex items-center justify-between w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                            <span class="font-medium text-gray-800">{{ $selectedProveedorFiltro['nombre'] }}</span>
                            <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: @entangle('showProveedorDropdown').live }" @click.outside="open = false">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="searchProveedorFiltro"
                                @click="open = true"
                                class="block w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400"
                                placeholder="Buscar proveedor...">
                            <div x-show="open"
                                 x-transition
                                 class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                <ul>
                                    <li wire:click.prevent="clearProveedorFiltro"
                                        @click="open = false"
                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                        Todos los proveedores
                                    </li>
                                    @foreach (array_slice($this->proveedorResults, 0, 8) as $proveedor)
                                        <li wire:click.prevent="selectProveedorFiltro({{ $proveedor['id'] }})"
                                            @click="open = false"
                                            class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                            {{ $proveedor['nombre'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Filtro de Bodega con búsqueda --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bodega Destino</label>
                <div class="relative">
                    @if($selectedBodegaFiltro)
                        <div wire:click="clearBodegaFiltro"
                             class="flex items-center justify-between w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                            <span class="font-medium text-gray-800">{{ $selectedBodegaFiltro['nombre'] }}</span>
                            <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: @entangle('showBodegaDropdown').live }" @click.outside="open = false">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="searchBodegaFiltro"
                                @click="open = true"
                                class="block w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400"
                                placeholder="Buscar bodega...">
                            <div x-show="open"
                                 x-transition
                                 class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                <ul>
                                    <li wire:click.prevent="clearBodegaFiltro"
                                        @click="open = false"
                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                        Todas las bodegas
                                    </li>
                                    @foreach (array_slice($this->bodegaResults, 0, 8) as $bodega)
                                        <li wire:click.prevent="selectBodegaFiltro({{ $bodega['id'] }})"
                                            @click="open = false"
                                            class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                            {{ $bodega['nombre'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Fecha Inicio con Flatpickr --}}
            <div x-data="{
                picker: null,
                initFlatpickr() {
                    this.picker = flatpickr(this.$refs.fechaInicio, {
                        dateFormat: 'Y-m-d',
                        locale: 'es',
                        altInput: true,
                        altFormat: 'd/m/Y',
                        allowInput: true,
                        onChange: (selectedDates, dateStr) => {
                            @this.set('fechaInicio', dateStr);
                        }
                    });
                }
            }"
            x-init="initFlatpickr()"
            @limpiar-filtros.window="if(picker) picker.clear()">
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="inline w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Fecha Inicio
                </label>
                <input
                    x-ref="fechaInicio"
                    type="text"
                    id="fecha_inicio"
                    wire:model="fechaInicio"
                    placeholder="dd/mm/aaaa"
                    class="block w-full py-2.5 px-4 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400 cursor-pointer">
            </div>

            {{-- Fecha Fin con Flatpickr --}}
            <div x-data="{
                picker: null,
                initFlatpickr() {
                    this.picker = flatpickr(this.$refs.fechaFin, {
                        dateFormat: 'Y-m-d',
                        locale: 'es',
                        altInput: true,
                        altFormat: 'd/m/Y',
                        allowInput: true,
                        onChange: (selectedDates, dateStr) => {
                            @this.set('fechaFin', dateStr);
                        }
                    });
                }
            }"
            x-init="initFlatpickr()"
            @limpiar-filtros.window="if(picker) picker.clear()">
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="inline w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Fecha Fin
                </label>
                <input
                    x-ref="fechaFin"
                    type="text"
                    id="fecha_fin"
                    wire:model="fechaFin"
                    placeholder="dd/mm/aaaa"
                    class="block w-full py-2.5 px-4 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400 cursor-pointer">
            </div>

            {{-- Botón Limpiar Filtros --}}
            <div class="flex items-end">
                <button
                    wire:click="limpiarFiltros"
                    @click="$dispatch('limpiar-filtros')"
                    class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                    <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla de Compras --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">
                Compras encontradas: <span class="text-blue-600">{{ $comprasFiltradas->total() }}</span>
            </h2>
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 font-medium">Mostrar:</span>
                    <select wire:model.live="perPage"
                            class="border-2 border-gray-300 rounded-lg text-sm shadow-sm py-1.5 px-3 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                
                <p class="text-sm text-gray-600">
                    Mostrando {{ $comprasFiltradas->firstItem() ?? 0 }} - {{ $comprasFiltradas->lastItem() ?? 0 }} de {{ $comprasFiltradas->total() }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Factura</th>
                        <th class="py-3 px-6 text-left">Correlativo</th>
                        <th class="py-3 px-6 text-left">Serie</th>
                        <th class="py-3 px-6 text-left">Proveedor</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-center">Productos</th>
                        <th class="py-3 px-6 text-right">Monto (sin IVA)</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($comprasFiltradas as $compra)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 {{ !$compra->activo ? 'opacity-50' : '' }}">
                            <td class="py-3 px-6 text-left font-medium">{{ $compra->no_factura ?? 'N/A' }}</td>
                            <td class="py-3 px-6 text-left">{{ $compra->correlativo ?? 'N/A' }}</td>
                            <td class="py-3 px-6 text-left">{{ $compra->no_serie ?? 'N/A' }}</td>
                            <td class="py-3 px-6 text-left">{{ $compra->proveedor->nombre ?? 'Sin proveedor' }}</td>
                            <td class="py-3 px-6 text-left">{{ $compra->fecha->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                                    {{ $compra->detalles->count() }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right font-semibold">Q{{ number_format($compra->total / 1.12, 2) }}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                    Completada
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-action-button
                                        type="view"
                                        title="Ver detalle"
                                        wire:click="verDetalle({{ $compra->id }})" />

                                    @if($compra->activo)
                                        <x-action-button
                                            type="edit"
                                            title="Editar"
                                            wire:click="editarCompra({{ $compra->id }})" />

                                        <x-action-button
                                            type="delete"
                                            title="Desactivar"
                                            wire:click="abrirModalDesactivar({{ $compra->id }})" />
                                    @else
                                        <x-action-button
                                            type="activate"
                                            title="Activar"
                                            wire:click="activarCompra({{ $compra->id }})" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-500">
                                No se encontraron compras con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $comprasFiltradas->links() }}
        </div>
    </div>

    {{-- Modal de Visualización de Detalle de Compra --}}
    <div x-data="{
            show: @entangle('showModalVer').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalVer">
        <div class="relative p-6 border w-full max-w-3xl shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Detalle de Compra</h3>
                <button wire:click="closeModalVer" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($compraSeleccionada)
                <div class="space-y-4">
                    {{-- Información de la compra --}}
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Bodega Destino:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['bodega'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Proveedor:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['proveedor'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Número de Factura:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['numero_factura'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Correlativo:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['correlativo'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Número de Serie:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['no_serie'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Fecha de Compra:</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($compraSeleccionada['fecha'] ?? now())->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Detalle de productos --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Productos en la Compra:</h4>
                        <div class="overflow-x-auto max-h-64 overflow-y-auto border rounded-md">
                            <table class="min-w-full bg-white text-sm">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Código</th>
                                        <th class="py-2 px-3 text-left">Descripción</th>
                                        <th class="py-2 px-3 text-center">Cantidad</th>
                                        <th class="py-2 px-3 text-right">Precio Unit.</th>
                                        <th class="py-2 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($compraSeleccionada['productos']) && count($compraSeleccionada['productos']) > 0)
                                        @foreach($compraSeleccionada['productos'] as $producto)
                                            <tr class="border-t hover:bg-gray-50">
                                                <td class="py-2 px-3 font-mono">{{ $producto['codigo'] }}</td>
                                                <td class="py-2 px-3">{{ $producto['descripcion'] }}</td>
                                                <td class="py-2 px-3 text-center">{{ $producto['cantidad'] }}</td>
                                                <td class="py-2 px-3 text-right">Q{{ number_format($producto['precio'], 2) }}</td>
                                                <td class="py-2 px-3 text-right font-semibold">Q{{ number_format($producto['subtotal'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">No hay productos en esta compra</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="bg-blue-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total de la Compra (sin IVA):</span>
                            <span class="text-2xl font-bold text-blue-600">Q{{ number_format($compraSeleccionada['total'] ?? 0, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Total de productos: {{ isset($compraSeleccionada['productos']) ? count($compraSeleccionada['productos']) : 0 }}
                        </p>
                    </div>

                    {{-- Botón de cerrar --}}
                    <div class="flex justify-end mt-6">
                        <button
                            wire:click="closeModalVer"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg">
                            Cerrar
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de Edición de Compra --}}
    <div x-data="{
            show: @entangle('showModalEditar').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalEditar">
        <div class="relative p-6 border w-full max-w-4xl shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Editar Compra</h3>
                <button wire:click="closeModalEditar" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($compraSeleccionada)
                <div class="space-y-4">
                    {{-- Información de la compra --}}
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Bodega Destino:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['bodega'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Proveedor:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['proveedor'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Número de Factura:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['numero_factura'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Correlativo:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['correlativo'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Número de Serie:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['no_serie'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Fecha de Compra:</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($compraSeleccionada['fecha'] ?? now())->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla editable de productos --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Editar Productos (Cantidades y Precios):</h4>
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border rounded-md">
                            <table class="min-w-full bg-white text-sm">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Código</th>
                                        <th class="py-2 px-3 text-left">Descripción</th>
                                        <th class="py-2 px-3 text-center">Cantidad</th>
                                        <th class="py-2 px-3 text-right">Precio Unit.</th>
                                        <th class="py-2 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($compraSeleccionada['productos']) && count($compraSeleccionada['productos']) > 0)
                                        @foreach($compraSeleccionada['productos'] as $index => $producto)
                                            <tr class="border-t hover:bg-gray-50">
                                                <td class="py-2 px-3 font-mono">{{ $producto['codigo'] }}</td>
                                                <td class="py-2 px-3">{{ $producto['descripcion'] }}</td>
                                                <td class="py-2 px-3 text-center">
                                                    <input
                                                        type="number"
                                                        step="1"
                                                        wire:model.blur="compraSeleccionada.productos.{{ $index }}.cantidad"
                                                        min="1"
                                                        class="w-20 text-center border-2 border-green-300 bg-green-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent font-semibold px-2 py-1"
                                                    >
                                                </td>
                                                <td class="py-2 px-3 text-right">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1">Q</span>
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            wire:model.blur="compraSeleccionada.productos.{{ $index }}.precio"
                                                            min="0"
                                                            class="w-28 text-right border-2 border-blue-300 bg-blue-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-semibold px-2 py-1"
                                                        >
                                                    </div>
                                                </td>
                                                <td class="py-2 px-3 text-right font-semibold">
                                                    Q{{ number_format($producto['cantidad'] * $producto['precio'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">No hay productos en esta compra</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Total actualizado --}}
                    <div class="bg-blue-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total de la Compra (sin IVA):</span>
                            <span class="text-2xl font-bold text-blue-600">
                                Q{{ number_format(collect($compraSeleccionada['productos'])->sum(function($p) { return $p['cantidad'] * $p['precio']; }), 2) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Total de productos: {{ isset($compraSeleccionada['productos']) ? count($compraSeleccionada['productos']) : 0 }}
                        </p>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="flex justify-end gap-3 mt-6">
                        <button
                            wire:click="closeModalEditar"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg">
                            Cancelar
                        </button>
                        <button
                            wire:click="abrirModalConfirmarEdicion"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de Confirmación de Edición --}}
    <div x-data="{
            show: @entangle('showModalConfirmarEdicion').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center"
         style="z-index: 9999 !important;"
         :style="(!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')) + ' z-index: 9999 !important;'"
         wire:click.self="closeModalConfirmarEdicion">
        <div class="relative p-6 border w-full max-w-md shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Confirmar Cambios</h3>
                <button wire:click="closeModalConfirmarEdicion" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-gray-700 mb-4">
                    ¿Está seguro de que desea guardar los cambios realizados en esta compra?
                </p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Esta acción modificará los registros de la compra. Los cambios no se pueden deshacer automáticamente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    wire:click="closeModalConfirmarEdicion"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                    Cancelar
                </button>
                <button
                    wire:click="guardarEdicion"
                    wire:loading.attr="disabled"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="guardarEdicion">Confirmar</span>
                    <span wire:loading wire:target="guardarEdicion">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Modal de Confirmación de Desactivación --}}
    <div x-data="{
            show: @entangle('showModalConfirmarDesactivar').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalConfirmarDesactivar">
        <div class="relative p-6 border w-full max-w-md shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Confirmar Desactivación</h3>
                <button wire:click="closeModalConfirmarDesactivar" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-gray-700 mb-4">
                    ¿Está seguro de que desea desactivar esta compra?
                </p>
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                Esta compra quedará desactivada y aparecerá con opacidad en el listado. Podrá activarla nuevamente cuando lo desee.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    wire:click="closeModalConfirmarDesactivar"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                    Cancelar
                </button>
                <button
                    wire:click="confirmarDesactivar"
                    wire:loading.attr="disabled"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="confirmarDesactivar">Desactivar</span>
                    <span wire:loading wire:target="confirmarDesactivar">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Desactivando...
                    </span>
                </button>
            </div>
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

        /* Estilos personalizados para Flatpickr - Tema moderno y suave */
        .flatpickr-calendar {
            background: white !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
            border: none !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            padding: 0 !important;
            margin-top: 8px !important;
        }

        .flatpickr-calendar.open {
            animation: slideDown 0.2s ease-out !important;
        }

        /* Header del calendario */
        .flatpickr-months {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            padding: 16px 12px !important;
            border-radius: 16px 16px 0 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .flatpickr-months .flatpickr-month {
            background: transparent !important;
            color: white !important;
            flex: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .flatpickr-current-month {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            height: auto !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: white !important;
            color: #1e40af !important;
            font-weight: 600 !important;
            font-size: 15px !important;
            border: none !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
            background: #eff6ff !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        }

        /* Estilos para las opciones del dropdown de mes */
        .flatpickr-monthDropdown-months option {
            background: white !important;
            color: #1e40af !important;
            padding: 8px !important;
        }

        .flatpickr-current-month .numInputWrapper {
            width: 70px !important;
            display: flex !important;
            align-items: center !important;
        }

        .flatpickr-current-month .numInputWrapper input,
        .flatpickr-current-month .cur-year {
            color: white !important;
            font-weight: 600 !important;
            font-size: 15px !important;
            background: rgba(255, 255, 255, 0.15) !important;
            border: none !important;
            padding: 6px 8px !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            text-align: center !important;
        }

        .flatpickr-current-month .numInputWrapper:hover input,
        .flatpickr-current-month .numInputWrapper:hover .cur-year {
            background: rgba(255, 255, 255, 0.25) !important;
        }

        /* Flechas del año (arriba y abajo del input de año) */
        .flatpickr-current-month .numInputWrapper span {
            display: none !important;
        }

        /* Flechas de navegación de mes - CENTRADAS */
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            position: static !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            fill: white !important;
            padding: 10px !important;
            border-radius: 10px !important;
            transition: all 0.2s ease !important;
            width: 40px !important;
            height: 40px !important;
            top: auto !important;
            transform: none !important;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: scale(1.1) !important;
        }

        .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-months .flatpickr-next-month svg {
            fill: white !important;
            width: 16px !important;
            height: 16px !important;
        }

        /* Días de la semana */
        .flatpickr-weekdays {
            background: #f8fafc !important;
            padding: 12px 0 8px 0 !important;
            border-bottom: 1px solid #e5e7eb !important;
        }

        .flatpickr-weekday {
            color: #64748b !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            text-transform: uppercase !important;
        }

        /* Contenedor de días */
        .flatpickr-days {
            padding: 8px !important;
        }

        /* Días individuales */
        .flatpickr-day {
            border-radius: 10px !important;
            border: none !important;
            color: #334155 !important;
            font-weight: 500 !important;
            margin: 2px !important;
            transition: all 0.2s ease !important;
            height: 38px !important;
            line-height: 38px !important;
        }

        /* Día actual (hoy) */
        .flatpickr-day.today {
            border: 2px solid #3b82f6 !important;
            background: white !important;
            color: #3b82f6 !important;
            font-weight: 700 !important;
        }

        .flatpickr-day.today:hover {
            background: #eff6ff !important;
            border-color: #3b82f6 !important;
        }

        /* Día seleccionado */
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            border: none !important;
            color: white !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important;
        }

        /* Hover en días normales */
        .flatpickr-day:not(.selected):not(.startRange):not(.endRange):not(.flatpickr-disabled):hover {
            background: #eff6ff !important;
            border: none !important;
            color: #3b82f6 !important;
            transform: scale(1.05) !important;
        }

        /* Días deshabilitados (fuera del mes) */
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #cbd5e1 !important;
        }

        .flatpickr-day.flatpickr-disabled {
            color: #e2e8f0 !important;
        }

        .flatpickr-day.flatpickr-disabled:hover {
            background: transparent !important;
            transform: none !important;
            cursor: not-allowed !important;
        }
    </style>

    {{-- Scripts de Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
</div>

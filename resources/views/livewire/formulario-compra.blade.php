{{--
    Vista: Formulario de Compra
    Descripción: Formulario complejo para registrar compras con selección de proveedor,
                 productos múltiples, cálculo automático de totales y creación rápida de
                 proveedores/productos/categorías mediante modales anidados
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Compras', 'url' => '/compras'],
        ['label' => 'Nueva Compra'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Registrar Nueva Compra</h1>
    </div>

    {{-- Contenedor principal del formulario --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form>
            {{-- Selección de proveedor con autocompletado --}}
            <div class="mb-6">
                <label for="proveedor" class="block text-sm font-medium text-gray-700">Proveedor</label>
                <div class="relative">
                    @if($selectedProveedor)
                        <div class="flex items-center justify-between mt-1 w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm">
                            <div class="flex flex-col gap-0.5 justify-center">
                                <span class="font-medium">{{ $selectedProveedor['nombre'] }}</span>
                                <span class="text-xs text-gray-500 mt-0.5">NIT: {{ $selectedProveedor['nit'] }}</span>
                            </div>
                            <button type="button" wire:click.prevent="clearProveedor" class="text-gray-400 hover:text-gray-600 text-xl">
                                ×
                            </button>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: @entangle('showProveedorDropdown') }" @click.outside="open = false">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="searchProveedor"
                                @click="open = true"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                placeholder="Buscar proveedor..."
                            >
                            <div x-show="open"
                                 x-transition
                                 class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                <ul>
                                    @foreach (array_slice($this->proveedorResults, 0, 6) as $proveedor)
                                        <li wire:click.prevent="selectProveedor({{ $proveedor['id'] }})"
                                            class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                            {{ $proveedor['nombre'] }}
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="border-t border-gray-200">
                                    <button
                                        type="button"
                                        wire:click="abrirModalProveedor"
                                        class="w-full px-3 py-2 text-left text-blue-600 hover:bg-blue-50 font-semibold flex items-center gap-2">
                                        <span>+  </span>
                                        <span> Crear nuevo proveedor</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Información de factura --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="numero_factura" class="block text-sm font-medium text-gray-700">Número de Factura:</label>
                    <input
                        type="text"
                        id="numero_factura"
                        wire:model="numeroFactura"
                        class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Ingrese el número de factura...">
                </div>

                <div>
                    <label for="numero_serie" class="block text-sm font-medium text-gray-700">Número de Serie / Correlativo:</label>
                    <input
                        type="text"
                        id="numero_serie"
                        wire:model="numeroSerie"
                        class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Ingrese el número de serie...">
                </div>
            </div>

            {{-- Búsqueda de productos --}}
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center mb-2">
                    <label for="searchProducto" class="block text-sm font-medium text-gray-700">Buscar producto:</label>
                    <button
                        type="button"
                        wire:click="abrirModalProducto"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg">
                        + Nuevo Producto
                    </button>
                </div>
                <div class="relative" x-data="{ open: @entangle('showProductoDropdown') }" @click.outside="open = false">
                    <input
                        type="text"
                        id="searchProducto"
                        wire:model.live.debounce.300ms="searchProducto"
                        @click="open = true"
                        wire:keydown.enter.prevent="seleccionarPrimerResultado"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                        placeholder="Buscar por código o descripción..."
                    >
                    <div x-show="open"
                         x-transition
                         class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                        <ul>
                            @foreach ($this->productoResults as $producto)
                                <li wire:click.prevent="selectProducto({{ $producto['id'] }})"
                                    class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center">
                                    <span class="font-mono text-gray-500 mr-2">{{ $producto['codigo'] }}</span>
                                    <span>{{ $producto['descripcion'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Tabla de productos agregados a la compra con inputs para cantidad y costo --}}
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-800">Productos en la Compra</h2>
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Código</th>
                                <th class="py-3 px-6 text-left">Descripción</th>
                                <th class="py-3 px-6 text-center">Cantidad</th>
                                <th class="py-3 px-6 text-right">Costo Unitario</th>
                                <th class="py-3 px-6 text-right">Precio sin IVA</th>
                                <th class="py-3 px-6 text-right">Subtotal</th>
                                <th class="py-3 px-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($productosSeleccionados as $producto)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left font-mono">{{ $producto['codigo'] }}</td>
                                    <td class="py-3 px-6 text-left">{{ $producto['descripcion'] }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <input
                                            type="number"
                                            wire:model.blur="productosSeleccionados.{{ $loop->index }}.cantidad"
                                            min="1"
                                            placeholder="0"
                                            class="w-24 text-center border-2 border-blue-300 bg-blue-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-semibold"
                                        >
                                    </td>
                                    <td class="py-3 px-6 text-right">
                                        <div class="flex items-center justify-end">
                                            <span class="mr-1 px-3">Q</span>
                                            <input
                                                type="number"
                                                step="0.01"
                                                wire:model.blur="productosSeleccionados.{{ $loop->index }}.costo"
                                                min="0"
                                                placeholder="0.00"
                                                class="w-28 text-right border-2 border-blue-300 bg-blue-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-semibold"
                                            >
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-right">
                                        <span class="text-gray-700 font-medium">Q{{ number_format((float)$producto['costo'] * 0.88, 2) }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-right font-semibold">Q{{ number_format((float)$producto['cantidad'] * ((float)$producto['costo'] * 0.88), 2) }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <button
                                            type="button"
                                            wire:click="eliminarProducto({{ $producto['id'] }})"
                                            class="text-red-600 hover:text-red-800 font-medium">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Totales de la compra --}}
            <div class="mt-8 flex justify-end">
                <div class="w-full max-w-sm">
                    <div class="flex justify-between py-2 border-b">
                        <span class="font-medium text-gray-700">Total:</span>
                        <span class="font-bold text-lg text-gray-800">Q{{ number_format($this->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Registrar Compra
                </button>
            </div>
        </form>
    </div>

    {{-- Mensaje de éxito al crear producto o proveedor --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Modal para crear nuevo producto durante la compra --}}
    <div x-data="{
            show: @entangle('showModalProducto').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalProducto"
         wire:ignore.self>
        <div class="relative p-6 border w-full max-w-lg shadow-lg rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Crear Nuevo Producto</h3>
                    <button wire:click="closeModalProducto" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="guardarNuevoProducto">
                    {{-- Código --}}
                    <div class="mb-4">
                        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">
                            Código del Producto
                        </label>
                        <input
                            type="text"
                            id="codigo"
                            wire:model="codigo"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('codigo') border-red-500 @enderror"
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
                        <textarea
                            id="descripcion"
                            wire:model="descripcion"
                            rows="4"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('descripcion') border-red-500 @enderror"
                            placeholder="Ej: Basureros de plástico 50L"></textarea>
                        @error('descripcion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Categoría --}}
                    <div class="mb-4">
                        <label for="categoriaId" class="block text-sm font-medium text-gray-700 mb-2">
                            Categoría
                        </label>
                        <div class="relative">
                            @if($selectedCategoria)
                                <div class="flex items-center justify-between w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm @error('categoriaId') border-red-500 @enderror">
                                    <span>{{ $selectedCategoria['nombre'] }}</span>
                                    <button type="button" wire:click.prevent="clearCategoria" class="text-gray-400 hover:text-gray-600">
                                        ×
                                    </button>
                                </div>
                            @else
                                <div class="relative" x-data="{ open: @entangle('showCategoriaDropdown') }" @click.outside="open = false">
                                    <input
                                        type="text"
                                        wire:model.live.debounce.300ms="searchCategoria"
                                        @click="open = true"
                                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('categoriaId') border-red-500 @enderror"
                                        placeholder="Buscar categoría..."
                                    >
                                    <div x-show="open"
                                         x-transition
                                         class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                        <ul>
                                            @foreach (array_slice($this->categoriaResults, 0, 6) as $categoria)
                                                <li wire:click.prevent="selectCategoria({{ $categoria['id'] }})"
                                                    class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                    {{ $categoria['nombre'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="border-t border-gray-200">
                                            <button
                                                type="button"
                                                wire:click="abrirSubModalCategoria"
                                                class="w-full px-3 py-2 text-left text-blue-600 hover:bg-blue-50 font-semibold flex items-center gap-2">
                                                <span>+</span>
                                                <span>Crear nueva categoría</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @error('categoriaId')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between mt-6">
                        <button
                            type="button"
                            wire:click="closeModalProducto"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                            Crear y Agregar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    {{-- Modal para crear nuevo proveedor durante la compra --}}
    <div x-data="{
            show: @entangle('showModalProveedor').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalProveedor"
         wire:ignore.self>
        <div class="relative p-6 border w-full max-w-sm shadow-lg rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Crear Nuevo Proveedor</h3>
                    <button wire:click="closeModalProveedor" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="guardarNuevoProveedor">
                    {{-- NIT --}}
                    <div class="mb-6">
                        <label for="nuevoProveedorNit" class="block text-sm font-medium text-gray-700">
                            NIT (Número de Identificación Tributaria)
                        </label>
                        <input
                            type="text"
                            id="nuevoProveedorNit"
                            wire:model="nuevoProveedorNit"
                            class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('nuevoProveedorNit') border-red-500 @enderror"
                            placeholder="Ej: 12345678-9">
                        @error('nuevoProveedorNit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Régimen --}}
                    <div class="mb-6">
                        <label for="nuevoProveedorRegimen" class="block text-sm font-medium text-gray-700">
                            Régimen
                        </label>
                        <div class="relative">
                            @if($selectedRegimen)
                                <div class="flex items-center justify-between mt-1 w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm @error('nuevoProveedorRegimen') border-red-500 @enderror">
                                    <span>{{ $selectedRegimen }}</span>
                                    <button type="button" wire:click.prevent="clearRegimen" class="text-gray-400 hover:text-gray-600">
                                        ×
                                    </button>
                                </div>
                            @else
                                <div class="relative" x-data="{ open: @entangle('showRegimenDropdown') }" @click.outside="open = false">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="mt-1 w-full px-4 py-3 text-left border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('nuevoProveedorRegimen') border-red-500 @enderror">
                                        <span class="text-gray-500">Seleccione un régimen</span>
                                    </button>
                                    <div x-show="open"
                                         x-transition
                                         class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                        <ul>
                                            @foreach($regimenes as $regimen)
                                                <li wire:click.prevent="selectRegimen('{{ $regimen }}')"
                                                    @click="open = false"
                                                    class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                    {{ $regimen }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @error('nuevoProveedorRegimen')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombre del Proveedor --}}
                    <div class="mb-6">
                        <label for="nuevoProveedorNombre" class="block text-sm font-medium text-gray-700">
                            Nombre del Proveedor
                        </label>
                        <input
                            type="text"
                            id="nuevoProveedorNombre"
                            wire:model="nuevoProveedorNombre"
                            class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('nuevoProveedorNombre') border-red-500 @enderror"
                            placeholder="Ej: Ferretería San José">
                        @error('nuevoProveedorNombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between mt-6">
                        <button
                            type="button"
                            wire:click="closeModalProveedor"
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

    {{-- Sub-modal para crear categoría al crear producto (modal anidado con z-index superior) --}}
    <div x-data="{
            show: @entangle('showSubModalCategoria').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center"
         style="z-index: 9999 !important;"
         :style="(!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')) + ' z-index: 9999 !important;'"
         wire:click.self="closeSubModalCategoria"
         wire:ignore.self>
        <div class="relative p-6 border w-full max-w-sm shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
                <div class="flex justify-between items-center mb-6">
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
                            placeholder="Ej: Artículos de Limpieza">
                        @error('nuevaCategoriaNombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between mt-6">
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

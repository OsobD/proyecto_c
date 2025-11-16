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
            {{-- Selección de bodega destino --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Bodega Destino</label>
                <div class="relative">
                    @if($selectedBodega)
                        <div wire:click="clearBodega" class="flex items-center justify-between mt-1 w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm cursor-pointer hover:border-indigo-400 transition-colors">
                            <span class="font-medium">{{ $selectedBodega['nombre'] }}</span>
                            <span class="text-gray-400 text-xl">⟲</span>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: @entangle('showBodegaDropdown').live }" @click.outside="open = false">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="searchBodega"
                                @click="open = true"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                placeholder="Buscar bodega..."
                            >
                            <div x-show="open"
                                 x-transition
                                 class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                <ul>
                                    @foreach (array_slice($this->bodegaResults, 0, 6) as $bodega)
                                        <li wire:click.prevent="selectBodega({{ $bodega['id'] }})"
                                            class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                            {{ $bodega['nombre'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
                @error('selectedBodega')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Selección de proveedor con autocompletado --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Proveedor</label>
                <div class="relative">
                    @if($selectedProveedor)
                        <div wire:click="clearProveedor" class="flex items-center justify-between mt-1 w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm cursor-pointer hover:border-indigo-400 transition-colors">
                            <div class="flex flex-col gap-0.5 justify-center">
                                <span class="font-medium">{{ $selectedProveedor['nombre'] }}</span>
                                <span class="text-xs text-gray-500 mt-0.5">NIT: {{ $selectedProveedor['nit'] }}</span>
                            </div>
                            <span class="text-gray-400 text-xl">⟲</span>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: @entangle('showProveedorDropdown').live }" @click.outside="open = false">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="numero_factura" class="block text-sm font-medium text-gray-700">Número de Factura:</label>
                    <input
                        type="text"
                        id="numero_factura"
                        wire:model="numeroFactura"
                        class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm"
                        placeholder="Ej: 12345678">
                    @error('numeroFactura')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_serie" class="block text-sm font-medium text-gray-700">Número de Serie:</label>
                    <input
                        type="text"
                        id="numero_serie"
                        wire:model="numeroSerie"
                        class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm"
                        placeholder="Ej: ABC123">
                    @error('numeroSerie')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="correlativo" class="block text-sm font-medium text-gray-700">Correlativo:</label>
                    <input
                        type="text"
                        id="correlativo"
                        wire:model="correlativo"
                        class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm"
                        placeholder="Ej: 001">
                    @error('correlativo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Precio de Factura (opcional para verificación) --}}
            <div class="mb-6">
                <label for="precio_factura" class="block text-sm font-medium text-gray-700">Precio Total de Factura (Opcional):</label>
                <input
                    type="number"
                    step="0.01"
                    id="precio_factura"
                    wire:model.live="precioFactura"
                    class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm"
                    placeholder="Ingrese el total de la factura para verificación">
                <p class="text-xs text-gray-500 mt-1">
                    Ingrese el monto total que aparece en la factura física para verificar que coincida con el cálculo del sistema
                </p>
                @error('precioFactura')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Búsqueda de productos --}}
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">Buscar producto:</label>
                    <button
                        type="button"
                        wire:click="abrirModalProducto"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg">
                        + Nuevo Producto
                    </button>
                </div>
                <div class="relative" x-data="{ open: @entangle('showProductoDropdown').live }" @click.outside="open = false">
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
                                <li @click.stop="$wire.selectProducto({{ $producto['id'] }}); open = false"
                                    class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center">
                                    <span class="font-mono text-gray-500 mr-2">#{{ $producto['codigo'] }}</span>
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
                                <th class="py-3 px-6 text-left">Observaciones</th>
                                <th class="py-3 px-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($productosSeleccionados as $index => $producto)
                                <tr class="border-b border-gray-200 hover:bg-gray-50" wire:key="producto-{{ $producto['id'] }}-{{ $index }}">
                                    <td class="py-3 px-6 text-left font-mono">{{ $producto['codigo'] }}</td>
                                    <td class="py-3 px-6 text-left">{{ $producto['descripcion'] }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <input
                                            type="number"
                                            wire:model.blur="productosSeleccionados.{{ $index }}.cantidad"
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
                                                wire:model.blur="productosSeleccionados.{{ $index }}.costo"
                                                min="0"
                                                placeholder="0.00"
                                                class="w-28 text-right border-2 border-blue-300 bg-blue-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-semibold"
                                            >
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-right">
                                        <span class="text-gray-700 font-medium">Q{{ number_format((float)$producto['costo'] / 1.12, 2) }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-right font-semibold">Q{{ number_format((float)$producto['cantidad'] * ((float)$producto['costo'] / 1.12), 2) }}</td>
                                    <td class="py-3 px-6 text-left">
                                        <textarea
                                            wire:model.blur="productosSeleccionados.{{ $index }}.observaciones"
                                            rows="2"
                                            placeholder="Observaciones del lote..."
                                            class="w-full text-sm border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        ></textarea>
                                    </td>
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

            {{-- Totales de la compra con desglose de IVA --}}
            <div class="mt-8 flex justify-end">
                <div class="w-full max-w-sm bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Subtotal (sin IVA):</span>
                        <span class="font-semibold text-gray-800">Q{{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">IVA (12%):</span>
                        <span class="font-semibold text-gray-800">Q{{ number_format($this->iva, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-t-2 border-gray-300 mt-2">
                        <span class="font-bold text-gray-700 text-lg">Total:</span>
                        <span class="font-bold text-lg text-indigo-600">Q{{ number_format($this->total, 2) }}</span>
                    </div>

                    @if(!empty($precioFactura))
                        <div class="mt-3 pt-3 border-t border-gray-300">
                            <div class="flex justify-between py-1">
                                <span class="text-sm text-gray-600">Precio Factura:</span>
                                <span class="text-sm font-semibold text-gray-700">Q{{ number_format((float)$precioFactura, 2) }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-sm font-medium {{ abs($this->diferenciaFactura) < 0.01 ? 'text-green-600' : 'text-red-600' }}">
                                    Diferencia:
                                </span>
                                <span class="text-sm font-bold {{ abs($this->diferenciaFactura) < 0.01 ? 'text-green-600' : 'text-red-600' }}">
                                    Q{{ number_format($this->diferenciaFactura, 2) }}
                                    @if(abs($this->diferenciaFactura) < 0.01)
                                        ✓
                                    @else
                                        ⚠
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="button" wire:click="abrirModalConfirmacion" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    <span wire:loading.remove wire:target="abrirModalConfirmacion">Registrar Compra</span>
                    <span wire:loading wire:target="abrirModalConfirmacion">Verificando...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Modal de Confirmación de Compra --}}
    <div x-data="{
            show: @entangle('showModalConfirmacion').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalConfirmacion">
        <div class="relative p-6 border w-full max-w-3xl shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Confirmar Compra</h3>
                <button wire:click="closeModalConfirmacion" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                {{-- Información de la compra --}}
                <div class="bg-gray-50 p-4 rounded-md">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Bodega Destino:</p>
                            <p class="font-semibold">{{ $selectedBodega['nombre'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Proveedor:</p>
                            <p class="font-semibold">{{ $selectedProveedor['nombre'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Factura:</p>
                            <p class="font-semibold">{{ $numeroFactura }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Número de Serie:</p>
                            <p class="font-semibold">{{ $numeroSerie ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Correlativo:</p>
                            <p class="font-semibold">{{ $correlativo }}</p>
                        </div>
                    </div>
                </div>

                {{-- Resumen de productos --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">Productos a Ingresar:</h4>
                    <div class="overflow-x-auto max-h-64 overflow-y-auto border rounded-md">
                        <table class="min-w-full bg-white text-sm">
                            <thead class="bg-gray-100 sticky top-0">
                                <tr>
                                    <th class="py-2 px-3 text-left">Código</th>
                                    <th class="py-2 px-3 text-left">Descripción</th>
                                    <th class="py-2 px-3 text-center">Cant.</th>
                                    <th class="py-2 px-3 text-right">Costo Unit.</th>
                                    <th class="py-2 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productosSeleccionados as $producto)
                                    <tr class="border-t">
                                        <td class="py-2 px-3 font-mono">{{ $producto['codigo'] }}</td>
                                        <td class="py-2 px-3">{{ $producto['descripcion'] }}</td>
                                        <td class="py-2 px-3 text-center">{{ $producto['cantidad'] }}</td>
                                        <td class="py-2 px-3 text-right">Q{{ number_format((float)$producto['costo'] / 1.12, 2) }}</td>
                                        <td class="py-2 px-3 text-right font-semibold">Q{{ number_format((float)$producto['cantidad'] * ((float)$producto['costo'] / 1.12), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Resumen de Totales --}}
                <div class="bg-blue-50 p-4 rounded-md space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-700">Subtotal (sin IVA):</span>
                        <span class="font-semibold text-gray-800">Q{{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">IVA (12%):</span>
                        <span class="font-semibold text-gray-800">Q{{ number_format($this->iva, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t-2 border-blue-200">
                        <span class="text-lg font-bold text-gray-800">Total de la Compra:</span>
                        <span class="text-2xl font-bold text-blue-600">Q{{ number_format($this->total, 2) }}</span>
                    </div>

                    @if(!empty($precioFactura))
                        <div class="pt-2 border-t border-blue-200 mt-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Precio Factura:</span>
                                <span class="text-sm font-semibold">Q{{ number_format((float)$precioFactura, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium {{ abs($this->diferenciaFactura) < 0.01 ? 'text-green-600' : 'text-red-600' }}">
                                    Diferencia:
                                </span>
                                <span class="text-sm font-bold {{ abs($this->diferenciaFactura) < 0.01 ? 'text-green-600' : 'text-red-600' }}">
                                    Q{{ number_format($this->diferenciaFactura, 2) }}
                                    @if(abs($this->diferenciaFactura) < 0.01)
                                        ✓ Correcto
                                    @else
                                        ⚠ No coincide
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif

                    <p class="text-xs text-gray-500 mt-2 pt-2 border-t border-blue-200">
                        Se crearán {{ count($productosSeleccionados) }} lote(s) en la bodega seleccionada.
                    </p>
                </div>

                {{-- Botones de acción --}}
                <div class="flex justify-end gap-3 mt-6">
                    <button
                        type="button"
                        wire:click="closeModalConfirmacion"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="guardarCompra"
                        wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="guardarCompra">✓ Confirmar y Registrar</span>
                        <span wire:loading wire:target="guardarCompra">
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
    </div>

    {{-- Mensajes de éxito y error --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4">
            <strong>Error:</strong> {{ session('error') }}
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                                <div class="relative" x-data="{ open: @entangle('showCategoriaDropdown').live }" @click.outside="open = false">
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
                        <label class="block text-sm font-medium text-gray-700">
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
                                <div class="relative" x-data="{ open: @entangle('showRegimenDropdown').live }" @click.outside="open = false">
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

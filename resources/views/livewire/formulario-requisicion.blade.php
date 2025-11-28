<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Traslados', 'url' => '/traslados'],
        ['label' => 'Nueva Requisición'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Formulario de Requisición</h1>
    </div>

    {{-- Mensajes de éxito/error --}}
    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Selección de Bodega Origen --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bodega Origen:</label>
                    <div class="relative">
                        @if($selectedOrigen)
                            <div wire:click="clearOrigen"
                                class="flex items-center justify-between mt-1 w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm cursor-pointer hover:border-indigo-400 transition-colors">
                                <span class="font-medium">{{ $selectedOrigen['nombre'] }}</span>
                                <span class="text-gray-400 text-xl">⟲</span>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showOrigenDropdown').live }"
                                @click.outside="open = false">
                                <input type="text" wire:model.live.debounce.300ms="searchOrigen" @click="open = true"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar bodega origen...">
                                <div x-show="open" x-transition
                                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                    <ul>
                                        @foreach ($this->origenResults as $result)
                                            <li wire:click.prevent="selectOrigen('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}', {{ $result['bodega_id'] }})"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $result['nombre'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('selectedOrigen') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Selección de Tarjeta Destino --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Empleado Destino:</label>
                    <div class="relative">
                        @if($selectedDestino)
                            <div wire:click="clearDestino"
                                class="flex items-center justify-between mt-1 w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm cursor-pointer hover:border-indigo-400 transition-colors">
                                <span class="font-medium">{{ $selectedDestino['nombre'] }}</span>
                                <span class="text-gray-400 text-xl">⟲</span>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showDestinoDropdown') }">
                                <input type="text" wire:model.live.debounce.300ms="searchDestino" @click="open = true"
                                    @click.outside="open = false"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar empleado/tarjeta...">
                                <div x-show="open" x-transition @click.away="open = false"
                                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                    <ul>
                                        @forelse ($this->destinoResults as $result)
                                            <li wire:click.prevent="selectDestino('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}', {{ $result['persona_id'] }}, {{ $result['tarjeta_id'] ?? 'null' }}, {{ $result['tiene_tarjeta'] ? 'true' : 'false' }})"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                <div class="font-medium">{{ $result['nombre'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $result['tipo'] }}</div>
                                            </li>
                                        @empty
                                            <li class="px-3 py-2 text-sm text-gray-500 text-center">
                                                No se encontraron personas
                                            </li>
                                        @endforelse
                                        @if(empty($searchDestino) && count($this->destinoResults) > 0)
                                            <li class="px-3 py-2 text-xs text-gray-500 bg-gray-50 border-t border-gray-200 text-center italic">
                                                Mostrando {{ count($this->destinoResults) }} resultados. Escribe para buscar entre todas las personas...
                                            </li>
                                        @endif
                                    </ul>
                                    {{-- Botón para crear nueva persona --}}
                                    <div class="border-t border-gray-200">
                                        <button type="button" wire:click="$dispatch('abrirModalPersona')"
                                            class="w-full px-3 py-2 text-left text-blue-600 hover:bg-blue-50 font-semibold flex items-center gap-2">
                                            <span>+</span>
                                            <span>Crear nueva persona</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('selectedDestino') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

            </div>

            {{-- Información de requisición --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="numero_serie" class="block text-sm font-medium text-gray-700">Número de Serie:</label>
                    <input type="text" id="numero_serie" wire:model="numeroSerie"
                        class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm"
                        placeholder="Ej: ABC123">
                    @error('numeroSerie')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="correlativo" class="block text-sm font-medium text-gray-700">Correlativo:</label>
                    <input type="text" id="correlativo" wire:model="correlativo"
                        class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm"
                        placeholder="Ej: 001">
                    @error('correlativo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Observaciones --}}
            <div class="mt-6 p-6 bg-gray-50 rounded-lg">
                <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones:</label>
                <textarea id="observaciones" wire:model="observaciones" rows="3"
                    class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Ingrese observaciones..."></textarea>
            </div>

            {{-- Búsqueda de productos --}}
            <div class="mt-8 pt-4 border-t border-gray-200">
                <label for="searchProducto" class="block text-sm font-medium text-gray-700">Buscar producto:</label>
                @if(!$selectedOrigen)
                    <div class="mt-2 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <p class="text-sm text-yellow-800">Seleccione primero una bodega origen para buscar productos
                            disponibles.</p>
                    </div>
                @else
                    <div class="relative" x-data="{ open: @entangle('showProductoDropdown') }">
                        <input type="text" id="searchProducto" wire:model.live.debounce.300ms="searchProducto"
                            @click="open = true" @click.outside="open = false"
                            wire:keydown.enter.prevent="seleccionarPrimerResultado"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                            placeholder="Buscar por código del producto o descripción...">
                        <div x-show="open" x-transition @click.away="open = false"
                            class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                            <ul>
                                @forelse ($this->productoResults as $producto)
                                    <li wire:click.prevent="selectProducto('{{ $producto['id'] }}')"
                                        class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <span class="font-mono text-gray-500 mr-2">{{ $producto['id'] }}</span>
                                                <span>{{ $producto['descripcion'] }}</span>
                                            </div>
                                            <span class="text-sm text-gray-600">Disponible:
                                                {{ $producto['cantidad_disponible'] }}</span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="px-3 py-2 text-sm text-gray-500 text-center">
                                        @if(empty($searchProducto))
                                            Escribe para buscar productos...
                                        @else
                                            No se encontraron productos
                                        @endif
                                    </li>
                                @endforelse
                                @if(empty($searchProducto) && count($this->productoResults) > 0)
                                    <li class="px-3 py-2 text-xs text-gray-500 bg-gray-50 border-t border-gray-200 text-center italic">
                                        Mostrando {{ count($this->productoResults) }} productos. Escribe para buscar más...
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Lista de Productos Seleccionados --}}
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-800">Productos en la Requisición</h2>
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Código</th>
                                <th class="py-3 px-6 text-left">Descripción</th>
                                <th class="py-3 px-6 text-center">Tipo</th>
                                <th class="py-3 px-6 text-right">Precio Unit.</th>
                                <th class="py-3 px-6 text-center">Cantidad</th>
                                <th class="py-3 px-6 text-center">Disponible</th>
                                <th class="py-3 px-6 text-right">Total</th>
                                <th class="py-3 px-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($productosSeleccionados as $producto)
                                <tr
                                    class="border-b border-gray-200 hover:bg-gray-50 {{ ($producto['es_consumible'] ?? false) ? 'bg-amber-50' : 'bg-blue-50' }}">
                                    <td class="py-3 px-6 text-left font-mono">{{ $producto['id'] }}</td>
                                    <td class="py-3 px-6 text-left">{{ $producto['descripcion'] }}</td>
                                    <td class="py-3 px-6 text-center">
                                        @if($producto['es_consumible'] ?? false)
                                            <span
                                                class="bg-amber-100 text-amber-800 py-1 px-3 rounded-full text-xs font-semibold">
                                                Consumible
                                            </span>
                                        @else
                                            <span
                                                class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                                                No Consumible
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-right">Q{{ number_format($producto['precio'], 2) }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <input
                                            type="number"
                                            wire:model.blur="productosSeleccionados.{{ $loop->index }}.cantidad"
                                            min="1"
                                            max="{{ $producto['cantidad_disponible'] ?? 999999 }}"
                                            placeholder="0"
                                            class="w-24 text-center border-2 border-blue-300 bg-blue-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-semibold {{ $producto['cantidad'] > $producto['cantidad_disponible'] ? 'border-red-500' : '' }}">
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <span
                                            class="text-sm {{ $producto['cantidad'] > $producto['cantidad_disponible'] ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                            {{ $producto['cantidad_disponible'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-right font-semibold">
                                        Q{{ number_format((float) $producto['cantidad'] * (float) $producto['precio'], 2) }}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex justify-center items-center">
                                            <x-action-button type="delete" title="Eliminar producto"
                                                wire:click="eliminarProducto('{{ $producto['id'] }}')" />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if(count($productosSeleccionados) > 0)
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="6" class="py-4 px-6 text-right text-gray-800 uppercase">Subtotal:</td>
                                    <td class="py-4 px-6 text-right text-lg text-gray-800">
                                        Q{{ number_format($this->subtotal, 2) }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Leyenda de tipos de productos --}}
                @if(count($productosSeleccionados) > 0)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Gestión automática por tipo:</p>
                        <div class="space-y-2">
                            <div class="flex items-start gap-2">
                                <div class="w-4 h-4 mt-0.5 bg-blue-100 border border-blue-300 rounded"></div>
                                <div class="flex-1">
                                    <span class="text-sm text-gray-700"><strong>No Consumibles:</strong> Se registran en
                                        <span class="font-mono text-xs bg-gray-200 px-1 rounded">Salida</span> y se agregan
                                        a la tarjeta de responsabilidad (persona responsable de devolverlos)</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <div class="w-4 h-4 mt-0.5 bg-amber-100 border border-amber-300 rounded"></div>
                                <div class="flex-1">
                                    <span class="text-sm text-gray-700"><strong>Consumibles:</strong> Se registran en <span
                                            class="font-mono text-xs bg-gray-200 px-1 rounded">Traslado</span> solo como
                                        registro de quien los retiró (sin responsabilidad de devolución)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('traslados') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                    Cancelar
                </a>
                <button type="button" wire:click="abrirModalConfirmacion" wire:loading.attr="disabled"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="abrirModalConfirmacion">Registrar Requisición</span>
                    <span wire:loading wire:target="abrirModalConfirmacion">Verificando...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Modal de Confirmación de Requisición --}}
    <div x-data="{
            show: @entangle('showModalConfirmacion').live,
            animatingOut: false
         }" x-show="show || animatingOut" x-cloak
        x-init="$watch('show', value => { if (!value) animatingOut = true; })"
        @animationend="if (!show) animatingOut = false"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
        :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
        wire:click.self="closeModalConfirmacion">
        <div class="relative p-6 border w-full max-w-3xl shadow-xl rounded-lg bg-white"
            :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
            @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Confirmar Requisición</h3>
                <button wire:click="closeModalConfirmacion" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                {{-- Información de la requisición --}}
                <div class="bg-gray-50 p-4 rounded-md">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Bodega Origen:</p>
                            <p class="font-semibold">{{ $selectedOrigen['nombre'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Empleado Destino:</p>
                            <p class="font-semibold">{{ $selectedDestino['nombre'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Número de Serie:</p>
                            <p class="font-semibold">{{ $numeroSerie ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Correlativo:</p>
                            <p class="font-semibold">{{ $correlativo ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($observaciones)
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Observaciones:</p>
                            <p class="font-semibold">{{ $observaciones }}</p>
                        </div>
                    @endif
                </div>

                {{-- Resumen de productos --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">Productos a Requisar:</h4>
                    <div class="overflow-x-auto max-h-64 overflow-y-auto border rounded-md">
                        <table class="min-w-full bg-white text-sm">
                            <thead class="bg-gray-100 sticky top-0">
                                <tr>
                                    <th class="py-2 px-3 text-left">Código</th>
                                    <th class="py-2 px-3 text-left">Descripción</th>
                                    <th class="py-2 px-3 text-center">Tipo</th>
                                    <th class="py-2 px-3 text-center">Cant.</th>
                                    <th class="py-2 px-3 text-center">Disponible</th>
                                    <th class="py-2 px-3 text-right">Precio Unit.</th>
                                    <th class="py-2 px-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productosSeleccionados as $producto)
                                    <tr
                                        class="border-t {{ ($producto['es_consumible'] ?? false) ? 'bg-amber-50' : 'bg-blue-50' }}">
                                        <td class="py-2 px-3 font-mono">{{ $producto['id'] }}</td>
                                        <td class="py-2 px-3">{{ $producto['descripcion'] }}</td>
                                        <td class="py-2 px-3 text-center">
                                            @if($producto['es_consumible'] ?? false)
                                                <span
                                                    class="bg-amber-100 text-amber-800 py-1 px-2 rounded-full text-xs font-semibold whitespace-nowrap">
                                                    Consumible
                                                </span>
                                            @else
                                                <span
                                                    class="bg-blue-100 text-blue-800 py-1 px-2 rounded-full text-xs font-semibold whitespace-nowrap">
                                                    No Consumible
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-3 text-center">{{ $producto['cantidad'] }}</td>
                                        <td class="py-2 px-3 text-center text-gray-500">
                                            {{ $producto['cantidad_disponible'] }}
                                        </td>
                                        <td class="py-2 px-3 text-right">Q{{ number_format($producto['precio'], 2) }}</td>
                                        <td class="py-2 px-3 text-right font-semibold">
                                            Q{{ number_format((float) $producto['cantidad'] * (float) $producto['precio'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Total y Resumen --}}
                <div class="bg-blue-50 p-4 rounded-md">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-800">Valor Total de la Requisición:</span>
                        <span class="text-2xl font-bold text-blue-600">Q{{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    @php
                        $consumibles = collect($productosSeleccionados)->filter(fn($p) => $p['es_consumible'] ?? false)->count();
                        $noConsumibles = collect($productosSeleccionados)->filter(fn($p) => !($p['es_consumible'] ?? false))->count();
                    @endphp
                    <div class="mt-3 pt-3 border-t border-blue-200 text-xs text-gray-600 space-y-1">
                        @if($noConsumibles > 0)
                            <p><strong class="text-blue-700">{{ $noConsumibles }} producto(s) no consumible(s)</strong> se
                                registrarán en <span class="font-mono bg-white px-1 rounded">Salida</span> y se agregarán a
                                la tarjeta de responsabilidad</p>
                        @endif
                        @if($consumibles > 0)
                            <p><strong class="text-amber-700">{{ $consumibles }} producto(s) consumible(s)</strong> se
                                registrarán en <span class="font-mono bg-white px-1 rounded">Traslado</span> solo como
                                registro de retiro</p>
                        @endif
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" wire:click="closeModalConfirmacion"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                        Cancelar
                    </button>
                    <button type="button" wire:click="guardarRequisicion" wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="guardarRequisicion">✓ Confirmar y Registrar</span>
                        <span wire:loading wire:target="guardarRequisicion">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
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
    </style>

    {{-- Modal para crear nueva persona --}}
    @livewire('modal-persona')
</div>
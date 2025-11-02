<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Requisiciones', 'url' => '/requisiciones'],
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
                            <div class="flex items-center justify-between mt-1 w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm">
                                <span>{{ $selectedOrigen['nombre'] }}</span>
                                <button type="button" wire:click.prevent="clearOrigen" class="text-gray-400 hover:text-gray-600">
                                    ×
                                </button>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showOrigenDropdown') }">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchOrigen"
                                    @click="open = true"
                                    @click.outside="open = false"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar bodega origen..."
                                >
                                <div x-show="open"
                                     x-transition
                                     @click.away="open = false"
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
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
                            <div class="flex items-center justify-between mt-1 w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm">
                                <div class="flex items-center gap-2">
                                    <span>{{ $selectedDestino['nombre'] }}</span>
                                    @if($selectedDestino['tiene_tarjeta'])
                                        <span class="bg-green-200 text-green-800 py-1 px-2 rounded-full text-xs">Con Tarjeta</span>
                                    @else
                                        <span class="bg-gray-200 text-gray-800 py-1 px-2 rounded-full text-xs">Sin Tarjeta</span>
                                    @endif
                                </div>
                                <button type="button" wire:click.prevent="clearDestino" class="text-gray-400 hover:text-gray-600">
                                    ×
                                </button>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showDestinoDropdown') }">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchDestino"
                                    @click="open = true"
                                    @click.outside="open = false"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar empleado/tarjeta..."
                                >
                                <div x-show="open"
                                     x-transition
                                     @click.away="open = false"
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                                    <ul>
                                        @foreach ($this->destinoResults as $result)
                                            <li wire:click.prevent="selectDestino('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}', {{ $result['persona_id'] }}, {{ $result['tarjeta_id'] ?? 'null' }}, {{ $result['tiene_tarjeta'] ? 'true' : 'false' }})"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center justify-between">
                                                <span>{{ $result['nombre'] }}</span>
                                                @if($result['tiene_tarjeta'])
                                                    <span class="bg-green-200 text-green-800 py-1 px-2 rounded-full text-xs">Con Tarjeta</span>
                                                @else
                                                    <span class="bg-gray-200 text-gray-800 py-1 px-2 rounded-full text-xs">Sin Tarjeta</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('selectedDestino') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

            </div>

            {{-- Correlativo --}}
            <div class="mt-6">
                <label for="correlativo" class="block text-sm font-medium text-gray-700">Correlativo:</label>
                <input
                    type="text"
                    id="correlativo"
                    wire:model="correlativo"
                    class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Ingrese el correlativo...">
            </div>

            {{-- Observaciones --}}
            <div class="mt-6 p-6 bg-gray-50 rounded-lg">
                <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones:</label>
                <textarea
                    id="observaciones"
                    wire:model="observaciones"
                    rows="3"
                    class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Ingrese observaciones..."></textarea>
            </div>

            {{-- Búsqueda de productos --}}
            <div class="mt-8 pt-4 border-t border-gray-200">
                <label for="searchProducto" class="block text-sm font-medium text-gray-700">Buscar producto:</label>
                @if(!$selectedOrigen)
                    <div class="mt-2 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <p class="text-sm text-yellow-800">Seleccione primero una bodega origen para buscar productos disponibles.</p>
                    </div>
                @else
                    <div class="relative" x-data="{ open: @entangle('showProductoDropdown') }">
                        <input
                            type="text"
                            id="searchProducto"
                            wire:model.live.debounce.300ms="searchProducto"
                            @click="open = true"
                            @click.outside="open = false"
                            wire:keydown.enter.prevent="seleccionarPrimerResultado"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                            placeholder="Buscar por ID (0xA1) o descripción..."
                        >
                        <div x-show="open"
                             x-transition
                             @click.away="open = false"
                             class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                            <ul>
                                @foreach ($this->productoResults as $producto)
                                    <li wire:click.prevent="selectProducto({{ $producto['id'] }})"
                                        class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <span class="font-mono text-gray-500 mr-2">0x{{ strtoupper(dechex($producto['id'])) }}</span>
                                                <span>{{ $producto['descripcion'] }}</span>
                                            </div>
                                            <span class="text-sm text-gray-600">Disponible: {{ $producto['cantidad_disponible'] }}</span>
                                        </div>
                                    </li>
                                @endforeach
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
                                <th class="py-3 px-6 text-right">Precio Unit.</th>
                                <th class="py-3 px-6 text-center">Cantidad</th>
                                <th class="py-3 px-6 text-center">Disponible</th>
                                <th class="py-3 px-6 text-right">Total</th>
                                <th class="py-3 px-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($productosSeleccionados as $producto)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left font-mono">0x{{ strtoupper(dechex($producto['id'])) }}</td>
                                    <td class="py-3 px-6 text-left">{{ $producto['descripcion'] }}</td>
                                    <td class="py-3 px-6 text-right">Q{{ number_format($producto['precio'], 2) }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <input
                                            type="number"
                                            wire:model.live="productosSeleccionados.{{ $loop->index }}.cantidad"
                                            wire:change="actualizarCantidad({{ $producto['id'] }}, $event.target.value)"
                                            min="1"
                                            max="{{ $producto['cantidad_disponible'] }}"
                                            class="w-20 text-center border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent {{ $producto['cantidad'] > $producto['cantidad_disponible'] ? 'border-red-500' : '' }}"
                                        >
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="text-sm {{ $producto['cantidad'] > $producto['cantidad_disponible'] ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                            {{ $producto['cantidad_disponible'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-right font-semibold">Q{{ number_format($producto['cantidad'] * $producto['precio'], 2) }}</td>
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
                            @if(count($productosSeleccionados) > 0)
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="5" class="py-4 px-6 text-right text-gray-800 uppercase">Subtotal:</td>
                                    <td class="py-4 px-6 text-right text-lg text-gray-800">Q{{ number_format($this->subtotal, 2) }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('traslados') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                    Cancelar
                </a>
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Completar Requisición</span>
                    <span wire:loading>Guardando...</span>
                </button>
            </div>
        </form>
    </div>
</div>

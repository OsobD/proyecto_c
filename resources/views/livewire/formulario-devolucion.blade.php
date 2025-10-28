<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Devoluciones'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Registrar Devolución de Material</h1>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Origin Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Origen:</label>
                    <div class="relative">
                        @if($selectedOrigen)
                            <div class="flex items-center justify-between mt-1 w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm">
                                <span>{{ $selectedOrigen['nombre'] }} ({{ $selectedOrigen['tipo'] }})</span>
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
                                    placeholder="Buscar origen..."
                                >
                                <div x-show="open"
                                     x-transition
                                     @click.away="open = false"
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                                    <ul>
                                        @foreach ($this->origenResults as $result)
                                            <li wire:click.prevent="selectOrigen('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}')"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $result['nombre'] }} ({{ $result['tipo'] }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Destination Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Destino:</label>
                    <div class="relative">
                        @if($selectedDestino)
                            <div class="flex items-center justify-between mt-1 w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm">
                                <span>{{ $selectedDestino['nombre'] }} ({{ $selectedDestino['tipo'] }})</span>
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
                                    placeholder="Buscar destino..."
                                >
                                <div x-show="open"
                                     x-transition
                                     @click.away="open = false"
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                                    <ul>
                                        @foreach ($this->destinoResults as $result)
                                            <li wire:click.prevent="selectDestino('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}')"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $result['nombre'] }} ({{ $result['tipo'] }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
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

            {{-- Motivo de la Devolución --}}
            <div class="mt-6 p-6 bg-gray-50 rounded-lg">
                <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo de la Devolución:</label>
                <textarea
                    id="motivo"
                    wire:model="motivo"
                    rows="3"
                    class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Describa el motivo de la devolución..."></textarea>
            </div>

            {{-- Búsqueda de productos --}}
            <div class="mt-8 pt-4 border-t border-gray-200">
                <label for="searchProducto" class="block text-sm font-medium text-gray-700">Buscar producto:</label>
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
                                    class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center">
                                    <span class="font-mono text-gray-500 mr-2">0x{{ strtoupper(dechex($producto['id'])) }}</span>
                                    <span>{{ $producto['descripcion'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Lista de Productos Seleccionados --}}
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-800">Productos a Devolver</h2>
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Código</th>
                                <th class="py-3 px-6 text-left">Descripción</th>
                                <th class="py-3 px-6 text-right">Precio Unit.</th>
                                <th class="py-3 px-6 text-center">Cantidad</th>
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
                                            class="w-20 text-center border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        >
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
                                    <td colspan="4" class="py-4 px-6 text-right text-gray-800 uppercase">Subtotal:</td>
                                    <td class="py-4 px-6 text-right text-lg text-gray-800">Q{{ number_format($this->subtotal, 2) }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Registrar Devolución
                </button>
            </div>
        </form>
    </div>
</div>

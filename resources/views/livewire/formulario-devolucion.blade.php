<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Devoluciones', 'url' => route('devoluciones.historial')],
        ['label' => 'Nueva Devolución'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Registrar Devolución de Material</h1>
    </div>

    {{-- Mensajes Flash --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <div class="font-medium mb-2">Por favor corrija los siguientes errores:</div>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form wire:submit.prevent="save">
            {{-- Correlativo --}}
            <div class="mb-6">
                <label for="correlativo" class="block text-sm font-medium text-gray-700">Número de Correlativo:</label>
                <input
                    type="text"
                    id="correlativo"
                    wire:model="correlativo"
                    class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Ej: DEV-2025-001">
            </div>

            {{-- Origen y Destino --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                {{-- Origen --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Origen (Persona que devuelve):</label>
                    <div class="relative">
                        @if($selectedOrigen)
                            <div class="flex items-center justify-between mt-1 w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm">
                                <span>{{ $selectedOrigen['nombre'] }}</span>
                                <button type="button" wire:click.prevent="clearOrigen" class="text-gray-400 hover:text-gray-600">
                                    ×
                                </button>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showOrigenDropdown') }" @click.outside="open = false">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchOrigen"
                                    @click="open = true"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar persona...">
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                                    <ul>
                                        @forelse ($this->origenResults as $result)
                                            <li wire:click.prevent="selectOrigen('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}')"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $result['nombre'] }}
                                            </li>
                                        @empty
                                            <li class="px-3 py-2 text-gray-500 text-center">No hay resultados</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Destino --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Destino (Bodega):</label>
                    <div class="relative">
                        @if($selectedDestino)
                            <div class="flex items-center justify-between mt-1 w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm">
                                <span>{{ $selectedDestino['nombre'] }}</span>
                                <button type="button" wire:click.prevent="clearDestino" class="text-gray-400 hover:text-gray-600">
                                    ×
                                </button>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showDestinoDropdown') }" @click.outside="open = false">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchDestino"
                                    @click="open = true"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar bodega...">
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                                    <ul>
                                        @forelse ($this->destinoResults as $result)
                                            <li wire:click.prevent="selectDestino('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}')"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $result['nombre'] }}
                                            </li>
                                        @empty
                                            <li class="px-3 py-2 text-gray-500 text-center">No hay resultados</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Motivo de la Devolución --}}
            <div class="mt-6 p-6 bg-gray-50 rounded-lg">
                <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo / Observaciones de la Devolución:</label>
                <textarea
                    id="motivo"
                    wire:model="motivo"
                    rows="3"
                    class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Describa detalladamente el motivo de la devolución..."></textarea>
            </div>

            {{-- Búsqueda de productos --}}
            <div class="mt-8 pt-4 border-t border-gray-200">
                <label for="searchProducto" class="block text-sm font-medium text-gray-700">Buscar producto:</label>
                <div class="relative" x-data="{ open: @entangle('showProductoDropdown') }" @click.outside="open = false">
                    <input
                        type="text"
                        id="searchProducto"
                        wire:model.live.debounce.300ms="searchProducto"
                        @click="open = true"
                        wire:keydown.enter.prevent="seleccionarPrimerResultado"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                        placeholder="Buscar por código (#aaa) o descripción...">
                    <div x-show="open"
                         x-transition
                         class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                        <ul>
                            @forelse ($this->productoResults as $producto)
                                <li wire:click.prevent="selectProducto('{{ $producto['id'] }}')"
                                    class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                    <div class="flex items-center">
                                        <span class="font-mono text-gray-500 mr-2">#{{ $producto['id'] }}</span>
                                        <span>{{ $producto['descripcion'] }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="px-3 py-2 text-gray-500 text-center">
                                    @if(!$selectedOrigen)
                                        Seleccione primero el origen
                                    @else
                                        No hay productos disponibles
                                    @endif
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Lista de Productos Seleccionados --}}
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-800">Productos a Devolver</h2>
                @if(count($productosSeleccionados) > 0)
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
                                @foreach($productosSeleccionados as $index => $producto)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6 text-left font-mono">#{{ $producto['id'] }}</td>
                                        <td class="py-3 px-6 text-left">{{ $producto['descripcion'] }}</td>
                                        <td class="py-3 px-6 text-right">
                                            Q{{ number_format($producto['precio'], 2) }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <input
                                                type="number"
                                                wire:model.live="productosSeleccionados.{{ $index }}.cantidad"
                                                wire:change="actualizarCantidad('{{ $producto['id'] }}', $event.target.value)"
                                                min="1"
                                                class="w-20 text-center border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        </td>
                                        <td class="py-3 px-6 text-right font-semibold">
                                            Q{{ number_format($producto['cantidad'] * $producto['precio'], 2) }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <button
                                                type="button"
                                                wire:click="eliminarProducto('{{ $producto['id'] }}')"
                                                class="text-red-600 hover:text-red-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="py-3 px-6 text-right font-bold text-gray-700">Total de la Devolución:</td>
                                    <td class="py-3 px-6 text-right font-bold text-lg text-gray-800">
                                        Q{{ number_format($this->subtotal, 2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="mt-4 text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <p class="text-gray-500">No hay productos agregados</p>
                        <p class="text-gray-400 text-sm mt-2">Busque y seleccione productos para agregar a la devolución</p>
                    </div>
                @endif
            </div>

            {{-- Botones de Acción --}}
            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('devoluciones.historial') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-lg">
                    Cancelar
                </a>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="save">Registrar Devolución</span>
                    <span wire:loading wire:target="save">Procesando...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Traslados', 'url' => route('traslados')],
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
            {{-- Origen y Destino --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Origen --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Origen (Persona que devuelve):</label>
                    <div class="relative">
                        @if($selectedOrigen)
                            <div wire:click="clearOrigen" class="flex items-center justify-between mt-1 w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm cursor-pointer hover:border-indigo-400 transition-colors">
                                <span class="font-medium">{{ $selectedOrigen['nombre'] }}</span>
                                <span class="text-gray-400 text-xl">⟲</span>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showOrigenDropdown').live }" @click.outside="open = false">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchOrigen"
                                    @click="open = true"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar persona...">
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                    <ul>
                                        @forelse ($this->origenResults as $result)
                                            <li wire:click.prevent="selectOrigen('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}', {{ json_encode($result['tarjetas'] ?? []) }})"
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
                            <div wire:click="clearDestino" class="flex items-center justify-between mt-1 w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm cursor-pointer hover:border-indigo-400 transition-colors">
                                <span class="font-medium">{{ $selectedDestino['nombre'] }}</span>
                                <span class="text-gray-400 text-xl">⟲</span>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showDestinoDropdown').live }" @click.outside="open = false">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchDestino"
                                    @click="open = true"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar bodega...">
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
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

            {{-- Correlativo y Número de Serie --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="numero_serie" class="block text-sm font-medium text-gray-700">Número de Serie:</label>
                    <input
                        type="text"
                        id="numero_serie"
                        wire:model="no_serie"
                        class="mt-1 block w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm"
                        placeholder="Ej: ABC123">
                    @error('no_serie')
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
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Código</th>
                                <th class="py-3 px-6 text-left">Descripción</th>
                                <th class="py-3 px-6 text-center">Tipo</th>
                                <th class="py-3 px-6 text-right">Precio Unit.</th>
                                <th class="py-3 px-6 text-center">Cantidad</th>
                                <th class="py-3 px-6 text-right">Total</th>
                                <th class="py-3 px-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($productosSeleccionados as $index => $producto)
                                <tr class="border-b border-gray-200 hover:bg-gray-50 {{ ($producto['es_consumible'] ?? false) ? 'bg-amber-50' : 'bg-blue-50' }}">
                                    <td class="py-3 px-6 text-left font-mono">#{{ $producto['id'] }}</td>
                                    <td class="py-3 px-6 text-left">{{ $producto['descripcion'] }}</td>
                                    <td class="py-3 px-6 text-center">
                                        @if($producto['es_consumible'] ?? false)
                                            <span class="bg-amber-200 text-amber-800 py-1 px-3 rounded-full text-xs font-semibold whitespace-nowrap">
                                                Consumible
                                            </span>
                                        @else
                                            <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold whitespace-nowrap">
                                                No Consumible
                                            </span>
                                        @endif
                                    </td>
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

                {{-- Leyenda de tipos de productos --}}
                @if(count($productosSeleccionados) > 0)
                    <div class="mt-4 flex gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded"></div>
                            <span class="text-gray-700">No Consumible: Se retira de tarjeta de responsabilidad</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-amber-100 border border-amber-300 rounded"></div>
                            <span class="text-gray-700">Consumible: Solo registro de devolución</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Botones de Acción --}}
            <div class="flex justify-end mt-8">
                <button
                    type="button"
                    wire:click="abrirModalConfirmacion"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Registrar Devolución
                </button>
            </div>
        </form>
    </div>

    {{-- Modal de Confirmación --}}
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

        <div class="relative p-6 border w-full max-w-4xl shadow-xl rounded-lg bg-white max-h-[90vh] overflow-y-auto"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>

                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Confirmar Devolución</h2>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-600">Origen (Persona que devuelve):</p>
                            <p class="font-semibold">{{ $selectedOrigen['nombre'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Destino (Bodega):</p>
                            <p class="font-semibold">{{ $selectedDestino['nombre'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Número de Serie:</p>
                            <p class="font-semibold">{{ $no_serie ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Correlativo:</p>
                            <p class="font-semibold">{{ $correlativo ?: 'N/A' }}</p>
                        </div>
                    </div>

                    @if($motivo)
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">Motivo / Observaciones:</p>
                        <p class="font-semibold">{{ $motivo }}</p>
                    </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Productos a Devolver</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 text-left text-sm">Código</th>
                                        <th class="py-2 px-4 text-left text-sm">Descripción</th>
                                        <th class="py-2 px-4 text-center text-sm">Tipo</th>
                                        <th class="py-2 px-4 text-right text-sm">Cantidad</th>
                                        <th class="py-2 px-4 text-right text-sm">Precio Unit.</th>
                                        <th class="py-2 px-4 text-right text-sm">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productosSeleccionados as $producto)
                                        <tr class="border-b {{ ($producto['es_consumible'] ?? false) ? 'bg-amber-50' : 'bg-blue-50' }}">
                                            <td class="py-2 px-4 text-sm font-mono">#{{ $producto['id'] }}</td>
                                            <td class="py-2 px-4 text-sm">{{ $producto['descripcion'] }}</td>
                                            <td class="py-2 px-4 text-sm text-center">
                                                @if($producto['es_consumible'] ?? false)
                                                    <span class="bg-amber-200 text-amber-800 py-1 px-3 rounded-full text-xs font-semibold whitespace-nowrap">
                                                        Consumible
                                                    </span>
                                                @else
                                                    <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold whitespace-nowrap">
                                                        No Consumible
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 text-sm text-right">{{ $producto['cantidad'] }}</td>
                                            <td class="py-2 px-4 text-sm text-right">Q{{ number_format($producto['precio'], 2) }}</td>
                                            <td class="py-2 px-4 text-sm text-right font-semibold">Q{{ number_format($producto['cantidad'] * $producto['precio'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-gray-50 font-bold">
                                        <td colspan="5" class="py-3 px-4 text-right text-gray-800">TOTAL:</td>
                                        <td class="py-3 px-4 text-right text-lg text-blue-600">Q{{ number_format($this->subtotal, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Leyenda en el modal --}}
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-semibold text-gray-700 mb-2">Devolución de productos:</p>
                            <ul class="text-xs text-gray-600 space-y-1">
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 font-bold">•</span>
                                    <span><strong>No Consumibles:</strong> Se retirarán de la tarjeta de responsabilidad de {{ $selectedOrigen['nombre'] ?? 'la persona seleccionada' }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-amber-600 font-bold">•</span>
                                    <span><strong>Consumibles:</strong> Solo quedará registro de la devolución</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <button
                            type="button"
                            wire:click="closeModalConfirmacion"
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold">
                            Cancelar
                        </button>
                        <button
                            type="button"
                            wire:click="save"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">Confirmar Devolución</span>
                            <span wire:loading wire:target="save">Procesando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

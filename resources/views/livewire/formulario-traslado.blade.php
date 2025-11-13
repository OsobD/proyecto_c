<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Traslados', 'url' => route('traslados')],
        ['label' => 'Nuevo Traslado'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Formulario de Traslado entre Bodegas</h1>
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

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Selección de Origen (Solo Bodegas) --}}
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
                            <div class="relative" x-data="{ open: @entangle('showOrigenDropdown') }" @click.outside="open = false">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchOrigen"
                                    @click="open = true"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar bodega origen..."
                                >
                                <div x-show="open"
                                     x-transition
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
                </div>

                {{-- Selección de Destino (Solo Bodegas) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bodega Destino:</label>
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
                                    placeholder="Buscar bodega destino..."
                                >
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                                    <ul>
                                        @foreach ($this->destinoResults as $result)
                                            <li wire:click.prevent="selectDestino('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}', {{ $result['bodega_id'] }})"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $result['nombre'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Selección de Persona Responsable --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Persona Responsable:</label>
                    <div class="relative">
                        @if($selectedPersona)
                            <div class="flex items-center justify-between mt-1 w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm">
                                <span>{{ $selectedPersona['nombre_completo'] }}</span>
                                <button type="button" wire:click.prevent="clearPersona" class="text-gray-400 hover:text-gray-600">
                                    ×
                                </button>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showPersonaDropdown') }" @click.outside="open = false">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchPersona"
                                    @click="open = true"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                    placeholder="Buscar persona responsable..."
                                >
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                                    <ul>
                                        @foreach ($this->personaResults as $result)
                                            <li wire:click.prevent="selectPersona({{ $result['id'] }}, '{{ $result['nombre_completo'] }}')"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $result['nombre_completo'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Los productos no consumibles se asignarán a la tarjeta de responsabilidad de esta persona
                    </p>
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
                <div class="relative" x-data="{ open: @entangle('showProductoDropdown') }" @click.outside="open = false">
                    <input
                        type="text"
                        id="searchProducto"
                        wire:model.live.debounce.300ms="searchProducto"
                        @click="open = true"
                        wire:keydown.enter.prevent="seleccionarPrimerResultado"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                        placeholder="Buscar por ID (0xA1) o descripción..."
                    >
                    <div x-show="open"
                         x-transition
                         class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                        <ul>
                            @forelse ($this->productoResults as $producto)
                                <li wire:click.prevent="selectProducto({{ $producto['id'] }})"
                                    class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="font-mono text-gray-500 mr-2">#{{ $producto['id'] }}</span>
                                            <span>{{ $producto['descripcion'] }}</span>
                                        </div>
                                        <span class="text-sm text-gray-500">Stock: {{ $producto['cantidad_disponible'] }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="px-3 py-2 text-gray-500 text-center">
                                    @if(!$this->selectedOrigen)
                                        Seleccione primero una bodega de origen
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
                <h2 class="text-lg font-semibold text-gray-800">Productos a Trasladar</h2>
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
                                    <td class="py-3 px-6 text-left">
                                        {{ $producto['descripcion'] }}
                                        <span class="text-xs text-gray-500">(Disponible: {{ $producto['cantidad_disponible'] }})</span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        @if($producto['es_consumible'] ?? false)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                Consumible
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                No Consumible
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-right">Q{{ number_format((float)$producto['precio'], 2) }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <input
                                            type="number"
                                            wire:model.live="productosSeleccionados.{{ $index }}.cantidad"
                                            wire:change="actualizarCantidad({{ $producto['id'] }}, $event.target.value)"
                                            min="1"
                                            max="{{ $producto['cantidad_disponible'] }}"
                                            class="w-20 text-center border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        >
                                    </td>
                                    <td class="py-3 px-6 text-right font-semibold">Q{{ number_format((int)$producto['cantidad'] * (float)$producto['precio'], 2) }}</td>
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
                                    <td class="py-4 px-6 text-right text-lg text-gray-800">Q{{ number_format((float)$this->subtotal, 2) }}</td>
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
                            <span class="text-gray-700">No Consumible: Se asigna a tarjeta de responsabilidad</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-amber-100 border border-amber-300 rounded"></div>
                            <span class="text-gray-700">Consumible: Solo registro de quien lo retiró</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-8 flex justify-end">
                <button
                    type="button"
                    wire:click="abrirModalConfirmacion"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Completar Traslado
                </button>
            </div>
        </form>
    </div>

    {{-- Modal de Confirmación --}}
    @if($showModalConfirmacion)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             x-data="{ show: @entangle('showModalConfirmacion') }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto"
                 @click.away="$wire.closeModalConfirmacion()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">

                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Confirmar Traslado</h2>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-600">Bodega Origen:</p>
                            <p class="font-semibold">{{ $selectedOrigen['nombre'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Bodega Destino:</p>
                            <p class="font-semibold">{{ $selectedDestino['nombre'] ?? 'N/A' }}</p>
                        </div>
                        @if($selectedPersona)
                        <div>
                            <p class="text-sm text-gray-600">Persona Responsable:</p>
                            <p class="font-semibold">{{ $selectedPersona['nombre_completo'] }}</p>
                        </div>
                        @endif
                        @if($correlativo)
                        <div>
                            <p class="text-sm text-gray-600">Correlativo:</p>
                            <p class="font-semibold">{{ $correlativo }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Estado:</p>
                            <p class="font-semibold text-yellow-600">Pendiente</p>
                        </div>
                    </div>

                    @if($observaciones)
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">Observaciones:</p>
                        <p class="font-semibold">{{ $observaciones }}</p>
                    </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Productos</h3>
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
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                        Consumible
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        No Consumible
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 text-sm text-right">{{ $producto['cantidad'] }}</td>
                                            <td class="py-2 px-4 text-sm text-right">Q{{ number_format((float)$producto['precio'], 2) }}</td>
                                            <td class="py-2 px-4 text-sm text-right font-semibold">Q{{ number_format((int)$producto['cantidad'] * (float)$producto['precio'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-gray-50 font-bold">
                                        <td colspan="5" class="py-3 px-4 text-right text-gray-800">TOTAL:</td>
                                        <td class="py-3 px-4 text-right text-lg text-blue-600">Q{{ number_format((float)$this->subtotal, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Leyenda en el modal --}}
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-semibold text-gray-700 mb-2">Asignación de productos:</p>
                            <ul class="text-xs text-gray-600 space-y-1">
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 font-bold">•</span>
                                    <span><strong>No Consumibles:</strong> Se agregarán a la tarjeta de responsabilidad de {{ $selectedPersona['nombre_completo'] ?? 'la persona seleccionada' }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-amber-600 font-bold">•</span>
                                    <span><strong>Consumibles:</strong> Solo quedará registro de quien los retiró, sin responsabilidad de devolución</span>
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
                            wire:click="guardarTraslado"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold disabled:opacity-50">
                            <span wire:loading.remove wire:target="guardarTraslado">Confirmar Traslado</span>
                            <span wire:loading wire:target="guardarTraslado">Procesando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

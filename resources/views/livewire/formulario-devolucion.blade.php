<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Devoluciones', 'url' => route('devoluciones.historial')],
        ['label' => 'Nueva Devolución'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Registrar Devolución de Material</h1>
        <a href="{{ route('devoluciones.historial') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-md transition duration-150">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Ver Historial
        </a>
    </div>

    {{-- Mensajes de éxito/error --}}
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-r-lg shadow-sm" role="alert">
            <div class="font-medium mb-2">Por favor corrija los siguientes errores:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <form wire:submit.prevent="save">
            {{-- Encabezado del formulario --}}
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Información de la Devolución</h2>
            </div>

            <div class="p-6 space-y-6">
                {{-- Tipo de Devolución --}}
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 p-6 rounded-xl border border-purple-200">
                    <label class="block text-sm font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Tipo de Devolución
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($this->tiposDevolucion as $tipo)
                            <label class="relative flex items-center p-4 bg-white rounded-lg border-2 cursor-pointer transition-all
                                {{ $tipoDevolucion === $tipo['value'] ? 'border-purple-500 bg-purple-50 shadow-md' : 'border-gray-200 hover:border-purple-300 hover:shadow' }}">
                                <input type="radio"
                                       wire:model.live="tipoDevolucion"
                                       value="{{ $tipo['value'] }}"
                                       class="form-radio h-5 w-5 text-purple-600">
                                <span class="ml-3 font-medium {{ $tipoDevolucion === $tipo['value'] ? 'text-purple-700' : 'text-gray-700' }}">
                                    {{ $tipo['nombre'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Razón y Correlativo en una fila --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Razón de la Devolución --}}
                    <div>
                        <label for="razon_devolucion" class="block text-sm font-bold text-gray-800 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Razón de la Devolución
                        </label>
                        <select id="razon_devolucion"
                                wire:model="selectedRazonDevolucionId"
                                class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-150">
                            <option value="">Seleccione una razón...</option>
                            @foreach($this->razonesDevolucion as $razon)
                                <option value="{{ $razon['id'] }}">{{ $razon['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Correlativo --}}
                    <div>
                        <label for="correlativo" class="block text-sm font-bold text-gray-800 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            Número de Correlativo
                        </label>
                        <input
                            type="text"
                            id="correlativo"
                            wire:model="correlativo"
                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-150"
                            placeholder="Ej: DEV-2025-001">
                    </div>
                </div>

                {{-- Origen y Destino --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Origen --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Origen (Persona que devuelve)
                        </label>
                        <div class="relative">
                            @if($selectedOrigen)
                                <div class="flex items-center justify-between px-4 py-3 bg-blue-50 border-2 border-blue-300 rounded-lg shadow-sm">
                                    <span class="font-medium text-blue-900">{{ $selectedOrigen['nombre'] }}</span>
                                    <button type="button" wire:click.prevent="clearOrigen"
                                            class="text-blue-600 hover:text-blue-800 font-bold text-xl">
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
                                        class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                                        placeholder="Buscar persona...">
                                    <div x-show="open"
                                         x-transition
                                         @click.away="open = false"
                                         class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-2 max-h-60 overflow-y-auto shadow-xl">
                                        <ul>
                                            @forelse ($this->origenResults as $result)
                                                <li wire:click.prevent="selectOrigen('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}')"
                                                    class="px-4 py-3 cursor-pointer hover:bg-blue-50 border-b border-gray-100 last:border-b-0 transition duration-150">
                                                    {{ $result['nombre'] }}
                                                </li>
                                            @empty
                                                <li class="px-4 py-3 text-gray-500 text-center">No hay resultados</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Destino --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Destino (Bodega)
                        </label>
                        <div class="relative">
                            @if($selectedDestino)
                                <div class="flex items-center justify-between px-4 py-3 bg-green-50 border-2 border-green-300 rounded-lg shadow-sm">
                                    <span class="font-medium text-green-900">{{ $selectedDestino['nombre'] }}</span>
                                    <button type="button" wire:click.prevent="clearDestino"
                                            class="text-green-600 hover:text-green-800 font-bold text-xl">
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
                                        class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                        placeholder="Buscar bodega...">
                                    <div x-show="open"
                                         x-transition
                                         @click.away="open = false"
                                         class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-2 max-h-60 overflow-y-auto shadow-xl">
                                        <ul>
                                            @forelse ($this->destinoResults as $result)
                                                <li wire:click.prevent="selectDestino('{{ $result['id'] }}', '{{ $result['nombre'] }}', '{{ $result['tipo'] }}')"
                                                    class="px-4 py-3 cursor-pointer hover:bg-green-50 border-b border-gray-100 last:border-b-0 transition duration-150">
                                                    {{ $result['nombre'] }}
                                                </li>
                                            @empty
                                                <li class="px-4 py-3 text-gray-500 text-center">No hay resultados</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Motivo de la Devolución --}}
                <div class="bg-amber-50 p-6 rounded-xl border border-amber-200">
                    <label for="motivo" class="block text-sm font-bold text-gray-800 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Motivo / Observaciones de la Devolución
                    </label>
                    <textarea
                        id="motivo"
                        wire:model="motivo"
                        rows="3"
                        class="block w-full px-4 py-3 border-2 border-amber-300 rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-150 bg-white"
                        placeholder="Describa detalladamente el motivo de la devolución..."></textarea>
                </div>

                {{-- Sección de Productos --}}
                <div class="border-t-4 border-purple-200 pt-6">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 -mx-6 px-6 py-3 mb-6">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Productos a Devolver
                        </h3>
                    </div>

                    {{-- Búsqueda de productos --}}
                    <div class="mb-6">
                        <label for="searchProducto" class="block text-sm font-bold text-gray-800 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Buscar Producto
                        </label>
                        <div class="relative" x-data="{ open: @entangle('showProductoDropdown') }">
                            <input
                                type="text"
                                id="searchProducto"
                                wire:model.live.debounce.300ms="searchProducto"
                                @click="open = true"
                                @click.outside="open = false"
                                wire:keydown.enter.prevent="seleccionarPrimerResultado"
                                class="block w-full px-4 py-3 pr-10 border-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-150"
                                placeholder="Buscar por código (#aaa) o descripción...">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <div x-show="open"
                                 x-transition
                                 @click.away="open = false"
                                 class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-2 max-h-60 overflow-y-auto shadow-xl">
                                <ul>
                                    @forelse ($this->productoResults as $producto)
                                        <li wire:click.prevent="selectProducto('{{ $producto['id'] }}')"
                                            class="px-4 py-3 cursor-pointer hover:bg-purple-50 border-b border-gray-100 last:border-b-0 transition duration-150">
                                            <div class="flex items-center">
                                                <span class="font-mono text-purple-600 font-semibold mr-3">#{{ $producto['id'] }}</span>
                                                <span class="text-gray-700">{{ $producto['descripcion'] }}</span>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="px-4 py-3 text-gray-500 text-center">
                                            @if($tipoDevolucion === 'equipo_no_registrado')
                                                No hay productos disponibles
                                            @else
                                                @if(!$selectedOrigen)
                                                    Seleccione primero el origen
                                                @else
                                                    No hay productos disponibles
                                                @endif
                                            @endif
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de Productos Seleccionados --}}
                    @if(count($productosSeleccionados) > 0)
                        <div class="overflow-x-auto rounded-lg shadow-md">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                                    <tr>
                                        <th class="py-4 px-6 text-left text-sm font-bold uppercase tracking-wider">Código</th>
                                        <th class="py-4 px-6 text-left text-sm font-bold uppercase tracking-wider">Descripción</th>
                                        <th class="py-4 px-6 text-right text-sm font-bold uppercase tracking-wider">Precio Unit.</th>
                                        <th class="py-4 px-6 text-center text-sm font-bold uppercase tracking-wider">Cantidad</th>
                                        <th class="py-4 px-6 text-center text-sm font-bold uppercase tracking-wider">Estado</th>
                                        <th class="py-4 px-6 text-right text-sm font-bold uppercase tracking-wider">Total</th>
                                        <th class="py-4 px-6 text-center text-sm font-bold uppercase tracking-wider">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($productosSeleccionados as $producto)
                                        <tr class="hover:bg-purple-50 transition duration-150">
                                            <td class="py-4 px-6 font-mono text-purple-600 font-semibold">#{{ $producto['id'] }}</td>
                                            <td class="py-4 px-6 text-gray-800">{{ $producto['descripcion'] }}</td>
                                            <td class="py-4 px-6 text-right">
                                                @if($tipoDevolucion === 'equipo_no_registrado')
                                                    <div class="flex items-center justify-end gap-1">
                                                        <span class="text-gray-600 font-semibold">Q</span>
                                                        <input
                                                            type="number"
                                                            wire:model.live="productosSeleccionados.{{ $loop->index }}.precio"
                                                            wire:change="actualizarPrecio('{{ $producto['id'] }}', $event.target.value)"
                                                            step="0.01"
                                                            min="0"
                                                            class="w-28 px-2 py-1 text-right border-2 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                                    </div>
                                                @else
                                                    <span class="font-semibold text-gray-800">Q{{ number_format($producto['precio'], 2) }}</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <input
                                                    type="number"
                                                    wire:model.live="productosSeleccionados.{{ $loop->index }}.cantidad"
                                                    wire:change="actualizarCantidad('{{ $producto['id'] }}', $event.target.value)"
                                                    min="1"
                                                    class="w-20 px-2 py-1 text-center border-2 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <select
                                                    wire:model.live="productosSeleccionados.{{ $loop->index }}.estado"
                                                    wire:change="actualizarEstado('{{ $producto['id'] }}', $event.target.value)"
                                                    class="px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium
                                                    {{ $producto['estado'] === 'bueno' ? 'text-green-700 bg-green-50' : '' }}
                                                    {{ $producto['estado'] === 'regular' ? 'text-yellow-700 bg-yellow-50' : '' }}
                                                    {{ $producto['estado'] === 'malo' ? 'text-red-700 bg-red-50' : '' }}">
                                                    <option value="bueno">Bueno</option>
                                                    <option value="regular">Regular</option>
                                                    <option value="malo">Malo</option>
                                                </select>
                                            </td>
                                            <td class="py-4 px-6 text-right font-bold text-lg text-purple-700">
                                                Q{{ number_format($producto['cantidad'] * $producto['precio'], 2) }}
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <button
                                                    type="button"
                                                    wire:click="eliminarProducto('{{ $producto['id'] }}')"
                                                    class="inline-flex items-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg transition duration-150">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gradient-to-r from-purple-100 to-indigo-100">
                                    <tr>
                                        <td colspan="5" class="py-5 px-6 text-right text-gray-800 font-bold text-lg uppercase">
                                            Total de la Devolución:
                                        </td>
                                        <td class="py-5 px-6 text-right font-bold text-2xl text-purple-700">
                                            Q{{ number_format($this->subtotal, 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-gray-500 text-lg font-medium">No hay productos agregados</p>
                            <p class="text-gray-400 text-sm mt-2">Busque y seleccione productos para agregar a la devolución</p>
                        </div>
                    @endif
                </div>

                {{-- Botones de Acción --}}
                <div class="flex justify-end gap-4 pt-6 border-t-2 border-gray-200">
                    <a href="{{ route('devoluciones.historial') }}"
                       class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-md transition duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar
                    </a>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading.remove wire:target="save" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg wire:loading wire:target="save" class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Registrar Devolución</span>
                        <span wire:loading wire:target="save">Procesando...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

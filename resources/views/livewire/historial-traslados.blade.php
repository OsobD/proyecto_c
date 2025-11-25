<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Traslados', 'url' => route('traslados')],
        ['label' => 'Historial'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Historial de Traslados</h1>
        <div class="flex space-x-2">
            <a href="{{ route('requisiciones') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                + Requisición
            </a>
            <a href="{{ route('traslados') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                + Traslado
            </a>
        </div>
    </div>

    {{-- Mensajes de éxito --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Filtros de Búsqueda</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input
                    type="text"
                    id="search"
                    wire:model.live.debounce.300ms="search"
                    class="block w-full py-3 px-4 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Correlativo, origen, destino...">
            </div>

            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <select
                    id="tipo"
                    wire:model.live="tipoFiltro"
                    class="block w-full py-3 px-4 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="Requisición">Requisición</option>
                    <option value="Traslado">Traslado</option>
                    <option value="Devolución">Devolución</option>
                </select>
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select
                    id="estado"
                    wire:model.live="estadoFiltro"
                    class="block w-full py-3 px-4 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="Completado">Completado</option>
                    <option value="Pendiente">Pendiente</option>
                </select>
            </div>

            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input
                    type="date"
                    id="fecha_inicio"
                    wire:model.live="fechaInicio"
                    class="block w-full py-3 px-4 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input
                    type="date"
                    id="fecha_fin"
                    wire:model.live="fechaFin"
                    class="block w-full py-3 px-4 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex items-end">
                <button
                    wire:click="limpiarFiltros"
                    class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-lg">
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla de Traslados --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">
                Traslados encontrados: <span class="text-blue-600">{{ $this->trasladosFiltrados->total() }}</span>
            </h2>
            <div class="flex items-center gap-2">
                <label for="perPage" class="text-sm font-medium text-gray-700">Mostrar:</label>
                <select
                    id="perPage"
                    wire:model.live="perPage"
                    class="py-2 px-3 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-700">por página</span>
            </div>
        </div>

        {{-- Leyenda --}}
        <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200 flex gap-6 text-sm">
            <div class="flex items-center gap-2">
                <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                    No Consumibles
                </span>
                <span class="text-gray-600">Se agregan a tarjeta de responsabilidad</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="bg-amber-200 text-amber-800 py-1 px-3 rounded-full text-xs font-semibold">
                    Consumibles
                </span>
                <span class="text-gray-600">Solo registro de retiro (sin tarjeta)</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Tipo</th>
                        <th class="py-3 px-6 text-left">Productos</th>
                        <th class="py-3 px-6 text-left">Correlativo</th>
                        <th class="py-3 px-6 text-left">Origen</th>
                        <th class="py-3 px-6 text-left">Destino</th>
                        <th class="py-3 px-6 text-left">Usuario</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-center">Cantidad</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($this->trasladosFiltrados as $traslado)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 {{ !$traslado['activo'] ? 'bg-red-50' : '' }}">
                            <td class="py-3 px-6 text-left">
                                <div class="flex items-center gap-2">
                                    @if($traslado['tipo'] === 'Requisición')
                                        <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                                            Requisición
                                        </span>
                                        @if(!$traslado['activo'])
                                            <span class="bg-red-200 text-red-800 py-1 px-2 rounded-full text-xs font-semibold">
                                                Eliminado
                                            </span>
                                        @endif
                                    @elseif($traslado['tipo'] === 'Traslado')
                                        <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                            Traslado
                                        </span>
                                    @else
                                        <span class="bg-purple-200 text-purple-800 py-1 px-3 rounded-full text-xs font-semibold">
                                            Devolución
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                @if(isset($traslado['tipo_badge']) && isset($traslado['tipo_color']))
                                    <span class="bg-{{ $traslado['tipo_color'] }}-200 text-{{ $traslado['tipo_color'] }}-800 py-1 px-3 rounded-full text-xs font-semibold whitespace-nowrap">
                                        {{ $traslado['tipo_badge'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left font-medium">{{ $traslado['correlativo'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $traslado['origen'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $traslado['destino'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $traslado['usuario'] }}</td>
                            <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($traslado['fecha'])->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="bg-gray-100 text-gray-800 py-1 px-3 rounded-full text-xs font-semibold">
                                    {{ $traslado['productos_count'] }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($traslado['estado'] === 'Completado')
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Completado
                                    </span>
                                @else
                                    <span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if(isset($traslado['agrupado']) && $traslado['agrupado'])
                                        <x-action-button
                                            type="view"
                                            title="Ver detalle"
                                            wire:click="verDetalle({{ json_encode($traslado['ids_agrupados']) }}, {{ json_encode($traslado['tipos_agrupados']) }}, '{{ $traslado['correlativo'] }}')" />
                                    @else
                                        <x-action-button
                                            type="view"
                                            title="Ver detalle"
                                            wire:click="verDetalle({{ $traslado['id'] }}, '{{ $traslado['tipo_clase'] }}')" />
                                        @if($traslado['tipo'] === 'Requisición' && $traslado['activo'])
                                            <x-action-button
                                                type="delete"
                                                title="Solicitar eliminación"
                                                wire:click="abrirModalEliminar({{ $traslado['id'] }}, '{{ $traslado['tipo_clase'] }}')" />
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-gray-500">
                                No se encontraron traslados con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de Visualización de Detalle --}}
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
                <h3 class="text-xl font-bold text-gray-900">Detalle de {{ $movimientoSeleccionado['tipo'] ?? 'Movimiento' }}</h3>
                <button wire:click="closeModalVer" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($movimientoSeleccionado)
                <div class="space-y-4">
                    {{-- Información del movimiento --}}
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Origen:</p>
                                <p class="font-semibold">{{ $movimientoSeleccionado['origen'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Destino:</p>
                                <p class="font-semibold">{{ $movimientoSeleccionado['destino'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Correlativo:</p>
                                <p class="font-semibold">{{ $movimientoSeleccionado['correlativo'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Fecha:</p>
                                <p class="font-semibold">{{ $movimientoSeleccionado['fecha'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if(isset($movimientoSeleccionado['observaciones']) && $movimientoSeleccionado['observaciones'])
                            <div class="mt-4">
                                <p class="text-sm text-gray-600">Observaciones:</p>
                                <p class="font-semibold">{{ $movimientoSeleccionado['observaciones'] }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Detalle de productos --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Productos:</h4>
                        <div class="overflow-x-auto max-h-64 overflow-y-auto border rounded-md">
                            <table class="min-w-full bg-white text-sm">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Código</th>
                                        <th class="py-2 px-3 text-left">Descripción</th>
                                        <th class="py-2 px-3 text-center">Tipo</th>
                                        <th class="py-2 px-3 text-center">Cantidad</th>
                                        <th class="py-2 px-3 text-right">Precio Unit.</th>
                                        <th class="py-2 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($movimientoSeleccionado['productos']) && count($movimientoSeleccionado['productos']) > 0)
                                        @foreach($movimientoSeleccionado['productos'] as $producto)
                                            <tr class="border-t hover:bg-gray-50">
                                                <td class="py-2 px-3 font-mono">{{ $producto['codigo'] }}</td>
                                                <td class="py-2 px-3">{{ $producto['descripcion'] }}</td>
                                                <td class="py-2 px-3 text-center">
                                                    @if(isset($producto['es_consumible']))
                                                        @if($producto['es_consumible'])
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                                Consumible
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                No Consumible
                                                            </span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="py-2 px-3 text-center">{{ $producto['cantidad'] }}</td>
                                                <td class="py-2 px-3 text-right">Q{{ number_format($producto['precio'], 2) }}</td>
                                                <td class="py-2 px-3 text-right font-semibold">Q{{ number_format($producto['subtotal'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="py-4 text-center text-gray-500">No hay productos en este movimiento</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="bg-blue-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total:</span>
                            <span class="text-2xl font-bold text-blue-600">Q{{ number_format($movimientoSeleccionado['total'] ?? 0, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Total de productos: {{ isset($movimientoSeleccionado['productos']) ? count($movimientoSeleccionado['productos']) : 0 }}
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

    {{-- Modal de Solicitud de Eliminación --}}
    <div x-data="{
            show: @entangle('showModalEliminar').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalEliminar">
        <div class="relative p-6 border w-full max-w-md shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Solicitar Eliminación</h3>
                <button wire:click="closeModalEliminar" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Importante:</strong> Esta solicitud será enviada a un administrador para su aprobación. 
                        El registro permanecerá visible en el historial.
                    </p>
                </div>

                <div>
                    <label for="justificacion" class="block text-sm font-medium text-gray-700 mb-2">
                        Justificación <span class="text-red-600">*</span>
                    </label>
                    <textarea
                        id="justificacion"
                        wire:model="justificacionEliminacion"
                        rows="4"
                        class="block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Explique el motivo de la eliminación..."></textarea>
                    @error('justificacionEliminacion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button
                        type="button"
                        wire:click="closeModalEliminar"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="solicitarEliminacion"
                        wire:loading.attr="disabled"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="solicitarEliminacion">Solicitar Eliminación</span>
                        <span wire:loading wire:target="solicitarEliminacion">Enviando...</span>
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
</div>

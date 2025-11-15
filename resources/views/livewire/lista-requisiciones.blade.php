<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Requisiciones'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Lista de Requisiciones</h1>
        <a href="{{ route('requisiciones.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            Nueva Requisición
        </a>
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

    {{-- Filtros y búsqueda --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Búsqueda --}}
            <div class="col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                <input
                    type="text"
                    id="search"
                    wire:model.live.debounce.300ms="search"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Buscar por correlativo o persona...">
            </div>

            {{-- Filtro por tipo --}}
            <div>
                <label for="filtroTipo" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por tipo:</label>
                <select
                    id="filtroTipo"
                    wire:model.live="filtroTipo"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="todos">Todos</option>
                    <option value="salida">Solo No Consumibles (Salidas)</option>
                    <option value="traslado">Solo Consumibles (Traslados)</option>
                </select>
            </div>
        </div>

        {{-- Leyenda --}}
        <div class="mt-4 pt-4 border-t border-gray-200 flex gap-6 text-sm">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    No Consumibles
                </span>
                <span class="text-gray-600">Productos en Salida + Tarjeta</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                    Consumibles
                </span>
                <span class="text-gray-600">Productos en Traslado (sin tarjeta)</span>
            </div>
        </div>
    </div>

    {{-- Tabla de requisiciones --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('correlativo')">
                            Correlativo
                            @if($sortBy === 'correlativo')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('fecha')">
                            Fecha
                            @if($sortBy === 'fecha')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Persona
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bodega
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Productos
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requisiciones as $requisicion)
                        <tr class="hover:bg-gray-50 {{ $requisicion['tipo_color'] === 'blue' ? 'bg-blue-50' : 'bg-amber-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $requisicion['tipo_color'] }}-100 text-{{ $requisicion['tipo_color'] }}-800">
                                    {{ $requisicion['tipo_badge'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-semibold text-gray-900">
                                {{ $requisicion['correlativo'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $requisicion['fecha']->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $requisicion['persona'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $requisicion['bodega'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $requisicion['productos_count'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right text-gray-900">
                                Q{{ number_format($requisicion['total'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <x-action-button
                                        type="view"
                                        wire:click="verDetalle({{ $requisicion['id'] }}, '{{ strtolower($requisicion['tipo']) }}')"
                                        title="Ver detalle" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-semibold">No se encontraron requisiciones</p>
                                    <p class="text-sm mt-1">{{ $search ? 'Intenta con otros términos de búsqueda' : 'Crea tu primera requisición' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Resumen --}}
    @if($requisiciones->isNotEmpty())
        <div class="mt-4 bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">
                Mostrando <strong>{{ $requisiciones->count() }}</strong> requisiciones
                @if($search)
                    que coinciden con "<strong>{{ $search }}</strong>"
                @endif
            </p>
        </div>
    @endif

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
        <div class="relative p-6 border w-full max-w-4xl shadow-xl rounded-lg bg-white max-h-[90vh] overflow-y-auto"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Detalle de Requisición</h3>
                <button wire:click="closeModalVer" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($requisicionSeleccionada)
                {{-- Información General --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-800">Información General</h4>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $requisicionSeleccionada['tipo_color'] }}-100 text-{{ $requisicionSeleccionada['tipo_color'] }}-800">
                            {{ $requisicionSeleccionada['tipo_nombre'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Correlativo:</p>
                            <p class="font-semibold font-mono">{{ $requisicionSeleccionada['correlativo'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Fecha:</p>
                            <p class="font-semibold">{{ $requisicionSeleccionada['fecha'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Persona Responsable:</p>
                            <p class="font-semibold">{{ $requisicionSeleccionada['persona'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Bodega Origen:</p>
                            <p class="font-semibold">{{ $requisicionSeleccionada['bodega'] ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if(isset($requisicionSeleccionada['observaciones']) && $requisicionSeleccionada['observaciones'])
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Observaciones:</p>
                            <p class="font-semibold">{{ $requisicionSeleccionada['observaciones'] }}</p>
                        </div>
                    @endif

                    {{-- Nota explicativa --}}
                    <div class="mt-4 p-3 bg-{{ $requisicionSeleccionada['tipo_color'] }}-50 border border-{{ $requisicionSeleccionada['tipo_color'] }}-200 rounded-lg">
                        <p class="text-xs text-{{ $requisicionSeleccionada['tipo_color'] }}-800">
                            @if($requisicionSeleccionada['tipo_color'] === 'amber')
                                <strong>Productos Consumibles:</strong> Estos productos fueron registrados en el sistema de Traslados.
                                La persona solo retiró los materiales, sin responsabilidad de devolverlos.
                                <strong>No aparecen en la tarjeta de responsabilidad.</strong>
                            @else
                                <strong>Productos No Consumibles:</strong> Estos productos fueron registrados en el sistema de Salidas
                                y agregados a la tarjeta de responsabilidad de {{ $requisicionSeleccionada['persona'] }}.
                                <strong>La persona es responsable de devolverlos.</strong>
                            @endif
                        </p>
                    </div>
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
                                    <th class="py-2 px-3 text-center">Lote</th>
                                    <th class="py-2 px-3 text-center">Cantidad</th>
                                    <th class="py-2 px-3 text-right">Precio Unit.</th>
                                    <th class="py-2 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($requisicionSeleccionada['productos']) && count($requisicionSeleccionada['productos']) > 0)
                                    @foreach($requisicionSeleccionada['productos'] as $producto)
                                        <tr class="border-t hover:bg-gray-50">
                                            <td class="py-2 px-3 font-mono">{{ $producto['codigo'] }}</td>
                                            <td class="py-2 px-3">
                                                {{ $producto['descripcion'] }}
                                                @if($producto['es_consumible'])
                                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                        Consumible
                                                    </span>
                                                @else
                                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        No Consumible
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-3 text-center">#{{ $producto['lote'] }}</td>
                                            <td class="py-2 px-3 text-center">{{ $producto['cantidad'] }}</td>
                                            <td class="py-2 px-3 text-right">Q{{ number_format($producto['precio'], 2) }}</td>
                                            <td class="py-2 px-3 text-right font-semibold">Q{{ number_format($producto['subtotal'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-gray-500">No hay productos en esta requisición</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Total --}}
                <div class="mt-6 bg-blue-50 p-4 rounded-md">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-800">Total:</span>
                        <span class="text-2xl font-bold text-blue-600">Q{{ number_format($requisicionSeleccionada['total'] ?? 0, 2) }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Total de productos: {{ isset($requisicionSeleccionada['productos']) ? count($requisicionSeleccionada['productos']) : 0 }}
                    </p>
                </div>

                {{-- Nota sobre edición --}}
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-4 h-4 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-yellow-800">Las requisiciones no se pueden editar</p>
                            <p class="text-xs text-yellow-700 mt-1">
                                Una vez creada, una requisición es un registro permanente que afecta el inventario y las tarjetas de responsabilidad.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Botón de cerrar --}}
                <div class="flex justify-end mt-6">
                    <button
                        wire:click="closeModalVer"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg">
                        Cerrar
                    </button>
                </div>
            @endif
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

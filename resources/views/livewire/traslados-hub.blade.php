<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Traslados'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Módulo de Traslados</h1>
    </div>

    {{-- Estadísticas Rápidas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Requisiciones</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['requisiciones_mes'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Este mes</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Traslados</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['traslados_mes'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Este mes</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-purple-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Devoluciones</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['devoluciones_mes'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Este mes</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Movimientos</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['total_movimientos'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Este mes</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Accesos Rápidos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('requisiciones') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-blue-600">
            <div class="flex flex-col items-center text-center">
                <div class="bg-blue-100 p-4 rounded-lg mb-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Nueva Requisición</h3>
                <p class="text-sm text-gray-600 mt-1">Solicitar productos</p>
            </div>
        </a>

        <a href="{{ route('traslados.nuevo') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-green-600">
            <div class="flex flex-col items-center text-center">
                <div class="bg-green-100 p-4 rounded-lg mb-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Nuevo Traslado</h3>
                <p class="text-sm text-gray-600 mt-1">Trasladar entre bodegas</p>
            </div>
        </a>

        <a href="{{ route('devoluciones') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-purple-600">
            <div class="flex flex-col items-center text-center">
                <div class="bg-purple-100 p-4 rounded-lg mb-3">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Nueva Devolución</h3>
                <p class="text-sm text-gray-600 mt-1">Devolver productos</p>
            </div>
        </a>

        <a href="{{ route('traslados.historial') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-gray-600">
            <div class="flex flex-col items-center text-center">
                <div class="bg-gray-100 p-4 rounded-lg mb-3">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Historial</h3>
                <p class="text-sm text-gray-600 mt-1">Ver todos los movimientos</p>
            </div>
        </a>
    </div>

    {{-- Movimientos Recientes --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Movimientos Recientes</h2>
            <a href="{{ route('traslados.historial') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Ver todos →
            </a>
        </div>

        {{-- Leyenda --}}
        <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200 flex gap-6 text-sm">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    No Consumibles
                </span>
                <span class="text-gray-600">Se agregan a tarjeta de responsabilidad</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
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
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach($trasladosRecientes as $traslado)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 text-left">
                                @if($traslado['tipo'] === 'Requisición')
                                    <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Requisición
                                    </span>
                                @elseif($traslado['tipo'] === 'Traslado')
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Traslado
                                    </span>
                                @else
                                    <span class="bg-purple-200 text-purple-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Devolución
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left">
                                @if(isset($traslado['tipo_badge']) && isset($traslado['tipo_color']))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $traslado['tipo_color'] }}-100 text-{{ $traslado['tipo_color'] }}-800">
                                        {{ $traslado['tipo_badge'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left font-medium">{{ $traslado['correlativo'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $traslado['origen'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $traslado['destino'] }}</td>
                            <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($traslado['fecha'])->format('d/m/Y') }}</td>
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
                                    <button
                                        wire:click="verDetalle({{ $traslado['id'] }}, '{{ $traslado['tipo_clase'] }}')"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 hover:bg-blue-200"
                                        title="Ver detalle">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
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

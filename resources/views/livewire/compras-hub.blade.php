{{--
    Vista: Hub de Compras
    Descripción: Dashboard del módulo de compras con estadísticas del mes y accesos rápidos
--}}
<div>
    {{-- Navegación de migas de pan --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Compras'],
    ]" />

    {{-- Encabezado del módulo --}}
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Módulo de Compras</h1>
    </div>

    {{-- Mensajes de éxito --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Mensajes de error --}}
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tarjetas de estadísticas: compras del mes, monto total, proveedores activos --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-eemq-horizon">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Compras este Mes</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['total_mes'] }}</p>
                </div>
                <div class="bg-eemq-horizon-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-eemq-horizon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-eemq-horizon-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Monto Total</p>
                    <p class="text-2xl xl:text-3xl font-bold text-gray-800 mt-1">Q{{ number_format($estadisticas['monto_total_mes'], 2) }}</p>
                </div>
                <div class="bg-eemq-horizon-50 p-3 rounded-full">
                    <svg class="w-8 h-8 text-eemq-horizon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-eemq-chambray">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Proveedores Activos</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['proveedores_activos'] }}</p>
                </div>
                <div class="bg-eemq-chambray-50 p-3 rounded-full">
                    <svg class="w-8 h-8 text-eemq-chambray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Accesos Rápidos --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16 mt-12">
        <a href="{{ route('compras.nueva') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-eemq-horizon">
            <div class="flex items-center gap-4">
                <div class="bg-eemq-horizon-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-eemq-horizon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Nueva Compra</h3>
                    <p class="text-xs text-gray-600 mt-0.5">Registrar una nueva compra</p>
                </div>
            </div>
        </a>

        <a href="{{ route('compras.historial') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-eemq-horizon-500">
            <div class="flex items-center gap-4">
                <div class="bg-eemq-horizon-50 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-eemq-horizon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Historial</h3>
                    <p class="text-xs text-gray-600 mt-0.5">Ver todas las compras</p>
                </div>
            </div>
        </a>

        <a href="{{ route('proveedores') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-eemq-chambray">
            <div class="flex items-center gap-4">
                <div class="bg-eemq-chambray-50 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-eemq-chambray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Proveedores</h3>
                    <p class="text-xs text-gray-600 mt-0.5">Gestionar proveedores</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Compras Recientes --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Compras Recientes</h2>
            <a href="{{ route('compras.historial') }}" class="text-eemq-horizon hover:text-blue-800 text-sm font-medium">
                Ver todas →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">No. Factura</th>
                        <th class="py-3 px-6 text-left">Proveedor</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-right">Monto</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach($comprasRecientes as $compra)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 {{ !$compra['activa'] ? 'opacity-50' : '' }}">
                            <td class="py-3 px-6 text-left font-medium">{{ $compra['numero_factura'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $compra['proveedor'] }}</td>
                            <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($compra['fecha'])->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-right font-semibold">Q{{ number_format($compra['monto'], 2) }}</td>
                            <td class="py-3 px-6 text-center">
                                @if($compra['estado'] === 'Completada')
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Completada
                                    </span>
                                @else
                                    <span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <button
                                    wire:click="verDetalle({{ $compra['id'] }})"
                                    class="text-eemq-horizon hover:text-blue-800 font-medium mr-2">
                                    Ver
                                </button>
                                <button
                                    wire:click="editarCompra({{ $compra['id'] }})"
                                    class="text-gray-600 hover:text-gray-800 font-medium">
                                    Editar
                                </button>
                                <div class="flex items-center justify-center gap-4">
                                    <button
                                        wire:click="verDetalle({{ $compra['id'] }})"
                                        class="text-blue-600 hover:text-blue-800 font-medium px-2">
                                        Ver
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    @if($compra['activa'])
                                        <button
                                            wire:click="editarCompra({{ $compra['id'] }})"
                                            class="text-gray-600 hover:text-gray-800 font-medium px-2">
                                            Editar
                                        </button>
                                        <span class="text-gray-300">|</span>
                                        <button
                                            wire:click="abrirModalDesactivar({{ $compra['id'] }})"
                                            class="text-red-600 hover:text-red-800 font-medium px-2">
                                            Desactivar
                                        </button>
                                    @else
                                        <button
                                            wire:click="activarCompra({{ $compra['id'] }})"
                                            class="text-green-600 hover:text-green-800 font-medium px-2">
                                            Activar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de Visualización de Detalle de Compra --}}
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
                <h3 class="text-xl font-bold text-gray-900">Detalle de Compra</h3>
                <button wire:click="closeModalVer" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($compraSeleccionada)
                <div class="space-y-4">
                    {{-- Información de la compra --}}
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Bodega Destino:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['bodega'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Proveedor:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['proveedor'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Número de Factura:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['numero_factura'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Correlativo / Serie:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['correlativo'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Fecha de Compra:</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($compraSeleccionada['fecha'] ?? now())->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Detalle de productos --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Productos en la Compra:</h4>
                        <div class="overflow-x-auto max-h-64 overflow-y-auto border rounded-md">
                            <table class="min-w-full bg-white text-sm">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Código</th>
                                        <th class="py-2 px-3 text-left">Descripción</th>
                                        <th class="py-2 px-3 text-center">Cantidad</th>
                                        <th class="py-2 px-3 text-right">Precio Unit.</th>
                                        <th class="py-2 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($compraSeleccionada['productos']) && count($compraSeleccionada['productos']) > 0)
                                        @foreach($compraSeleccionada['productos'] as $producto)
                                            <tr class="border-t hover:bg-gray-50">
                                                <td class="py-2 px-3 font-mono">{{ $producto['codigo'] }}</td>
                                                <td class="py-2 px-3">{{ $producto['descripcion'] }}</td>
                                                <td class="py-2 px-3 text-center">{{ $producto['cantidad'] }}</td>
                                                <td class="py-2 px-3 text-right">Q{{ number_format($producto['precio'], 2) }}</td>
                                                <td class="py-2 px-3 text-right font-semibold">Q{{ number_format($producto['subtotal'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">No hay productos en esta compra</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="bg-blue-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total de la Compra:</span>
                            <span class="text-2xl font-bold text-blue-600">Q{{ number_format($compraSeleccionada['total'] ?? 0, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Total de productos: {{ isset($compraSeleccionada['productos']) ? count($compraSeleccionada['productos']) : 0 }}
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

    {{-- Modal de Edición de Compra --}}
    <div x-data="{
            show: @entangle('showModalEditar').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalEditar">
        <div class="relative p-6 border w-full max-w-4xl shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Editar Compra</h3>
                <button wire:click="closeModalEditar" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($compraSeleccionada)
                <div class="space-y-4">
                    {{-- Información de la compra --}}
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Bodega Destino:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['bodega'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Proveedor:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['proveedor'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Número de Factura:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['numero_factura'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Correlativo / Serie:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['correlativo'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Fecha de Compra:</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($compraSeleccionada['fecha'] ?? now())->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla editable de productos --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Editar Precios de Productos:</h4>
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border rounded-md">
                            <table class="min-w-full bg-white text-sm">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Código</th>
                                        <th class="py-2 px-3 text-left">Descripción</th>
                                        <th class="py-2 px-3 text-center">Cantidad</th>
                                        <th class="py-2 px-3 text-right">Precio Unit.</th>
                                        <th class="py-2 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($compraSeleccionada['productos']) && count($compraSeleccionada['productos']) > 0)
                                        @foreach($compraSeleccionada['productos'] as $index => $producto)
                                            <tr class="border-t hover:bg-gray-50">
                                                <td class="py-2 px-3 font-mono">{{ $producto['codigo'] }}</td>
                                                <td class="py-2 px-3">{{ $producto['descripcion'] }}</td>
                                                <td class="py-2 px-3 text-center">{{ $producto['cantidad'] }}</td>
                                                <td class="py-2 px-3 text-right">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1">Q</span>
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            wire:model.blur="compraSeleccionada.productos.{{ $index }}.precio"
                                                            min="0"
                                                            class="w-28 text-right border-2 border-blue-300 bg-blue-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-semibold px-2 py-1"
                                                        >
                                                    </div>
                                                </td>
                                                <td class="py-2 px-3 text-right font-semibold">
                                                    Q{{ number_format($producto['cantidad'] * $producto['precio'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">No hay productos en esta compra</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Total actualizado --}}
                    <div class="bg-blue-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total de la Compra:</span>
                            <span class="text-2xl font-bold text-blue-600">
                                Q{{ number_format(collect($compraSeleccionada['productos'])->sum(function($p) { return $p['cantidad'] * $p['precio']; }), 2) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Total de productos: {{ isset($compraSeleccionada['productos']) ? count($compraSeleccionada['productos']) : 0 }}
                        </p>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="flex justify-end gap-3 mt-6">
                        <button
                            wire:click="closeModalEditar"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg">
                            Cancelar
                        </button>
                        <button
                            wire:click="abrirModalConfirmarEdicion"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de Confirmación de Edición --}}
    <div x-data="{
            show: @entangle('showModalConfirmarEdicion').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center"
         style="z-index: 9999 !important;"
         :style="(!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')) + ' z-index: 9999 !important;'"
         wire:click.self="closeModalConfirmarEdicion">
        <div class="relative p-6 border w-full max-w-md shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Confirmar Cambios</h3>
                <button wire:click="closeModalConfirmarEdicion" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-gray-700 mb-4">
                    ¿Está seguro de que desea guardar los cambios realizados en esta compra?
                </p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Esta acción modificará los registros de la compra. Los cambios no se pueden deshacer automáticamente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    wire:click="closeModalConfirmarEdicion"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                    Cancelar
                </button>
                <button
                    wire:click="guardarEdicion"
                    wire:loading.attr="disabled"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="guardarEdicion">Confirmar</span>
                    <span wire:loading wire:target="guardarEdicion">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
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

    {{-- Modal de Visualización de Detalle de Compra --}}
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
                <h3 class="text-xl font-bold text-gray-900">Detalle de Compra</h3>
                <button wire:click="closeModalVer" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($compraSeleccionada)
                <div class="space-y-4">
                    {{-- Información de la compra --}}
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Bodega Destino:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['bodega'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Proveedor:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['proveedor'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Número de Factura:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['numero_factura'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Correlativo / Serie:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['correlativo'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Fecha de Compra:</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($compraSeleccionada['fecha'] ?? now())->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Detalle de productos --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Productos en la Compra:</h4>
                        <div class="overflow-x-auto max-h-64 overflow-y-auto border rounded-md">
                            <table class="min-w-full bg-white text-sm">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Código</th>
                                        <th class="py-2 px-3 text-left">Descripción</th>
                                        <th class="py-2 px-3 text-center">Cantidad</th>
                                        <th class="py-2 px-3 text-right">Precio Unit.</th>
                                        <th class="py-2 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($compraSeleccionada['productos']) && count($compraSeleccionada['productos']) > 0)
                                        @foreach($compraSeleccionada['productos'] as $producto)
                                            <tr class="border-t hover:bg-gray-50">
                                                <td class="py-2 px-3 font-mono">{{ $producto['codigo'] }}</td>
                                                <td class="py-2 px-3">{{ $producto['descripcion'] }}</td>
                                                <td class="py-2 px-3 text-center">{{ $producto['cantidad'] }}</td>
                                                <td class="py-2 px-3 text-right">Q{{ number_format($producto['precio'], 2) }}</td>
                                                <td class="py-2 px-3 text-right font-semibold">Q{{ number_format($producto['subtotal'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">No hay productos en esta compra</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="bg-blue-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total de la Compra:</span>
                            <span class="text-2xl font-bold text-blue-600">Q{{ number_format($compraSeleccionada['total'] ?? 0, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Total de productos: {{ isset($compraSeleccionada['productos']) ? count($compraSeleccionada['productos']) : 0 }}
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

    {{-- Modal de Edición de Compra --}}
    <div x-data="{
            show: @entangle('showModalEditar').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalEditar">
        <div class="relative p-6 border w-full max-w-4xl shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Editar Compra</h3>
                <button wire:click="closeModalEditar" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($compraSeleccionada)
                <div class="space-y-4">
                    {{-- Información de la compra --}}
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Bodega Destino:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['bodega'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Proveedor:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['proveedor'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Número de Factura:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['numero_factura'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Correlativo / Serie:</p>
                                <p class="font-semibold">{{ $compraSeleccionada['correlativo'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Fecha de Compra:</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($compraSeleccionada['fecha'] ?? now())->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla editable de productos --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Editar Productos (Cantidades y Precios):</h4>
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border rounded-md">
                            <table class="min-w-full bg-white text-sm">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Código</th>
                                        <th class="py-2 px-3 text-left">Descripción</th>
                                        <th class="py-2 px-3 text-center">Cantidad</th>
                                        <th class="py-2 px-3 text-right">Precio Unit.</th>
                                        <th class="py-2 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($compraSeleccionada['productos']) && count($compraSeleccionada['productos']) > 0)
                                        @foreach($compraSeleccionada['productos'] as $index => $producto)
                                            <tr class="border-t hover:bg-gray-50">
                                                <td class="py-2 px-3 font-mono">{{ $producto['codigo'] }}</td>
                                                <td class="py-2 px-3">{{ $producto['descripcion'] }}</td>
                                                <td class="py-2 px-3 text-center">
                                                    <input
                                                        type="number"
                                                        step="1"
                                                        wire:model.blur="compraSeleccionada.productos.{{ $index }}.cantidad"
                                                        min="1"
                                                        class="w-20 text-center border-2 border-green-300 bg-green-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent font-semibold px-2 py-1"
                                                    >
                                                </td>
                                                <td class="py-2 px-3 text-right">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1">Q</span>
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            wire:model.blur="compraSeleccionada.productos.{{ $index }}.precio"
                                                            min="0"
                                                            class="w-28 text-right border-2 border-blue-300 bg-blue-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-semibold px-2 py-1"
                                                        >
                                                    </div>
                                                </td>
                                                <td class="py-2 px-3 text-right font-semibold">
                                                    Q{{ number_format($producto['cantidad'] * $producto['precio'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">No hay productos en esta compra</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Total actualizado --}}
                    <div class="bg-blue-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total de la Compra:</span>
                            <span class="text-2xl font-bold text-blue-600">
                                Q{{ number_format(collect($compraSeleccionada['productos'])->sum(function($p) { return $p['cantidad'] * $p['precio']; }), 2) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Total de productos: {{ isset($compraSeleccionada['productos']) ? count($compraSeleccionada['productos']) : 0 }}
                        </p>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="flex justify-end gap-3 mt-6">
                        <button
                            wire:click="closeModalEditar"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg">
                            Cancelar
                        </button>
                        <button
                            wire:click="abrirModalConfirmarEdicion"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de Confirmación de Edición --}}
    <div x-data="{
            show: @entangle('showModalConfirmarEdicion').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center"
         style="z-index: 9999 !important;"
         :style="(!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')) + ' z-index: 9999 !important;'"
         wire:click.self="closeModalConfirmarEdicion">
        <div class="relative p-6 border w-full max-w-md shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Confirmar Cambios</h3>
                <button wire:click="closeModalConfirmarEdicion" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-gray-700 mb-4">
                    ¿Está seguro de que desea guardar los cambios realizados en esta compra?
                </p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Esta acción modificará los registros de la compra. Los cambios no se pueden deshacer automáticamente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    wire:click="closeModalConfirmarEdicion"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                    Cancelar
                </button>
                <button
                    wire:click="guardarEdicion"
                    wire:loading.attr="disabled"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="guardarEdicion">Confirmar</span>
                    <span wire:loading wire:target="guardarEdicion">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Modal de Confirmación de Desactivación --}}
    <div x-data="{
            show: @entangle('showModalConfirmarDesactivar').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalConfirmarDesactivar">
        <div class="relative p-6 border w-full max-w-md shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Confirmar Desactivación</h3>
                <button wire:click="closeModalConfirmarDesactivar" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-gray-700 mb-4">
                    ¿Está seguro de que desea desactivar esta compra?
                </p>
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                Esta compra quedará desactivada y aparecerá con opacidad en el listado. Podrá activarla nuevamente cuando lo desee.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    wire:click="closeModalConfirmarDesactivar"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                    Cancelar
                </button>
                <button
                    wire:click="confirmarDesactivar"
                    wire:loading.attr="disabled"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="confirmarDesactivar">Desactivar</span>
                    <span wire:loading wire:target="confirmarDesactivar">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Desactivando...
                    </span>
                </button>
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

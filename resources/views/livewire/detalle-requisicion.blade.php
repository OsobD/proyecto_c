<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Requisiciones', 'url' => route('requisiciones')],
        ['label' => 'Detalle #' . $correlativo],
    ]" />

    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detalle de Requisición</h1>
        <a href="{{ route('requisiciones') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
            Volver al Listado
        </a>
    </div>

    {{-- Información General --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Información General</h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $tipoColor }}-100 text-{{ $tipoColor }}-800">
                {{ $tipoNombre }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600">Correlativo:</p>
                <p class="font-semibold text-lg font-mono">{{ $correlativo }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Fecha:</p>
                <p class="font-semibold">{{ $requisicion->fecha->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Persona Responsable:</p>
                <p class="font-semibold">{{ $persona }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Bodega Origen:</p>
                <p class="font-semibold">{{ $bodega }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total:</p>
                <p class="font-semibold text-lg text-green-600">Q{{ number_format($requisicion->total, 2) }}</p>
            </div>
            @if($descripcion)
            <div class="col-span-full">
                <p class="text-sm text-gray-600">Observaciones:</p>
                <p class="font-semibold">{{ $descripcion }}</p>
            </div>
            @endif
        </div>

        {{-- Nota explicativa --}}
        <div class="mt-6 p-4 bg-{{ $tipoColor }}-50 border border-{{ $tipoColor }}-200 rounded-lg">
            <p class="text-sm text-{{ $tipoColor }}-800">
                @if($esConsumible)
                    <strong>Productos Consumibles:</strong> Estos productos fueron registrados en el sistema de Traslados.
                    La persona solo retiró los materiales, sin responsabilidad de devolverlos.
                    <strong>No aparecen en la tarjeta de responsabilidad.</strong>
                @else
                    <strong>Productos No Consumibles:</strong> Estos productos fueron registrados en el sistema de Salidas
                    y agregados a la tarjeta de responsabilidad de {{ $persona }}.
                    <strong>La persona es responsable de devolverlos.</strong>
                @endif
            </p>
        </div>
    </div>

    {{-- Detalle de Productos --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Productos</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lote</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requisicion->detalles as $detalle)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-semibold text-gray-900">
                                {{ $detalle->producto->id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $detalle->producto->descripcion }}
                                @if($detalle->producto->es_consumible)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Consumible
                                    </span>
                                @else
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        No Consumible
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                #{{ $detalle->id_lote }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                {{ $detalle->cantidad }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                Q{{ number_format($tipo === 'salida' ? $detalle->precio_salida : $detalle->precio_traslado, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                Q{{ number_format($detalle->cantidad * ($tipo === 'salida' ? $detalle->precio_salida : $detalle->precio_traslado), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No se encontraron productos en esta requisición.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase">
                            Total:
                        </td>
                        <td class="px-6 py-4 text-right text-lg font-bold text-green-600">
                            Q{{ number_format($requisicion->total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Nota sobre edición --}}
    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <p class="font-semibold text-yellow-800">Las requisiciones no se pueden editar</p>
                <p class="text-sm text-yellow-700 mt-1">
                    Una vez creada, una requisición es un registro permanente que afecta el inventario y las tarjetas de responsabilidad.
                    Para corregir errores, debe crear una devolución o un ajuste de inventario.
                </p>
            </div>
        </div>
    </div>
</div>

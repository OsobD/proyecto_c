<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Devoluciones', 'url' => route('devoluciones')],
        ['label' => 'Historial'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Historial de Devoluciones</h1>
        <a href="{{ route('devoluciones') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg">
            + Nueva Devolución
        </a>
    </div>

    {{-- Mensajes de éxito --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Mensajes de error --}}
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Filtros de Búsqueda</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input
                    type="text"
                    id="search"
                    wire:model.live.debounce.300ms="search"
                    class="block w-full py-3 px-4 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Correlativo, bodega...">
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

    {{-- Tabla de Devoluciones --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">
                Devoluciones encontradas: <span class="text-blue-600">{{ $devoluciones->total() }}</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Correlativo</th>
                        <th class="py-3 px-6 text-left">Bodega</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-center">Productos</th>
                        <th class="py-3 px-6 text-right">Total</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($devoluciones as $devolucion)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 text-left font-medium">{{ $devolucion->no_formulario ?? 'DEV-' . $devolucion->id }}</td>
                            <td class="py-3 px-6 text-left">{{ $devolucion->bodega->nombre ?? 'N/A' }}</td>
                            <td class="py-3 px-6 text-left">{{ $devolucion->fecha->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="bg-gray-100 text-gray-800 py-1 px-3 rounded-full text-xs font-semibold">
                                    {{ $devolucion->detalles->count() }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right font-semibold">Q{{ number_format($devolucion->total, 2) }}</td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        wire:click="verDetalle({{ $devolucion->id }})"
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
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                No se encontraron devoluciones con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($devoluciones->hasPages())
            <div class="mt-4">
                {{ $devoluciones->links() }}
            </div>
        @endif
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
                <h3 class="text-xl font-bold text-gray-900">Detalle de Devolución</h3>
                <button wire:click="closeModalVer" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($devolucionSeleccionada)
                {{-- Información General --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Correlativo</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $devolucionSeleccionada['no_formulario'] }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Bodega</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $devolucionSeleccionada['bodega'] }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Fecha</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $devolucionSeleccionada['fecha'] }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Usuario</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $devolucionSeleccionada['usuario'] }}</p>
                    </div>
                </div>

                {{-- Productos --}}
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Productos Devueltos</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 text-left text-xs font-medium text-gray-700 uppercase">Código</th>
                                    <th class="py-2 px-4 text-left text-xs font-medium text-gray-700 uppercase">Descripción</th>
                                    <th class="py-2 px-4 text-center text-xs font-medium text-gray-700 uppercase">Cantidad</th>
                                    <th class="py-2 px-4 text-right text-xs font-medium text-gray-700 uppercase">P. Unit.</th>
                                    <th class="py-2 px-4 text-right text-xs font-medium text-gray-700 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($devolucionSeleccionada['productos'] as $producto)
                                    <tr>
                                        <td class="py-2 px-4 text-sm text-gray-600">{{ $producto['codigo'] }}</td>
                                        <td class="py-2 px-4 text-sm text-gray-800">{{ $producto['descripcion'] }}</td>
                                        <td class="py-2 px-4 text-center text-sm font-semibold text-gray-800">{{ $producto['cantidad'] }}</td>
                                        <td class="py-2 px-4 text-right text-sm text-gray-800">Q{{ number_format($producto['precio_unitario'], 2) }}</td>
                                        <td class="py-2 px-4 text-right text-sm font-semibold text-gray-800">Q{{ number_format($producto['subtotal'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="py-3 px-4 text-right font-bold text-gray-800 uppercase">Total:</td>
                                    <td class="py-3 px-4 text-right font-bold text-lg text-gray-800">Q{{ number_format($devolucionSeleccionada['total'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Botón Cerrar --}}
                <div class="flex justify-end">
                    <button
                        wire:click="closeModalVer"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg">
                        Cerrar
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Compras', 'url' => route('compras')],
        ['label' => 'Historial'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Historial de Compras</h1>
        <a href="{{ route('compras.nueva') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            + Nueva Compra
        </a>
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
                    placeholder="No. Factura o Proveedor...">
            </div>

            <div>
                <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                <select
                    id="proveedor"
                    wire:model.live="proveedorFiltro"
                    class="block w-full py-3 px-4 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor['id'] }}">{{ $proveedor['nombre'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select
                    id="estado"
                    wire:model.live="estadoFiltro"
                    class="block w-full py-3 px-4 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="Completada">Completada</option>
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

    {{-- Tabla de Compras --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">
                Compras encontradas: <span class="text-blue-600">{{ count($comprasFiltradas) }}</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Factura</th>
                        <th class="py-3 px-6 text-left">Serie</th>
                        <th class="py-3 px-6 text-left">Proveedor</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-center">Productos</th>
                        <th class="py-3 px-6 text-right">Monto</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($comprasFiltradas as $compra)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 {{ !$compra['activa'] ? 'opacity-50' : '' }}">
                            <td class="py-3 px-6 text-left font-medium">{{ $compra['numero_factura'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $compra['numero_serie'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $compra['proveedor'] }}</td>
                            <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($compra['fecha'])->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                                    {{ $compra['productos_count'] }}
                                </span>
                            </td>
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
                                            wire:click="desactivarCompra({{ $compra['id'] }})"
                                            onclick="return confirm('¿Está seguro de desactivar esta compra?')"
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
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">
                                No se encontraron compras con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal para editar (placeholder) --}}
    @if($showModalEditar)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModalEditar">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Editar Compra</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Esta funcionalidad requiere permisos de supervisor.
                    </p>
                    <div class="bg-gray-100 p-4 rounded mb-4">
                        <p class="text-sm"><strong>Factura:</strong> {{ $compraSeleccionada['numero_factura'] ?? '' }}</p>
                        <p class="text-sm"><strong>Proveedor:</strong> {{ $compraSeleccionada['proveedor'] ?? '' }}</p>
                        <p class="text-sm"><strong>Monto:</strong> Q{{ number_format($compraSeleccionada['monto'] ?? 0, 2) }}</p>
                    </div>
                    <div class="flex justify-end">
                        <button
                            wire:click="closeModalEditar"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

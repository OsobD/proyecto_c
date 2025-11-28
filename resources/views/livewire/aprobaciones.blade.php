<div>
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Aprobaciones'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Solicitudes de Aprobación</h1>
    </div>

    {{-- Mensajes --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="flex gap-4">
            <button wire:click="$set('filtroEstado', 'PENDIENTE')" 
                    class="px-4 py-2 rounded-lg {{ $filtroEstado === 'PENDIENTE' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                Pendientes
            </button>
            <button wire:click="$set('filtroEstado', 'APROBADO')" 
                    class="px-4 py-2 rounded-lg {{ $filtroEstado === 'APROBADO' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                Aprobadas
            </button>
            <button wire:click="$set('filtroEstado', 'RECHAZADO')" 
                    class="px-4 py-2 rounded-lg {{ $filtroEstado === 'RECHAZADO' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                Rechazadas
            </button>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Módulo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($solicitudes as $solicitud)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $solicitud->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $solicitud->solicitante->nombre_usuario }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $solicitud->tipo === 'EDICION' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800' }}">
                                {{ $solicitud->tipo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst($solicitud->tabla) }} #{{ $solicitud->registro_id }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            {{ $solicitud->observaciones }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $solicitud->estado === 'PENDIENTE' ? 'bg-blue-100 text-blue-800' : 
                                   ($solicitud->estado === 'APROBADO' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ $solicitud->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="verDetalle({{ $solicitud->id }})" class="text-blue-600 hover:text-blue-900">Ver Detalle</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No hay solicitudes {{ strtolower($filtroEstado) }}s.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $solicitudes->links() }}
        </div>
    </div>

    {{-- Modal Detalle --}}
    @if($showModalDetalle && $solicitudSeleccionada)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Detalle de Solicitud #{{ $solicitudSeleccionada->id }}</h3>
                    <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Solicitante</p>
                            <p class="mt-1">{{ $solicitudSeleccionada->solicitante->nombre_usuario }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha</p>
                            <p class="mt-1">{{ $solicitudSeleccionada->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Observaciones</p>
                            <p class="mt-1">{{ $solicitudSeleccionada->observaciones }}</p>
                        </div>
                    </div>

                    @if($solicitudSeleccionada->tipo === 'EDICION' && $solicitudSeleccionada->tabla === 'compra')
                        <h4 class="font-medium text-gray-900 mb-4">Cambios Propuestos</h4>
                        
                        <div class="border rounded-lg overflow-hidden mb-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Producto</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Cant. Actual</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-blue-600">Cant. Nueva</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Precio Actual (c/IVA)</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-blue-600">Precio Nuevo (c/IVA)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($solicitudSeleccionada->datos['productos'] as $prod)
                                        @php
                                            $detalleActual = $solicitudSeleccionada->datos_actuales->detalles->firstWhere('id', $prod['id_detalle']);
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-2 text-sm">{{ $prod['descripcion'] }}</td>
                                            <td class="px-4 py-2 text-center text-sm">{{ $detalleActual->cantidad ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 text-center text-sm font-bold {{ ($detalleActual->cantidad ?? 0) != $prod['cantidad'] ? 'text-blue-600 bg-blue-50' : '' }}">
                                                {{ $prod['cantidad'] }}
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm">Q{{ number_format($detalleActual->precio_ingreso ?? 0, 4) }}</td>
                                            <td class="px-4 py-2 text-right text-sm font-bold {{ ($detalleActual->precio_ingreso ?? 0) != ($prod['precio_con_iva'] ?? $prod['precio']) ? 'text-blue-600 bg-blue-50' : '' }}">
                                                Q{{ number_format($prod['precio_con_iva'] ?? $prod['precio'], 4) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end gap-8 text-lg font-medium">
                            <div class="text-gray-500">
                                Total Actual: Q{{ number_format($solicitudSeleccionada->datos_actuales->total ?? 0, 2) }}
                            </div>
                            <div class="text-blue-600">
                                Nuevo Total: Q{{ number_format($solicitudSeleccionada->datos['total_con_iva'] ?? $solicitudSeleccionada->datos['total'], 2) }}
                            </div>
                        </div>

                    @elseif($solicitudSeleccionada->tipo === 'DESACTIVACION')
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        Se solicita DESACTIVAR este registro. Esta acción ocultará el registro de las vistas principales.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-lg">
                    <button wire:click="cerrarModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    @if($solicitudSeleccionada->estado === 'PENDIENTE')
                        <button wire:click="rechazar({{ $solicitudSeleccionada->id }})" class="px-4 py-2 bg-red-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700">
                            Rechazar
                        </button>
                        <button wire:click="aprobar({{ $solicitudSeleccionada->id }})" class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                            Aprobar y Aplicar
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Aprobaciones Pendientes'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Aprobaciones Pendientes</h1>
    </div>

    {{-- Mensajes de confirmación --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Filtros --}}
        <div class="mb-6">
            <label for="filtro_tipo" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Tipo</label>
            <select wire:model.live="filtroTipo" id="filtro_tipo"
                class="block w-full md:w-64 border-2 border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                <option value="todos">Todos</option>
                <option value="crear">Creaciones</option>
                <option value="editar">Ediciones</option>
                <option value="eliminar">Eliminaciones</option>
            </select>
        </div>

        {{-- Tabla de Aprobaciones --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Acción</th>
                        <th class="py-3 px-6 text-left">Modelo</th>
                        <th class="py-3 px-6 text-left">Número</th>
                        <th class="py-3 px-6 text-left">Solicitante</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-left">Justificación</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($this->pendientesFiltrados as $item)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($item['tipo'] === 'eliminar') bg-red-100 text-red-800
                                        @elseif($item['tipo'] === 'editar') bg-yellow-100 text-yellow-800
                                        @elseif($item['tipo'] === 'crear') bg-green-100 text-green-800
                                        @endif">
                                    {{ $item['accion'] ?? ucfirst($item['tipo']) }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">
                                    {{ $item['modelo'] ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-left font-medium">
                                {{ $item['numero'] }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $item['solicitante'] }}
                            </td>
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <div class="text-sm">
                                    <div>{{ $item['fecha'] }}</div>
                                    @if(isset($item['fecha_completa']))
                                        <div class="text-xs text-gray-500">{{ $item['fecha_completa'] }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-6 text-left">
                                @if(isset($item['justificacion']) && $item['justificacion'])
                                    <div class="max-w-xs">
                                        <p class="text-sm text-gray-700 line-clamp-2" title="{{ $item['justificacion'] }}">
                                            {{ $item['justificacion'] }}
                                        </p>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">Sin justificación</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="aprobar({{ $item['id'] }})" wire:loading.attr="disabled"
                                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="aprobar({{ $item['id'] }})">Aprobar</span>
                                        <span wire:loading wire:target="aprobar({{ $item['id'] }})">...</span>
                                    </button>
                                    <button wire:click="rechazar({{ $item['id'] }})" wire:loading.attr="disabled"
                                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="rechazar({{ $item['id'] }})">Rechazar</span>
                                        <span wire:loading wire:target="rechazar({{ $item['id'] }})">...</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 px-6 text-center text-gray-500">
                                No hay aprobaciones pendientes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
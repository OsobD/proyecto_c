<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Bitácora'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bitácora del Sistema</h1>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label for="search_usuario" class="block text-sm font-medium text-gray-700 mb-2">Buscar por Usuario</label>
                <input type="text" wire:model.live.debounce.300ms="searchUsuario" id="search_usuario"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                    placeholder="Nombre o email...">
            </div>
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                <input type="date" wire:model.live="fechaInicio" id="fecha_inicio"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
            </div>
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                <input type="date" wire:model.live="fechaFin" id="fecha_fin"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
            </div>
        </div>

        {{-- Log Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left font-semibold">Fecha y Hora</th>
                        <th class="py-3 px-6 text-left font-semibold">Usuario</th>
                        <th class="py-3 px-6 text-left font-semibold">Acción</th>
                        <th class="py-3 px-6 text-left font-semibold">Descripción</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($bitacoras as $bitacora)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                {{ $bitacora->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                <div class="flex items-center">
                                    <div class="mr-2">
                                        @php
                                            $displayName = 'Sistema';
                                            if ($bitacora->usuario) {
                                                if ($bitacora->usuario->persona) {
                                                    $displayName = $bitacora->usuario->persona->nombres . ' ' . $bitacora->usuario->persona->apellidos;
                                                } else {
                                                    $displayName = $bitacora->usuario->nombre_usuario;
                                                }
                                            }
                                            $initial = substr($displayName, 0, 1);
                                        @endphp
                                        <div
                                            class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold uppercase">
                                            {{ $initial }}
                                        </div>
                                    </div>
                                    <span>{{ $displayName }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-6 text-left">
                                @php
                                    $accionLower = strtolower($bitacora->accion);
                                @endphp
                                <span class="py-1 px-3 rounded-full text-xs font-bold
                                            @if ($accionLower == 'crear') bg-green-100 text-green-700
                                            @elseif($accionLower == 'actualizar') bg-blue-100 text-blue-700
                                            @elseif($accionLower == 'eliminar' || $accionLower == 'desactivar') bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($accionLower) }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $bitacora->descripcion }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p>No se encontraron registros en la bitácora.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $bitacoras->links() }}
        </div>
    </div>
</div>
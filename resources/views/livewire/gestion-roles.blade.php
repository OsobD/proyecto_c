<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Configuración', 'url' => route('configuracion')],
        ['label' => 'Roles'],
    ]" />

    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Roles</h1>
    </div>

    {{-- Barra de búsqueda y acciones --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar rol</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Nombre del rol...">
                </div>
            </div>
            <div>
                <button
                    wire:click="abrirModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                    + Nuevo Rol
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla de Roles --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('nombre')">
                            Rol @if($sortField === 'nombre') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th class="py-3 px-6 text-center">Permisos Asignados</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($roles as $rol)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <span class="font-medium">{{ $rol->nombre }}</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                                    {{ $rol->permisos_count }} permisos
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-action-button 
                                        type="edit" 
                                        wire:click="editarRol({{ $rol->id }})" 
                                        title="Editar" />
                                    
                                    <x-action-button 
                                        type="delete" 
                                        wire:click="eliminarRol({{ $rol->id }})" 
                                        wire:confirm="¿Está seguro de eliminar este rol?" 
                                        title="Eliminar" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 px-6 text-center text-gray-500">No hay roles registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($roles->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $roles->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Crear/Editar --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden animate-fade-in-down flex flex-col">
                {{-- Header fijo --}}
                <div class="p-6 border-b border-gray-200 flex-shrink-0">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-900">{{ $editMode ? 'Editar Rol' : 'Nuevo Rol' }}</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Contenido con scroll --}}
                <div class="flex-1 overflow-y-auto p-6">
                    <form wire:submit.prevent="guardarRol" id="formRol">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Rol</label>
                            <input type="text" wire:model="nombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Permisos Asignados</label>

                            {{-- Búsqueda de permisos --}}
                            <div class="mb-3">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        wire:model.live.debounce.300ms="searchPermisos"
                                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Buscar permisos por nombre, módulo o descripción...">
                                </div>
                            </div>

                            {{-- Permisos agrupados por módulo --}}
                            <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto bg-gray-50">
                                @if(count($this->permisosAgrupados) > 0)
                                    @foreach($this->permisosAgrupados as $modulo => $permisos)
                                        <div class="border-b border-gray-200 last:border-b-0">
                                            {{-- Encabezado del módulo --}}
                                            <div class="bg-white px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                                <button
                                                    type="button"
                                                    wire:click="toggleModulo('{{ $modulo }}')"
                                                    class="flex-1 flex items-center gap-2 text-left">
                                                    <svg class="w-4 h-4 transition-transform {{ in_array($modulo, $modulosAbiertos) ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                    <span class="font-semibold text-gray-800">{{ $this->getNombreModuloAmigable($modulo) }}</span>
                                                    <span class="text-xs text-gray-500">({{ $permisos->count() }} permisos)</span>
                                                </button>

                                                {{-- Botones seleccionar/deseleccionar todo --}}
                                                <div class="flex gap-2">
                                                    @if($this->todoModuloSeleccionado($modulo))
                                                        <button
                                                            type="button"
                                                            wire:click="deseleccionarTodoModulo('{{ $modulo }}')"
                                                            class="text-xs px-2 py-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                                                            title="Deseleccionar todo">
                                                            Deseleccionar todo
                                                        </button>
                                                    @else
                                                        <button
                                                            type="button"
                                                            wire:click="seleccionarTodoModulo('{{ $modulo }}')"
                                                            class="text-xs px-2 py-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                                            title="Seleccionar todo">
                                                            Seleccionar todo
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Permisos del módulo (collapsible) --}}
                                            @if(in_array($modulo, $modulosAbiertos))
                                                <div class="bg-gray-50 px-4 py-3 space-y-2">
                                                    @foreach($permisos as $permiso)
                                                        <label class="flex items-start space-x-3 p-2 hover:bg-white rounded cursor-pointer transition-colors group">
                                                            <input
                                                                type="checkbox"
                                                                wire:model.live="selectedPermisos"
                                                                value="{{ $permiso->id }}"
                                                                class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mt-0.5">
                                                            <div class="flex-1">
                                                                <div class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                                                                    {{ $permiso->nombre }}
                                                                </div>
                                                                @if($permiso->descripcion)
                                                                    <div class="text-xs text-gray-500 mt-0.5">
                                                                        {{ $permiso->descripcion }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="p-4 text-center text-gray-500 text-sm">
                                        @if(!empty($searchPermisos))
                                            No se encontraron permisos que coincidan con "{{ $searchPermisos }}"
                                        @else
                                            No hay permisos registrados en el sistema.
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Contador de permisos seleccionados --}}
                            <div class="mt-2 text-sm text-gray-600">
                                <span class="font-semibold">{{ count($selectedPermisos) }}</span> permisos seleccionados
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer fijo con botones --}}
                <div class="p-6 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">Cancelar</button>
                        <button type="submit" form="formRol" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Mensajes Flash --}}
    @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50" role="alert">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
</div>

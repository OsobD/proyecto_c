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
                                    <button wire:click="editarRol({{ $rol->id }})" class="text-blue-500 hover:text-blue-700" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button wire:click="eliminarRol({{ $rol->id }})" wire:confirm="¿Está seguro de eliminar este rol?" class="text-red-500 hover:text-red-700" title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
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
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
            <div class="relative bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 overflow-hidden animate-fade-in-down">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">{{ $editMode ? 'Editar Rol' : 'Nuevo Rol' }}</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="guardarRol">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Rol</label>
                            <input type="text" wire:model="nombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Permisos Asignados</label>
                            <div class="border border-gray-200 rounded-lg p-4 max-h-60 overflow-y-auto bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($allPermisos as $permiso)
                                        <label class="flex items-center space-x-3 p-2 hover:bg-white rounded cursor-pointer transition-colors">
                                            <input type="checkbox" wire:model="selectedPermisos" value="{{ $permiso->id }}" class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <span class="text-sm text-gray-700">{{ $permiso->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @if(count($allPermisos) === 0)
                                <p class="text-sm text-gray-500 mt-2">No hay permisos registrados en el sistema.</p>
                            @endif
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar</button>
                        </div>
                    </form>
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

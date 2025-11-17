{{--
    Vista: Gestión de Proveedores
    Descripción: CRUD completo de proveedores con búsqueda y activación/desactivación
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Catálogo', 'url' => '#'],
        ['label' => 'Proveedores'],
    ]" />

    {{-- Mensajes flash --}}
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Encabezado con título y búsqueda --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Proveedores</h1>
            <p class="text-sm text-gray-600 mt-1">
                Administra los proveedores del sistema de inventario
            </p>
        </div>
        <button
            wire:click="abrirModal"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Proveedor
        </button>
    </div>

    {{-- Barra de búsqueda --}}
    <div class="mb-4">
        <input
            type="text"
            wire:model.live="searchProveedor"
            placeholder="Buscar por nombre, NIT o régimen tributario..."
            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>

    {{-- Contenedor principal --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Tabla de listado de proveedores --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Nombre del Proveedor</th>
                        <th class="py-3 px-6 text-left">NIT</th>
                        <th class="py-3 px-6 text-left">Régimen Tributario</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($proveedores as $proveedor)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <span class="font-medium">{{ $proveedor->nombre }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="font-mono text-gray-700">{{ $proveedor->nit }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $proveedor->regimenTributario->nombre ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if ($proveedor->activo)
                                    <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">Activo</span>
                                @else
                                    <span class="bg-red-200 text-red-700 py-1 px-3 rounded-full text-xs font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    {{-- Editar --}}
                                    <button
                                        wire:click="editarProveedor({{ $proveedor->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-blue-100 hover:bg-blue-200 text-blue-600"
                                        title="Editar proveedor">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    {{-- Toggle Estado --}}
                                    <button
                                        wire:click="toggleEstado({{ $proveedor->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-md {{ $proveedor->activo ? 'bg-red-100 hover:bg-red-200 text-red-600' : 'bg-green-100 hover:bg-green-200 text-green-600' }}"
                                        title="{{ $proveedor->activo ? 'Desactivar' : 'Activar' }} proveedor">
                                        @if ($proveedor->activo)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">
                                @if ($searchProveedor)
                                    No se encontraron proveedores que coincidan con "{{ $searchProveedor }}".
                                @else
                                    No hay proveedores registrados. Haz clic en "Nuevo Proveedor" para crear uno.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal para crear/editar proveedor --}}
    @if ($showModal)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Fondo oscuro --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                {{-- Contenedor del modal --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    {{-- Encabezado del modal --}}
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                {{ $editingId ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
                            </h3>
                            <button
                                wire:click="closeModal"
                                class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Formulario --}}
                        <form wire:submit.prevent="guardarProveedor">
                            {{-- Nombre --}}
                            <div class="mb-4">
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre del Proveedor <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nombre"
                                    wire:model="nombre"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ej: Distribuidora XYZ">
                                @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- NIT --}}
                            <div class="mb-4">
                                <label for="nit" class="block text-sm font-medium text-gray-700 mb-1">
                                    NIT <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nit"
                                    wire:model="nit"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ej: 123456789-0">
                                @error('nit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Régimen Tributario --}}
                            <div class="mb-4">
                                <label for="regimenTributarioId" class="block text-sm font-medium text-gray-700 mb-1">
                                    Régimen Tributario <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="regimenTributarioId"
                                    wire:model="regimenTributarioId"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccione un régimen</option>
                                    @foreach ($regimenesTributarios as $regimen)
                                        <option value="{{ $regimen->id }}">{{ $regimen->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('regimenTributarioId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Dirección --}}
                            <div class="mb-4">
                                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">
                                    Dirección
                                </label>
                                <input
                                    type="text"
                                    id="direccion"
                                    wire:model="direccion"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ej: Calle 123, Ciudad">
                                @error('direccion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Teléfono --}}
                            <div class="mb-4">
                                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                                    Teléfono
                                </label>
                                <input
                                    type="text"
                                    id="telefono"
                                    wire:model="telefono"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ej: +593 999 999 999">
                                @error('telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    wire:model="email"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ej: contacto@proveedor.com">
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Botones --}}
                            <div class="flex justify-end gap-2 mt-6">
                                <button
                                    type="button"
                                    wire:click="closeModal"
                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-md">
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md">
                                    {{ $editingId ? 'Actualizar' : 'Guardar' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

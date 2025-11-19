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
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative animate-fade-in" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative animate-fade-in" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
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
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            + Nuevo Proveedor
        </button>
    </div>

    {{-- Contenedor principal --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Barra de búsqueda --}}
        <div class="mb-6">
            <div class="relative w-full md:w-1/2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live="searchProveedor"
                    placeholder="Buscar por nombre, NIT o régimen tributario..."
                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
            </div>
        </div>

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
                                    <x-action-button
                                        type="edit"
                                        wire:click="editarProveedor({{ $proveedor->id }})"
                                        title="Editar proveedor" />
                                    {{-- Toggle Estado --}}
                                    @if ($proveedor->activo)
                                        <x-action-button
                                            type="delete"
                                            wire:click="toggleEstado({{ $proveedor->id }})"
                                            title="Desactivar proveedor" />
                                    @else
                                        <x-action-button
                                            type="activate"
                                            wire:click="toggleEstado({{ $proveedor->id }})"
                                            title="Activar proveedor" />
                                    @endif
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

        {{-- Paginación --}}
        @if($proveedores->hasPages())
            <div class="mt-6 px-6 py-4 border-t border-gray-200">
                {{ $proveedores->links() }}
            </div>
        @endif
    </div>

    {{-- Modal para crear/editar proveedor --}}
    <div x-data="{
            show: @entangle('showModal').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-[100] flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModal"
         wire:ignore.self>
        <div class="relative p-6 border w-full max-w-sm shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-hidden"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">
                    {{ $editingId ? 'Editar Proveedor' : 'Crear Nuevo Proveedor' }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="guardarProveedor">
                {{-- NIT --}}
                <div class="mb-6">
                    <label for="nit" class="block text-sm font-medium text-gray-700">
                        NIT (Número de Identificación Tributaria)
                    </label>
                    <input
                        type="text"
                        id="nit"
                        wire:model="nit"
                        class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('nit') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="Ej: 12345678-9">
                    @error('nit')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Régimen --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">
                        Régimen
                    </label>
                    <div class="relative">
                        @if($selectedRegimen)
                            <div class="flex items-center justify-between mt-1 w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm @error('regimenTributarioId') border-red-500 @enderror">
                                <span>{{ $selectedRegimen }}</span>
                                <button type="button" wire:click.prevent="clearRegimen" class="text-gray-400 hover:text-gray-600">
                                    ×
                                </button>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showRegimenDropdown').live }" @click.outside="open = false">
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="mt-1 w-full px-4 py-3 text-left border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('regimenTributarioId') border-red-500 ring-2 ring-red-200 @enderror">
                                    <span class="text-gray-500">Seleccione un régimen</span>
                                </button>
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                    <ul>
                                        @foreach($regimenesTributarios as $regimen)
                                            <li wire:click.prevent="selectRegimen({{ $regimen->id }}, '{{ $regimen->nombre }}')"
                                                @click="open = false"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $regimen->nombre }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('regimenTributarioId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nombre del Proveedor --}}
                <div class="mb-6">
                    <label for="nombre" class="block text-sm font-medium text-gray-700">
                        Nombre del Proveedor
                    </label>
                    <input
                        type="text"
                        id="nombre"
                        wire:model="nombre"
                        class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('nombre') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="Ej: Ferretería San José">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-all duration-200">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                        {{ $editingId ? 'Actualizar' : 'Crear' }}
                    </button>
                </div>
            </form>
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

        /* Animación de mensajes flash */
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
</div>

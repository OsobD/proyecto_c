{{--
Vista: Gestión de Regímenes Tributarios
Descripción: CRUD de regímenes tributarios con diseño estandarizado.
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Configuración', 'url' => route('configuracion')],
        ['label' => 'Regímenes Tributarios'],
    ]" />

    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Regímenes Tributarios</h1>
        <button wire:click="openModal"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
            + Nuevo Régimen
        </button>
    </div>

    {{-- Mensajes --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 animate-fade-in"
            role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animate-fade-in"
            role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    {{-- Contenedor principal --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Búsqueda --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar régimen</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live="search"
                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="Buscar régimen por nombre...">
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Nombre</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($regimenes as $regimen)
                        <tr class="border-b border-gray-200 hover:bg-gray-100 transition-colors">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <span class="font-medium">{{ $regimen->nombre }}</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    <x-action-button type="edit" wire:click="edit({{ $regimen->id }})"
                                        title="Editar régimen" />

                                    <x-action-button type="delete" wire:click="confirmDelete({{ $regimen->id }})"
                                        title="Eliminar régimen" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-4 text-gray-500">No se encontraron regímenes tributarios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $regimenes->links() }}
        </div>
    </div>

    {{-- Modal de Régimen --}}
    <div x-data="{
            show: @entangle('showModal').live,
            animatingOut: false
         }" x-show="show || animatingOut" x-cloak
        x-init="$watch('show', value => { if (!value) animatingOut = true; })"
        @animationend="if (!show) animatingOut = false"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
        :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
        wire:click.self="closeModal" wire:ignore.self>
        <div class="relative border w-full max-w-md shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-hidden"
            :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
            @click.stop>
            <div class="p-8 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        {{ $editMode ? 'Editar Régimen' : 'Nuevo Régimen' }}
                    </h3>
                    <button wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200 font-bold text-xl">
                        &times;
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Régimen <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nombre"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('nombre') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="Ej: Pequeño Contribuyente">
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" wire:click="closeModal"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="save">
                                {{ $editMode ? 'Actualizar' : 'Guardar' }}
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal de Confirmación de Eliminación --}}
    <div x-data="{
            show: @entangle('showDeleteModal').live,
            animatingOut: false
         }" x-show="show || animatingOut" x-cloak
        x-init="$watch('show', value => { if (!value) animatingOut = true; })"
        @animationend="if (!show) animatingOut = false"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
        :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
        wire:click.self="closeDeleteModal" wire:ignore.self>
        <div class="relative border w-full max-w-md shadow-2xl rounded-xl bg-white overflow-hidden"
            :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
            @click.stop>
            <div class="p-6 text-center">
                <h3 class="text-lg font-bold text-gray-900 mb-2 mt-4">¿Eliminar Régimen?</h3>
                <p class="text-sm text-gray-500 mb-6">
                    ¿Estás seguro de que deseas eliminar el régimen <span
                        class="font-bold text-gray-800">"{{ $regimenToDeleteName }}"</span>? Esta acción no se puede
                    deshacer.
                </p>

                <div class="flex justify-center gap-3">
                    <button type="button" wire:click="closeDeleteModal"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                        Cancelar
                    </button>
                    <button type="button" wire:click="delete"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                        Sí, Eliminar
                    </button>
                </div>
            </div>
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
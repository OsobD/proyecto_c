<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Categorías</h1>
        <button
            wire:click="abrirModal"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            + Nueva Categoría
        </button>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Buscador --}}
        <div class="mb-6">
            <input
                type="text"
                wire:model.live.debounce.300ms="searchCategoria"
                class="w-full md:w-1/2 px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                placeholder="Buscar categoría...">
        </div>

        {{-- Tabla de Categorías --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Nombre</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($this->categoriasFiltradas as $categoria)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left">
                                <span class="font-medium">{{ $categoria['nombre'] }}</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($categoria['activo'])
                                    <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">Activo</span>
                                @else
                                    <span class="bg-red-200 text-red-700 py-1 px-3 rounded-full text-xs font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <button
                                        wire:click="editarCategoria({{ $categoria['id'] }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 hover:bg-blue-200 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="toggleEstado({{ $categoria['id'] }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-full {{ $categoria['activo'] ? 'bg-red-100 hover:bg-red-200' : 'bg-green-100 hover:bg-green-200' }}">
                                        @if($categoria['activo'])
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500">
                                No se encontraron categorías.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Crear/Editar Categoría --}}
    <div x-data="{
            show: @entangle('showModal'),
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModal">
        <div class="relative p-6 border w-full max-w-md shadow-lg rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    {{ $editingId ? 'Editar Categoría' : 'Nueva Categoría' }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="guardarCategoria">
                <div class="mb-4">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de la Categoría
                    </label>
                    <input
                        type="text"
                        id="nombre"
                        wire:model="nombre"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nombre') border-red-500 @enderror"
                        placeholder="Ej: Herramientas">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                        {{ $editingId ? 'Actualizar' : 'Crear' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
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
    </style>
</div>

{{--
    Vista: Gestión de Bodegas
    Descripción: Interfaz para administrar bodegas físicas y tarjetas de responsabilidad
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Bodegas'],
    ]" />

    {{-- Encabezado con título y botón para agregar bodega --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Bodegas</h1>
        <button wire:click="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            + Nueva Bodega
        </button>
    </div>

    {{-- Contenedor principal --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Tabla de listado de bodegas --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Nombre</th>
                        <th class="py-3 px-6 text-left">Tipo</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach ($bodegas as $bodega)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <span class="font-medium">{{ $bodega['nombre'] }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                @if ($bodega['tipo'] == 'Física')
                                    <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs">Física</span>
                                @else
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Tarjeta de Responsabilidad</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    {{-- Editar --}}
                                    <button wire:click="openModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    {{-- Eliminar --}}
                                    <button class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Form --}}
    <div x-data="{
            show: @entangle('isModalOpen'),
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModal">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-xl font-semibold text-gray-800">Crear/Editar Bodega</h3>
                <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800">&times;</button>
            </div>
            <form wire:submit.prevent="saveBodega" class="mt-4">
                <div class="space-y-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Bodega</label>
                        <input type="text" id="nombre" wire:model="nombre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-3 px-4 text-base">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <div class="relative">
                            @if($tipo)
                                <div class="flex items-center justify-between mt-1 w-full px-4 pr-4 py-3 text-base border-2 border-gray-300 rounded-md shadow-sm">
                                    <span>{{ $tipo }}</span>
                                    <button type="button" wire:click.prevent="clearTipo" class="text-gray-400 hover:text-gray-600 text-2xl ml-2">
                                        ×
                                    </button>
                                </div>
                            @else
                                <div class="relative" x-data="{ open: @entangle('showTipoDropdown') }">
                                    <input
                                        type="text"
                                        @click="open = true"
                                        @click.outside="open = false"
                                        readonly
                                        class="mt-1 block w-full px-4 py-3 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent rounded-md shadow-sm cursor-pointer"
                                        placeholder="Seleccionar tipo..."
                                    >
                                    <div x-show="open"
                                         x-transition
                                         @click.away="open = false"
                                         class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 shadow-lg">
                                        <ul>
                                            @foreach ($tiposDisponibles as $tipoItem)
                                                <li wire:click.prevent="selectTipo('{{ $tipoItem['nombre'] }}')"
                                                    class="px-4 py-3 cursor-pointer hover:bg-indigo-50 transition-colors">
                                                    {{ $tipoItem['nombre'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end">
                    <button type="button" wire:click="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                        Guardar
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

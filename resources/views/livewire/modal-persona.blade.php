{{-- Modal Reutilizable: Crear Nueva Persona --}}
<div x-data="{
        show: @entangle('showModal').live,
        animatingOut: false
     }"
     x-show="show || animatingOut"
     x-cloak
     x-init="$watch('show', value => { if (!value) animatingOut = true; })"
     @animationend="if (!show) animatingOut = false"
     class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-[60] flex items-center justify-center"
     :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
     wire:click.self="cerrar"
     wire:ignore.self>
    <div class="relative border w-full max-w-lg shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-hidden"
         :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
         @click.stop>
        <div class="p-8 overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Crear Nueva Persona</h3>
                <button wire:click="cerrar" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Botón de prueba (temporal para debugging) --}}
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                <button
                    type="button"
                    wire:click="testComponente"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded">
                    🔧 Probar Componente
                </button>
                <p class="text-xs text-yellow-700 mt-1">Botón de prueba - Si aparece un alert, el componente funciona</p>
            </div>

            <form wire:submit.prevent="guardar">
                {{-- Resumen de errores de validación --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-2 border-red-300 rounded-lg">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-red-800 mb-2">Hay errores en el formulario:</p>
                                <ul class="list-disc list-inside text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Indicador de carga visible --}}
                <div wire:loading wire:target="guardar" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                    <div class="flex items-center gap-2 text-blue-700">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="font-semibold">Guardando persona...</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    {{-- Nombres --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                        <input
                            type="text"
                            wire:model="nombres"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('nombres') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="Ej: Juan Carlos">
                        @error('nombres')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Apellidos --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos *</label>
                        <input
                            type="text"
                            wire:model="apellidos"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('apellidos') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="Ej: Pérez García">
                        @error('apellidos')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- DPI --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">DPI *</label>
                        <input
                            type="text"
                            wire:model="dpi"
                            maxlength="13"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('dpi') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="Ej: 1234567890101">
                        @error('dpi')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                        <input
                            type="text"
                            wire:model="telefono"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('telefono') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="Ej: 5555-5555">
                        @error('telefono')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Correo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                        <input
                            type="email"
                            wire:model="correo"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('correo') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="Ej: usuario@eemq.com">
                        @error('correo')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        wire:click="cerrar"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-all duration-200">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50">
                        <span wire:loading.remove wire:target="guardar">✓ Crear Persona</span>
                        <span wire:loading wire:target="guardar" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creando...
                        </span>
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
    </style>
</div>

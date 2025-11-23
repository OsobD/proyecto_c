{{-- 
    GUÍA DE ESTILO: MODAL DE FILTROS Y AJUSTES
    
    Este archivo sirve como referencia para implementar el modal de filtros estandarizado.
    Copie y adapte este código en sus componentes Livewire.
    
    REQUISITOS:
    1. Alpine.js (para la lógica del modal)
    2. Tailwind CSS (para los estilos)
    3. Iconos (Heroicons recomendados)
    
    ESTRUCTURA:
    - Contenedor Fixed (Overlay)
    - Modal Card (Flex Column, Max Height)
    - Header (Fixed/Shrink-0)
    - Body (Scrollable/Flex-1)
    - Footer (Fixed/Shrink-0)
--}}

{{-- 1. BOTÓN DE APERTURA --}}
<button
    wire:click="openFilterModal"
    class="flex items-center gap-2 bg-white border-2 border-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
    </svg>
    <span>Filtros / Ajustes</span>
    {{-- Indicador de filtros activos --}}
    @if($filtrosActivos ?? false)
        <span class="flex h-3 w-3 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
        </span>
    @endif
</button>

{{-- 2. MODAL COMPLETO --}}
<div x-data="{
        show: @entangle('showFilterModal').live,
        animatingOut: false
     }"
     x-show="show || animatingOut"
     x-cloak
     x-init="$watch('show', value => { if (!value) animatingOut = true; })"
     @animationend="if (!show) animatingOut = false"
     class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
     :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
     wire:click.self="closeFilterModal"
     wire:ignore.self>
    
    {{-- CARD DEL MODAL --}}
    <div class="relative border w-full max-w-lg shadow-2xl rounded-xl bg-white max-h-[85vh] flex flex-col overflow-hidden"
         :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
         @click.stop>
        
        {{-- HEADER (FIJO) --}}
        <div class="flex justify-between items-center p-5 border-b border-gray-100 shrink-0">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                Filtros y Ajustes
            </h3>
            <button wire:click="closeFilterModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- BODY (SCROLLABLE) --}}
        <div class="p-5 overflow-y-auto flex-1">
            
            {{-- SECCIÓN: ORDENAR --}}
            <div class="mb-6">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Ordenar por</h4>
                <div class="grid grid-cols-2 gap-3">
                    {{-- Botón de ordenamiento ejemplo --}}
                    <button class="flex items-center justify-between px-3 py-2 rounded-lg border bg-blue-50 border-blue-500 text-blue-700 transition-all text-sm font-medium">
                        <span>Nombre</span>
                        <span class="text-xs font-bold">ASC</span>
                    </button>
                    <button class="flex items-center justify-between px-3 py-2 rounded-lg border bg-white border-gray-200 text-gray-600 hover:bg-gray-50 transition-all text-sm font-medium">
                        <span>Fecha</span>
                    </button>
                </div>
            </div>

            {{-- SECCIÓN: FILTROS --}}
            <div class="mb-6">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Filtrar por</h4>
                
                {{-- Input ejemplo --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option>Todas</option>
                        <option>Opción 1</option>
                    </select>
                </div>
            </div>

            {{-- SECCIÓN: VISUALIZACIÓN (CHECKBOX ESTILIZADO) --}}
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Visualización</h4>
                
                <label class="custom-checkbox-container gap-3 cursor-pointer select-none p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors w-full flex items-center">
                    <input type="checkbox">
                    <div class="custom-checkmark"></div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-800">Mostrar archivados</span>
                        <span class="text-xs text-gray-500">Incluir elementos ocultos</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- FOOTER (FIJO) --}}
        <div class="flex justify-between items-center p-5 border-t border-gray-100 bg-gray-50 shrink-0">
            <button
                type="button"
                wire:click="clearFilters"
                class="text-sm text-red-600 hover:text-red-800 font-medium hover:underline">
                Limpiar filtros
            </button>
            
            <button
                type="button"
                wire:click="closeFilterModal"
                class="bg-gray-900 hover:bg-black text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                Listo
            </button>
        </div>
    </div>
</div>

{{-- ESTILOS NECESARIOS (Si no están en app.css) --}}
<style>
    /* Checkbox personalizado */
    .custom-checkbox-container { display: inline-flex; align-items: center; position: relative; }
    .custom-checkbox-container input { display: none; }
    .custom-checkmark {
        position: relative; display: inline-block; height: 1.25em; width: 1.25em;
        background-color: transparent; border-radius: 0.25em; transition: all 0.3s ease; flex-shrink: 0;
    }
    .custom-checkmark:after {
        content: ""; position: absolute; transition: all 0.3s ease; box-sizing: border-box;
        left: 0; top: 0; width: 100%; height: 100%;
        border: 0.125em solid #4B5563; border-radius: 0.25em; transform: rotate(0deg);
    }
    .custom-checkbox-container input:checked ~ .custom-checkmark { background-color: #2563EB; }
    .custom-checkbox-container input:checked ~ .custom-checkmark:after {
        left: 0.45em; top: 0.25em; width: 0.35em; height: 0.7em;
        border-color: transparent white white transparent; border-width: 0 0.15em 0.15em 0;
        border-radius: 0; transform: rotate(45deg);
    }
</style>

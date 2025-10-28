<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Configuración'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Configuración del Sistema</h1>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- IVA Percentage --}}
                <div>
                    <label for="iva" class="block text-sm font-medium text-gray-700">Porcentaje de IVA (%)</label>
                    <input type="number" step="0.01" id="iva" name="iva" wire:model="iva" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

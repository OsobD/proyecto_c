<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Configuración'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Configuración del Sistema</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Tarjeta: Roles -->
        <a href="{{ route('configuracion.roles') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow cursor-pointer flex items-center gap-4 border-l-4 border-blue-500">
            <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Roles</h2>
                <p class="text-gray-600 text-sm">Gestionar roles de usuario y sus niveles de acceso.</p>
            </div>
        </a>

        <!-- Tarjeta: Permisos -->
        <a href="{{ route('configuracion.permisos') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow cursor-pointer flex items-center gap-4 border-l-4 border-green-500">
            <div class="p-3 bg-green-100 rounded-full text-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Permisos</h2>
                <p class="text-gray-600 text-sm">Definir permisos granulares para el sistema.</p>
            </div>
        </a>
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

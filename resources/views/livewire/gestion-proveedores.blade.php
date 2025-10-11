<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Proveedores</h1>
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            + Nuevo Proveedor
        </button>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Nombre del Proveedor</th>
                        <th class="py-3 px-6 text-left">Contacto</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach ($proveedores as $proveedor)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <span class="font-medium">{{ $proveedor['nombre'] }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $proveedor['contacto'] }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if ($proveedor['estado'] == 'Activo')
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Activo</span>
                                @else
                                    <span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <button class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 mr-2">
                                        {{-- Edit Icon --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300">
                                        {{-- Toggle Status Icon --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
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
</div>

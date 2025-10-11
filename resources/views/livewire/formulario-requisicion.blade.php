<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Formulario de Requisición</h1>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Recipient Selection --}}
                <div>
                    <label for="empleado" class="block text-sm font-medium text-gray-700">Transferir a:</label>
                    <select id="empleado" name="empleado" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option>Seleccione un empleado</option>
                        @foreach ($empleados as $empleado)
                            <option value="{{ $empleado['id'] }}">{{ $empleado['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Product Search with Autocomplete --}}
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Buscar producto:</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" placeholder="Escriba para buscar...">
                        {{-- Placeholder for autocomplete results --}}
                        <div class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden">
                            <ul>
                                @foreach ($productos as $producto)
                                    <li class="px-3 py-2 cursor-pointer hover:bg-gray-100">{{ $producto['descripcion'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Products List Table --}}
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-800">Productos en la Requisición</h2>
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Código</th>
                                <th class="py-3 px-6 text-left">Descripción</th>
                                <th class="py-3 px-6 text-center">Cantidad</th>
                                <th class="py-3 px-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            {{-- Example Row --}}
                            <tr class="border-b border-gray-200">
                                <td class="py-3 px-6 text-left">PROD-002</td>
                                <td class="py-3 px-6 text-left">Abrazaderas de metal</td>
                                <td class="py-3 px-6 text-center">
                                    <input type="number" value="5" class="w-20 text-center border-gray-300 rounded-md">
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <button type="button" class="text-red-600 hover:text-red-800">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-3 px-6 text-left">PROD-003</td>
                                <td class="py-3 px-6 text-left">Cinta aislante</td>
                                <td class="py-3 px-6 text-center">
                                    <input type="number" value="2" class="w-20 text-center border-gray-300 rounded-md">
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <button type="button" class="text-red-600 hover:text-red-800">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Completar Requisición
                </button>
            </div>
        </form>
    </div>
</div>

<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Generación de Reportes</h1>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Filters Form --}}
        <form class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div>
                <label for="tipo_reporte" class="block text-sm font-medium text-gray-700">Tipo de Reporte</label>
                <select id="tipo_reporte" name="tipo_reporte" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option>Seleccione un tipo</option>
                    @foreach ($tiposReporte as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label for="usuario" class="block text-sm font-medium text-gray-700">Usuario</label>
                <select id="usuario" name="usuario" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option>Todos</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario['id'] }}">{{ $usuario['nombre'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-4 flex justify-end">
                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Generar Reporte
                </button>
            </div>
        </form>
    </div>

    {{-- Report Display Area --}}
    <div class="bg-white p-6 rounded-lg shadow-md mt-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Resultado del Reporte</h2>
            <div>
                <button class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg mr-2">
                    Imprimir
                </button>
                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                    Exportar a XLS
                </button>
            </div>
        </div>

        {{-- Example Report Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-left">Descripción del Movimiento</th>
                        <th class="py-3 px-6 text-left">Usuario</th>
                        <th class="py-3 px-6 text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <tr class="border-b border-gray-200">
                        <td class="py-3 px-6 text-left">2023-10-26</td>
                        <td class="py-3 px-6 text-left">Requisición #123: Tornillos de acero</td>
                        <td class="py-3 px-6 text-left">Juan Pérez</td>
                        <td class="py-3 px-6 text-right">50</td>
                    </tr>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <td class="py-3 px-6 text-left">2023-10-25</td>
                        <td class="py-3 px-6 text-left">Compra #45: Cinta aislante</td>
                        <td class="py-3 px-6 text-left">Admin</td>
                        <td class="py-3 px-6 text-right">100</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

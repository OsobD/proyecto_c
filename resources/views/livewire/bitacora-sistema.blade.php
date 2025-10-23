{{--
    Vista principal para la Bitácora del Sistema.
    Esta vista muestra un historial de las acciones importantes realizadas en la aplicación.
    Utiliza datos proporcionados por el componente de Livewire `BitacoraSistema`.
--}}
<div>
    {{-- Encabezado de la página --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bitácora del Sistema</h1>
    </div>

    {{-- Contenedor principal con fondo blanco y sombra --}}
    <div class="bg-white p-6 rounded-lg shadow-md">

        {{-- Sección de Filtros --}}
        {{-- A futuro, estos campos permitirán filtrar los registros de la bitácora. --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label for="search_usuario" class="block text-sm font-medium text-gray-700">Buscar por Usuario</label>
                <input type="text" id="search_usuario" name="search_usuario" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Nombre o email...">
            </div>
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Desde</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Hasta</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        {{-- Tabla de Registros de la Bitácora --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                {{-- Encabezado de la tabla --}}
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Fecha y Hora</th>
                        <th class="py-3 px-6 text-left">Usuario</th>
                        <th class="py-3 px-6 text-left">Acción</th>
                        <th class="py-3 px-6 text-left">Descripción</th>
                    </tr>
                </thead>
                {{-- Cuerpo de la tabla --}}
                <tbody class="text-gray-600 text-sm font-light">
                    {{-- Itera sobre la variable $logs (proporcionada por el componente) para mostrar cada registro. --}}
                    @foreach ($logs as $log)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            {{-- Columna: Fecha y Hora --}}
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                {{ $log['fecha'] }}
                            </td>
                            {{-- Columna: Usuario --}}
                            <td class="py-3 px-6 text-left">
                                {{ $log['usuario'] }}
                            </td>
                            {{-- Columna: Acción --}}
                            <td class="py-3 px-6 text-left">
                                <span class="font-medium">{{ $log['accion'] }}</span>
                            </td>
                            {{-- Columna: Descripción --}}
                            <td class="py-3 px-6 text-left">
                                {{ $log['descripcion'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Reportes'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Generación de Reportes</h1>
    </div>

    {{-- Mensajes --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tabs de Categorías --}}
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button
                    wire:click="cambiarTab('compras')"
                    class="py-4 px-6 text-center border-b-2 font-medium text-sm {{ $tabActivo === 'compras' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Compras
                </button>
                <button
                    wire:click="cambiarTab('traslados')"
                    class="py-4 px-6 text-center border-b-2 font-medium text-sm {{ $tabActivo === 'traslados' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Traslados
                </button>
                <button
                    wire:click="cambiarTab('inventario')"
                    class="py-4 px-6 text-center border-b-2 font-medium text-sm {{ $tabActivo === 'inventario' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Inventario
                </button>
                <button
                    wire:click="cambiarTab('bitacora')"
                    class="py-4 px-6 text-center border-b-2 font-medium text-sm {{ $tabActivo === 'bitacora' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Bitácora
                </button>
            </nav>
        </div>

        {{-- Contenido de cada tab --}}
        <div class="p-6">
            {{-- Tab Compras --}}
            @if($tabActivo === 'compras')
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Reportes de Compras</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="tipo_reporte" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Reporte</label>
                            <select
                                id="tipo_reporte"
                                wire:model="tipoReporte"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione un tipo</option>
                                @foreach ($reportesCompras as $reporte)
                                    <option value="{{ $reporte['id'] }}">{{ $reporte['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                            <select
                                id="proveedor"
                                wire:model="proveedorSeleccionado"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor['id'] }}">{{ $proveedor['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                            <input
                                type="date"
                                id="fecha_inicio"
                                wire:model="fechaInicio"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                            <input
                                type="date"
                                id="fecha_fin"
                                wire:model="fechaFin"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tab Traslados --}}
            @if($tabActivo === 'traslados')
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Reportes de Traslados</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="tipo_reporte" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Reporte</label>
                            <select
                                id="tipo_reporte"
                                wire:model="tipoReporte"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione un tipo</option>
                                @foreach ($reportesTraslados as $reporte)
                                    <option value="{{ $reporte['id'] }}">{{ $reporte['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="bodega" class="block text-sm font-medium text-gray-700 mb-1">Bodega</label>
                            <select
                                id="bodega"
                                wire:model="bodegaSeleccionada"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todas</option>
                                @foreach ($bodegas as $bodega)
                                    <option value="{{ $bodega['id'] }}">{{ $bodega['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                            <input
                                type="date"
                                id="fecha_inicio"
                                wire:model="fechaInicio"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                            <input
                                type="date"
                                id="fecha_fin"
                                wire:model="fechaFin"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tab Inventario --}}
            @if($tabActivo === 'inventario')
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Reportes de Inventario</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="tipo_reporte" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Reporte</label>
                            <select
                                id="tipo_reporte"
                                wire:model="tipoReporte"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione un tipo</option>
                                @foreach ($reportesInventario as $reporte)
                                    <option value="{{ $reporte['id'] }}">{{ $reporte['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="bodega" class="block text-sm font-medium text-gray-700 mb-1">Bodega</label>
                            <select
                                id="bodega"
                                wire:model="bodegaSeleccionada"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todas</option>
                                @foreach ($bodegas as $bodega)
                                    <option value="{{ $bodega['id'] }}">{{ $bodega['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="usuario" class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                            <select
                                id="usuario"
                                wire:model="usuarioSeleccionado"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos</option>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario['id'] }}">{{ $usuario['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Corte</label>
                            <input
                                type="date"
                                id="fecha_fin"
                                wire:model="fechaFin"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tab Bitácora --}}
            @if($tabActivo === 'bitacora')
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Reportes de Bitácora</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="tipo_reporte" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Reporte</label>
                            <select
                                id="tipo_reporte"
                                wire:model="tipoReporte"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione un tipo</option>
                                @foreach ($reportesBitacora as $reporte)
                                    <option value="{{ $reporte['id'] }}">{{ $reporte['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="usuario" class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                            <select
                                id="usuario"
                                wire:model="usuarioSeleccionado"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos</option>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario['id'] }}">{{ $usuario['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                            <input
                                type="date"
                                id="fecha_inicio"
                                wire:model="fechaInicio"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                            <input
                                type="date"
                                id="fecha_fin"
                                wire:model="fechaFin"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Botón de Generar Reporte --}}
            <div class="mt-6 flex justify-end">
                <button
                    wire:click="generarReporte"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                    Generar Reporte
                </button>
            </div>
        </div>
    </div>

    {{-- Área de Resultados --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Resultado del Reporte</h2>
            <div class="flex space-x-2">
                <button
                    wire:click="imprimir"
                    class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg">
                    Imprimir
                </button>
                <button
                    wire:click="exportarExcel"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                    Exportar a XLS
                </button>
            </div>
        </div>

        {{-- Tabla de Ejemplo --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-left">Descripción</th>
                        <th class="py-3 px-6 text-left">Usuario</th>
                        <th class="py-3 px-6 text-right">Cantidad / Monto</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <tr class="border-b border-gray-200">
                        <td colspan="4" class="py-8 text-center text-gray-500">
                            Seleccione un tipo de reporte y haga clic en "Generar Reporte" para ver los resultados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Reporte</label>
                            <div class="relative">
                                @if($selectedTipoReporteFiltro)
                                    <div wire:click="clearTipoReporteFiltro"
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                                        <span class="font-medium text-gray-800">{{ $selectedTipoReporteFiltro['nombre'] }}</span>
                                        <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showTipoReporteDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchTipoReporteFiltro"
                                            @click="open = true"
                                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-blue-400"
                                            placeholder="Buscar tipo de reporte...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                            <ul>
                                                <li wire:click.prevent="clearTipoReporteFiltro"
                                                    @click="open = false"
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                                    Seleccione un tipo
                                                </li>
                                                @foreach ($this->tipoReporteResults as $reporte)
                                                    <li wire:click.prevent="selectTipoReporteFiltro('{{ $reporte['id'] }}')"
                                                        @click="open = false"
                                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                                        {{ $reporte['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                            <div class="relative">
                                @if($selectedProveedorFiltro)
                                    <div wire:click="clearProveedorFiltro"
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                                        <span class="font-medium text-gray-800">{{ $selectedProveedorFiltro['nombre'] }}</span>
                                        <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showProveedorDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchProveedorFiltro"
                                            @click="open = true"
                                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-blue-400"
                                            placeholder="Buscar proveedor...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                            <ul>
                                                <li wire:click.prevent="clearProveedorFiltro"
                                                    @click="open = false"
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                                    Todos los proveedores
                                                </li>
                                                @foreach ($this->proveedorResults as $proveedor)
                                                    <li wire:click.prevent="selectProveedorFiltro({{ $proveedor['id'] }})"
                                                        @click="open = false"
                                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                                        {{ $proveedor['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                            <input
                                type="date"
                                id="fecha_inicio"
                                wire:model="fechaInicio"
                                class="block w-full border-2 border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400">
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                            <input
                                type="date"
                                id="fecha_fin"
                                wire:model="fechaFin"
                                class="block w-full border-2 border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Reporte</label>
                            <div class="relative">
                                @if($selectedTipoReporteFiltro)
                                    <div wire:click="clearTipoReporteFiltro"
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                                        <span class="font-medium text-gray-800">{{ $selectedTipoReporteFiltro['nombre'] }}</span>
                                        <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showTipoReporteDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchTipoReporteFiltro"
                                            @click="open = true"
                                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-blue-400"
                                            placeholder="Buscar tipo de reporte...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                            <ul>
                                                <li wire:click.prevent="clearTipoReporteFiltro"
                                                    @click="open = false"
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                                    Seleccione un tipo
                                                </li>
                                                @foreach ($this->tipoReporteResults as $reporte)
                                                    <li wire:click.prevent="selectTipoReporteFiltro('{{ $reporte['id'] }}')"
                                                        @click="open = false"
                                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                                        {{ $reporte['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bodega</label>
                            <div class="relative">
                                @if($selectedBodegaFiltro)
                                    <div wire:click="clearBodegaFiltro"
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                                        <span class="font-medium text-gray-800">{{ $selectedBodegaFiltro['nombre'] }}</span>
                                        <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showBodegaDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchBodegaFiltro"
                                            @click="open = true"
                                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-blue-400"
                                            placeholder="Buscar bodega...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                            <ul>
                                                <li wire:click.prevent="clearBodegaFiltro"
                                                    @click="open = false"
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                                    Todas las bodegas
                                                </li>
                                                @foreach ($this->bodegaResults as $bodega)
                                                    <li wire:click.prevent="selectBodegaFiltro({{ $bodega['id'] }})"
                                                        @click="open = false"
                                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                                        {{ $bodega['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                            <input
                                type="date"
                                id="fecha_inicio"
                                wire:model="fechaInicio"
                                class="block w-full border-2 border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400">
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                            <input
                                type="date"
                                id="fecha_fin"
                                wire:model="fechaFin"
                                class="block w-full border-2 border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tab Inventario --}}
            @if($tabActivo === 'inventario')
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Reportes de Inventario</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Reporte</label>
                            <div class="relative">
                                @if($selectedTipoReporteFiltro)
                                    <div wire:click="clearTipoReporteFiltro"
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                                        <span class="font-medium text-gray-800">{{ $selectedTipoReporteFiltro['nombre'] }}</span>
                                        <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showTipoReporteDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchTipoReporteFiltro"
                                            @click="open = true"
                                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-blue-400"
                                            placeholder="Buscar tipo de reporte...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                            <ul>
                                                <li wire:click.prevent="clearTipoReporteFiltro"
                                                    @click="open = false"
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                                    Seleccione un tipo
                                                </li>
                                                @foreach ($this->tipoReporteResults as $reporte)
                                                    <li wire:click.prevent="selectTipoReporteFiltro('{{ $reporte['id'] }}')"
                                                        @click="open = false"
                                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                                        {{ $reporte['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label for="producto" class="block text-sm font-medium text-gray-700 mb-2">Producto (Opcional)</label>
                            <div class="relative">
                                @if($selectedProductoFiltro)
                                    <div wire:click="clearProductoFiltro"
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                                        <span class="font-medium text-gray-800">{{ $selectedProductoFiltro['nombre'] }}</span>
                                        <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showProductoDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchProductoFiltro"
                                            @click="open = true"
                                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-blue-400"
                                            placeholder="Buscar producto...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                            <ul>
                                                <li wire:click.prevent="clearProductoFiltro"
                                                    @click="open = false"
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                                    Todos los productos
                                                </li>
                                                @foreach (array_slice($this->productoResults, 0, 10) as $producto)
                                                    <li wire:click.prevent="selectProductoFiltro({{ $producto['id'] }})"
                                                        @click="open = false"
                                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                                        {{ $producto['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bodega</label>
                            <div class="relative">
                                @if($selectedBodegaFiltro)
                                    <div wire:click="clearBodegaFiltro"
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                                        <span class="font-medium text-gray-800">{{ $selectedBodegaFiltro['nombre'] }}</span>
                                        <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showBodegaDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchBodegaFiltro"
                                            @click="open = true"
                                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-blue-400"
                                            placeholder="Buscar bodega...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                            <ul>
                                                <li wire:click.prevent="clearBodegaFiltro"
                                                    @click="open = false"
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                                    Todas las bodegas
                                                </li>
                                                @foreach ($this->bodegaResults as $bodega)
                                                    <li wire:click.prevent="selectBodegaFiltro({{ $bodega['id'] }})"
                                                        @click="open = false"
                                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                                        {{ $bodega['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                            <div class="relative">
                                @if($selectedUsuarioFiltro)
                                    <div wire:click="clearUsuarioFiltro"
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                                        <span class="font-medium text-gray-800">{{ $selectedUsuarioFiltro['nombre'] }}</span>
                                        <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showUsuarioDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchUsuarioFiltro"
                                            @click="open = true"
                                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-blue-400"
                                            placeholder="Buscar usuario...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                            <ul>
                                                <li wire:click.prevent="clearUsuarioFiltro"
                                                    @click="open = false"
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                                    Todos los usuarios
                                                </li>
                                                @foreach ($this->usuarioResults as $usuario)
                                                    <li wire:click.prevent="selectUsuarioFiltro({{ $usuario['id'] }})"
                                                        @click="open = false"
                                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                                        {{ $usuario['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                            <input
                                type="date"
                                id="fecha_inicio"
                                wire:model="fechaInicio"
                                class="block w-full border-2 border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400">
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                            <input
                                type="date"
                                id="fecha_fin"
                                wire:model="fechaFin"
                                class="block w-full border-2 border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Reporte</label>
                            <div class="relative">
                                @if($selectedTipoReporteFiltro)
                                    <div wire:click="clearTipoReporteFiltro"
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                                        <span class="font-medium text-gray-800">{{ $selectedTipoReporteFiltro['nombre'] }}</span>
                                        <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showTipoReporteDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchTipoReporteFiltro"
                                            @click="open = true"
                                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-blue-400"
                                            placeholder="Buscar tipo de reporte...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                            <ul>
                                                <li wire:click.prevent="clearTipoReporteFiltro"
                                                    @click="open = false"
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                                    Seleccione un tipo
                                                </li>
                                                @foreach ($this->tipoReporteResults as $reporte)
                                                    <li wire:click.prevent="selectTipoReporteFiltro('{{ $reporte['id'] }}')"
                                                        @click="open = false"
                                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                                        {{ $reporte['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                            <div class="relative">
                                @if($selectedUsuarioFiltro)
                                    <div wire:click="clearUsuarioFiltro"
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                                        <span class="font-medium text-gray-800">{{ $selectedUsuarioFiltro['nombre'] }}</span>
                                        <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showUsuarioDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchUsuarioFiltro"
                                            @click="open = true"
                                            class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-blue-400"
                                            placeholder="Buscar usuario...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                            <ul>
                                                <li wire:click.prevent="clearUsuarioFiltro"
                                                    @click="open = false"
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                                    Todos los usuarios
                                                </li>
                                                @foreach ($this->usuarioResults as $usuario)
                                                    <li wire:click.prevent="selectUsuarioFiltro({{ $usuario['id'] }})"
                                                        @click="open = false"
                                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                                        {{ $usuario['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                            <input
                                type="date"
                                id="fecha_inicio"
                                wire:model="fechaInicio"
                                class="block w-full border-2 border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400">
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                            <input
                                type="date"
                                id="fecha_fin"
                                wire:model="fechaFin"
                                class="block w-full border-2 border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Botón de Generar Reporte --}}
            <div class="mt-6 flex justify-end">
                <button
                    wire:click="generarReporte"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                    Generar Reporte
                </button>
            </div>
        </div>
    </div>

    {{-- Área de Resultados --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Resultado del Reporte</h2>
            @if(!empty($datosKardex))
                <div class="flex space-x-2">
                    <button
                        wire:click="imprimir"
                        class="bg-gray-700 hover:bg-gray-800 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                        Imprimir
                    </button>
                    <button
                        wire:click="exportarExcel"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                        Exportar a XLS
                    </button>
                </div>
            @endif
        </div>

        {{-- Tabla de Kardex --}}
        @if($tipoReporte === 'kardex' && !empty($datosKardex))
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white text-xs">
                    <thead class="bg-blue-600 text-white uppercase text-xs leading-normal sticky top-0">
                        <tr>
                            <th class="py-2 px-3 text-left">Fecha</th>
                            <th class="py-2 px-3 text-left">Código</th>
                            <th class="py-2 px-3 text-left">Producto</th>
                            <th class="py-2 px-3 text-left">Descripción</th>
                            <th class="py-2 px-3 text-left">Documento</th>
                            <th class="py-2 px-3 text-center">Lote</th>
                            <th class="py-2 px-3 text-right">Entrada</th>
                            <th class="py-2 px-3 text-right">Salida</th>
                            <th class="py-2 px-3 text-right">Saldo</th>
                            <th class="py-2 px-3 text-right">Costo</th>
                            <th class="py-2 px-3 text-right">Costo Entrada</th>
                            <th class="py-2 px-3 text-right">Costo Salida</th>
                            <th class="py-2 px-3 text-right">Costo Inventario</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-xs">
                        @foreach($datosKardex as $index => $movimiento)
                            <tr class="border-b border-gray-200 hover:bg-gray-100 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="py-2 px-3 text-left whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y') }}
                                </td>
                                <td class="py-2 px-3 text-left">{{ $movimiento['codigo'] }}</td>
                                <td class="py-2 px-3 text-left">
                                    <div class="font-medium">{{ $movimiento['producto'] }}</div>
                                    <div class="text-gray-500 text-xs">{{ $movimiento['categoria'] }}</div>
                                </td>
                                <td class="py-2 px-3 text-left whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        @if(str_contains($movimiento['tipo_movimiento'], 'ENTRADA') || $movimiento['tipo_movimiento'] === 'COMPRA' || $movimiento['tipo_movimiento'] === 'DEVOLUCION')
                                            bg-green-100 text-green-800
                                        @elseif(str_contains($movimiento['tipo_movimiento'], 'SALIDA'))
                                            bg-red-100 text-red-800
                                        @else
                                            bg-blue-100 text-blue-800
                                        @endif
                                    ">
                                        {{ $movimiento['descripcion'] }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-left">{{ $movimiento['documento'] }}</td>
                                <td class="py-2 px-3 text-center">
                                    @if($movimiento['lote_id'])
                                        <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs font-mono">
                                            #{{ $movimiento['lote_id'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-right {{ $movimiento['cantidad_entrada'] > 0 ? 'text-green-600 font-semibold' : '' }}">
                                    {{ $movimiento['cantidad_entrada'] > 0 ? number_format($movimiento['cantidad_entrada'], 0) : '-' }}
                                </td>
                                <td class="py-2 px-3 text-right {{ $movimiento['cantidad_salida'] > 0 ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $movimiento['cantidad_salida'] > 0 ? number_format($movimiento['cantidad_salida'], 0) : '-' }}
                                </td>
                                <td class="py-2 px-3 text-right font-bold {{ $movimiento['saldo'] > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                                    {{ number_format($movimiento['saldo'], 0) }}
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap">Q {{ number_format($movimiento['costo_unitario'], 2) }}</td>
                                <td class="py-2 px-3 text-right whitespace-nowrap {{ $movimiento['costo_entrada'] > 0 ? 'text-green-600' : '' }}">
                                    {{ $movimiento['costo_entrada'] > 0 ? 'Q ' . number_format($movimiento['costo_entrada'], 2) : '-' }}
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap {{ $movimiento['costo_salida'] > 0 ? 'text-red-600' : '' }}">
                                    {{ $movimiento['costo_salida'] > 0 ? 'Q ' . number_format($movimiento['costo_salida'], 2) : '-' }}
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap font-bold text-blue-700">
                                    Q {{ number_format($movimiento['costo_inventario'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td colspan="13" class="py-3 px-3 text-right text-sm">
                                Total de movimientos: {{ count($datosKardex) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            {{-- Tabla de Placeholder cuando no hay datos --}}
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
        @endif
    </div>
</div>

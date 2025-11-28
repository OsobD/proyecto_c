<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Traslados', 'url' => route('traslados')],
        ['label' => 'Historial'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Historial de Traslados</h1>
        <div class="flex space-x-2">
            <a href="{{ route('requisiciones') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                + Requisición
            </a>
            <a href="{{ route('traslados') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                + Traslado
            </a>
        </div>
    </div>

    {{-- Mensajes de éxito --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Filtros de Búsqueda</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input
                    type="text"
                    id="search"
                    wire:model.live.debounce.300ms="search"
                    class="block w-full py-2.5 px-4 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400"
                    placeholder="Correlativo, origen, destino...">
            </div>

            {{-- Filtro de Tipo con búsqueda --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <div class="relative">
                    @if($selectedTipoFiltro)
                        <div wire:click="clearTipoFiltro"
                             class="flex items-center justify-between w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                            <span class="font-medium text-gray-800">{{ $selectedTipoFiltro['nombre'] }}</span>
                            <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: @entangle('showTipoDropdown').live }" @click.outside="open = false">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="searchTipoFiltro"
                                @click="open = true"
                                class="block w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400"
                                placeholder="Buscar tipo...">
                            <div x-show="open"
                                 x-transition
                                 class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                <ul>
                                    <li wire:click.prevent="clearTipoFiltro"
                                        @click="open = false"
                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                        Todos los tipos
                                    </li>
                                    @foreach ($this->tipoResults as $tipo)
                                        <li wire:click.prevent="selectTipoFiltro('{{ $tipo['id'] }}')"
                                            @click="open = false"
                                            class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                            {{ $tipo['nombre'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Filtro de Estado con búsqueda --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <div class="relative">
                    @if($selectedEstadoFiltro)
                        <div wire:click="clearEstadoFiltro"
                             class="flex items-center justify-between w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 transition-all duration-200 bg-blue-50">
                            <span class="font-medium text-gray-800">{{ $selectedEstadoFiltro['nombre'] }}</span>
                            <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: @entangle('showEstadoDropdown').live }" @click.outside="open = false">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="searchEstadoFiltro"
                                @click="open = true"
                                class="block w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400"
                                placeholder="Buscar estado...">
                            <div x-show="open"
                                 x-transition
                                 class="absolute z-10 w-full bg-white border-2 border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-xl">
                                <ul>
                                    <li wire:click.prevent="clearEstadoFiltro"
                                        @click="open = false"
                                        class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-gray-600 font-medium border-b border-gray-200">
                                        Todos los estados
                                    </li>
                                    @foreach ($this->estadoResults as $estado)
                                        <li wire:click.prevent="selectEstadoFiltro('{{ $estado['id'] }}')"
                                            @click="open = false"
                                            class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-150">
                                            {{ $estado['nombre'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Fecha Inicio con Flatpickr --}}
            <div wire:ignore x-data="{
                picker: null,
                fechaActual: @entangle('fechaInicio').live,
                initFlatpickr() {
                    const input = this.$refs.fechaInicio;

                    // Destruir instancia previa si existe
                    if (this.picker) {
                        this.picker.destroy();
                        this.picker = null;
                    }

                    // Configuración de Flatpickr
                    this.picker = flatpickr(input, {
                        dateFormat: 'Y-m-d',
                        locale: {
                            ...flatpickr.l10ns.es,
                            months: {
                                shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                                longhand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
                            },
                            weekdays: {
                                shorthand: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                                longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
                            }
                        },
                        altInput: true,
                        altFormat: 'd/m/Y',
                        allowInput: false,
                        clickOpens: true,
                        disableMobile: true,
                        maxDate: 'today',
                        defaultDate: this.fechaActual || null,
                        onChange: (selectedDates, dateStr, instance) => {
                            this.fechaActual = dateStr;
                            console.log('Fecha Inicio seleccionada:', dateStr);
                        },
                        onClose: (selectedDates, dateStr, instance) => {
                            // No hacer nada al cerrar
                        }
                    });
                },
                resetPicker() {
                    if (this.picker) {
                        this.picker.clear();
                        this.fechaActual = '';
                    }
                }
            }"
            x-init="initFlatpickr()"
            @limpiar-filtros.window="resetPicker()">
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="inline w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Fecha Inicio
                </label>
                <input
                    x-ref="fechaInicio"
                    type="text"
                    id="fecha_inicio"
                    placeholder="Seleccionar fecha..."
                    readonly
                    class="block w-full py-2.5 px-4 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400 cursor-pointer bg-white">
            </div>

            {{-- Fecha Fin con Flatpickr --}}
            <div wire:ignore x-data="{
                picker: null,
                fechaActual: @entangle('fechaFin').live,
                initFlatpickr() {
                    const input = this.$refs.fechaFin;

                    // Destruir instancia previa si existe
                    if (this.picker) {
                        this.picker.destroy();
                        this.picker = null;
                    }

                    // Configuración de Flatpickr
                    this.picker = flatpickr(input, {
                        dateFormat: 'Y-m-d',
                        locale: {
                            ...flatpickr.l10ns.es,
                            months: {
                                shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                                longhand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
                            },
                            weekdays: {
                                shorthand: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                                longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
                            }
                        },
                        altInput: true,
                        altFormat: 'd/m/Y',
                        allowInput: false,
                        clickOpens: true,
                        disableMobile: true,
                        maxDate: 'today',
                        defaultDate: this.fechaActual || null,
                        onChange: (selectedDates, dateStr, instance) => {
                            this.fechaActual = dateStr;
                            console.log('Fecha Fin seleccionada:', dateStr);
                        },
                        onClose: (selectedDates, dateStr, instance) => {
                            // No hacer nada al cerrar
                        }
                    });
                },
                resetPicker() {
                    if (this.picker) {
                        this.picker.clear();
                        this.fechaActual = '';
                    }
                }
            }"
            x-init="initFlatpickr()"
            @limpiar-filtros.window="resetPicker()">
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="inline w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Fecha Fin
                </label>
                <input
                    x-ref="fechaFin"
                    type="text"
                    id="fecha_fin"
                    placeholder="Seleccionar fecha..."
                    readonly
                    class="block w-full py-2.5 px-4 border-2 border-gray-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400 cursor-pointer bg-white">
            </div>

            <div class="flex items-end">
                <button
                    wire:click="limpiarFiltros"
                    @click="$dispatch('limpiar-filtros')"
                    class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                    <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla de Traslados --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">
                Traslados encontrados: <span class="text-blue-600">{{ $this->trasladosFiltrados->total() }}</span>
            </h2>
            <div class="flex items-center gap-2">
                <label for="perPage" class="text-sm font-medium text-gray-700">Mostrar:</label>
                <select
                    id="perPage"
                    wire:model.live="perPage"
                    class="py-2 px-3 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-700">por página</span>
            </div>
        </div>

        {{-- Leyenda --}}
        <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200 flex gap-6 text-sm">
            <div class="flex items-center gap-2">
                <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                    No Consumibles
                </span>
                <span class="text-gray-600">Se agregan a tarjeta de responsabilidad</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="bg-amber-200 text-amber-800 py-1 px-3 rounded-full text-xs font-semibold">
                    Consumibles
                </span>
                <span class="text-gray-600">Solo registro de retiro (sin tarjeta)</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">
                            <button
                                wire:click="sortBy('tipo')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                Tipo
                                @if($sortField === 'tipo')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-left">Productos</th>
                        <th class="py-3 px-6 text-left">
                            <button
                                wire:click="sortBy('correlativo')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                Correlativo
                                @if($sortField === 'correlativo')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-left">
                            <button
                                wire:click="sortBy('origen')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                Origen
                                @if($sortField === 'origen')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-left">
                            <button
                                wire:click="sortBy('destino')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                Destino
                                @if($sortField === 'destino')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-left">
                            <button
                                wire:click="sortBy('usuario')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                Usuario
                                @if($sortField === 'usuario')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-left">
                            <button
                                wire:click="sortBy('fecha')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                Fecha
                                @if($sortField === 'fecha')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-center">
                            <button
                                wire:click="sortBy('productos_count')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors mx-auto">
                                Cantidad
                                @if($sortField === 'productos_count')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-center">
                            <button
                                wire:click="sortBy('estado')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors mx-auto">
                                Estado
                                @if($sortField === 'estado')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($this->trasladosFiltrados as $traslado)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 {{ !$traslado['activo'] ? 'bg-red-50' : '' }}">
                            <td class="py-3 px-6 text-left">
                                <div class="flex items-center gap-2">
                                    @if($traslado['tipo'] === 'Requisición')
                                        <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                                            Requisición
                                        </span>
                                        @if(!$traslado['activo'])
                                            <span class="bg-red-200 text-red-800 py-1 px-2 rounded-full text-xs font-semibold">
                                                Eliminado
                                            </span>
                                        @endif
                                    @elseif($traslado['tipo'] === 'Traslado')
                                        <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                            Traslado
                                        </span>
                                    @else
                                        <span class="bg-purple-200 text-purple-800 py-1 px-3 rounded-full text-xs font-semibold">
                                            Devolución
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                @if(isset($traslado['tipo_badge']) && isset($traslado['tipo_color']))
                                    <span class="bg-{{ $traslado['tipo_color'] }}-200 text-{{ $traslado['tipo_color'] }}-800 py-1 px-3 rounded-full text-xs font-semibold whitespace-nowrap">
                                        {{ $traslado['tipo_badge'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left font-medium">{{ $traslado['correlativo'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $traslado['origen'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $traslado['destino'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $traslado['usuario'] }}</td>
                            <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($traslado['fecha'])->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="bg-gray-100 text-gray-800 py-1 px-3 rounded-full text-xs font-semibold">
                                    {{ $traslado['productos_count'] }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($traslado['estado'] === 'Completado')
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Completado
                                    </span>
                                @else
                                    <span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if(isset($traslado['agrupado']) && $traslado['agrupado'])
                                        <x-action-button
                                            type="view"
                                            title="Ver detalle"
                                            wire:click="verDetalle({{ json_encode($traslado['ids_agrupados']) }}, {{ json_encode($traslado['tipos_agrupados']) }}, '{{ $traslado['correlativo'] }}')" />
                                    @else
                                        <x-action-button
                                            type="view"
                                            title="Ver detalle"
                                            wire:click="verDetalle({{ $traslado['id'] }}, '{{ $traslado['tipo_clase'] }}')" />
                                        @if($traslado['tipo'] === 'Requisición' && $traslado['activo'])
                                            <x-action-button
                                                type="delete"
                                                title="Solicitar eliminación"
                                                wire:click="abrirModalEliminar({{ $traslado['id'] }}, '{{ $traslado['tipo_clase'] }}')" />
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-gray-500">
                                No se encontraron traslados con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de Visualización de Detalle --}}
    <div x-data="{
            show: @entangle('showModalVer').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalVer">
        <div class="relative p-6 border w-full max-w-3xl shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Detalle de {{ $movimientoSeleccionado['tipo'] ?? 'Movimiento' }}</h3>
                <button wire:click="closeModalVer" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($movimientoSeleccionado)
                <div class="space-y-4">
                    {{-- Información del movimiento --}}
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Origen:</p>
                                <p class="font-semibold">{{ $movimientoSeleccionado['origen'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Destino:</p>
                                <p class="font-semibold">{{ $movimientoSeleccionado['destino'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Correlativo:</p>
                                <p class="font-semibold">{{ $movimientoSeleccionado['correlativo'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Fecha:</p>
                                <p class="font-semibold">{{ $movimientoSeleccionado['fecha'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if(isset($movimientoSeleccionado['observaciones']) && $movimientoSeleccionado['observaciones'])
                            <div class="mt-4">
                                <p class="text-sm text-gray-600">Observaciones:</p>
                                <p class="font-semibold">{{ $movimientoSeleccionado['observaciones'] }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Detalle de productos --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Productos:</h4>
                        <div class="overflow-x-auto max-h-64 overflow-y-auto border rounded-md">
                            <table class="min-w-full bg-white text-sm">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Código</th>
                                        <th class="py-2 px-3 text-left">Descripción</th>
                                        <th class="py-2 px-3 text-center">Tipo</th>
                                        <th class="py-2 px-3 text-center">Cantidad</th>
                                        <th class="py-2 px-3 text-right">Precio Unit.</th>
                                        <th class="py-2 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($movimientoSeleccionado['productos']) && count($movimientoSeleccionado['productos']) > 0)
                                        @foreach($movimientoSeleccionado['productos'] as $producto)
                                            <tr class="border-t hover:bg-gray-50">
                                                <td class="py-2 px-3 font-mono">{{ $producto['codigo'] }}</td>
                                                <td class="py-2 px-3">{{ $producto['descripcion'] }}</td>
                                                <td class="py-2 px-3 text-center">
                                                    @if(isset($producto['es_consumible']))
                                                        @if($producto['es_consumible'])
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                                Consumible
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                No Consumible
                                                            </span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="py-2 px-3 text-center">{{ $producto['cantidad'] }}</td>
                                                <td class="py-2 px-3 text-right">Q{{ number_format($producto['precio'], 2) }}</td>
                                                <td class="py-2 px-3 text-right font-semibold">Q{{ number_format($producto['subtotal'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="py-4 text-center text-gray-500">No hay productos en este movimiento</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="bg-blue-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total:</span>
                            <span class="text-2xl font-bold text-blue-600">Q{{ number_format($movimientoSeleccionado['total'] ?? 0, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Total de productos: {{ isset($movimientoSeleccionado['productos']) ? count($movimientoSeleccionado['productos']) : 0 }}
                        </p>
                    </div>

                    {{-- Botón de cerrar --}}
                    <div class="flex justify-end mt-6">
                        <button
                            wire:click="closeModalVer"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg">
                            Cerrar
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de Solicitud de Eliminación --}}
    <div x-data="{
            show: @entangle('showModalEliminar').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalEliminar">
        <div class="relative p-6 border w-full max-w-md shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Solicitar Eliminación</h3>
                <button wire:click="closeModalEliminar" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Importante:</strong> Esta solicitud será enviada a un administrador para su aprobación. 
                        El registro permanecerá visible en el historial.
                    </p>
                </div>

                <div>
                    <label for="justificacion" class="block text-sm font-medium text-gray-700 mb-2">
                        Justificación <span class="text-red-600">*</span>
                    </label>
                    <textarea
                        id="justificacion"
                        wire:model="justificacionEliminacion"
                        rows="4"
                        class="block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Explique el motivo de la eliminación..."></textarea>
                    @error('justificacionEliminacion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button
                        type="button"
                        wire:click="closeModalEliminar"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="solicitarEliminacion"
                        wire:loading.attr="disabled"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="solicitarEliminacion">Solicitar Eliminación</span>
                        <span wire:loading wire:target="solicitarEliminacion">Enviando...</span>
                    </button>
                </div>
            </div>
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

        /* Estilos personalizados para Flatpickr - Tema moderno y suave */
        .flatpickr-calendar {
            background: white !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
            border: none !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            padding: 0 !important;
            margin-top: 8px !important;
        }

        .flatpickr-calendar.open {
            animation: slideDown 0.2s ease-out !important;
        }

        /* Header del calendario */
        .flatpickr-months {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            padding: 18px 16px !important;
            border-radius: 16px 16px 0 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            min-height: 64px !important;
        }

        .flatpickr-months .flatpickr-month {
            background: transparent !important;
            color: white !important;
            flex: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: auto !important;
        }

        .flatpickr-current-month {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 12px !important;
            height: auto !important;
            position: static !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: white !important;
            color: #1e40af !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            border: 2px solid rgba(255, 255, 255, 0.3) !important;
            padding: 8px 16px !important;
            border-radius: 8px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            min-width: 85px !important;
            appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231e40af' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 8px center !important;
            background-size: 10px !important;
            padding-right: 30px !important;
            flex-shrink: 0 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
            background-color: #f0f9ff !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231e40af' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 8px center !important;
            background-size: 10px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12) !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3) !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231e40af' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 8px center !important;
            background-size: 10px !important;
        }

        /* Estilos para las opciones del dropdown de mes - Limpio como el de proveedor */
        .flatpickr-monthDropdown-months option {
            background: white !important;
            color: #334155 !important;
            padding: 10px 16px !important;
            font-weight: 500 !important;
            font-size: 14px !important;
            border: none !important;
            border-bottom: 1px solid #e5e7eb !important;
        }

        .flatpickr-monthDropdown-months option:last-child {
            border-bottom: none !important;
        }

        .flatpickr-monthDropdown-months option:hover {
            background: #f8fafc !important;
            color: #3b82f6 !important;
        }

        .flatpickr-monthDropdown-months option:checked,
        .flatpickr-monthDropdown-months option[selected] {
            background: #eff6ff !important;
            color: #3b82f6 !important;
            font-weight: 600 !important;
        }

        .flatpickr-current-month .numInputWrapper {
            width: auto !important;
            min-width: 75px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: static !important;
            flex-shrink: 0 !important;
        }

        .flatpickr-current-month .numInputWrapper input,
        .flatpickr-current-month .cur-year {
            color: white !important;
            font-weight: 700 !important;
            font-size: 16px !important;
            background: rgba(255, 255, 255, 0.25) !important;
            border: 2px solid rgba(255, 255, 255, 0.3) !important;
            padding: 8px 14px !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            text-align: center !important;
            width: 75px !important;
            letter-spacing: 0.5px !important;
            line-height: 1.4 !important;
            height: 38px !important;
            min-height: 38px !important;
            box-sizing: border-box !important;
            display: block !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }

        .flatpickr-current-month .numInputWrapper:hover input,
        .flatpickr-current-month .numInputWrapper:hover .cur-year {
            background: rgba(255, 255, 255, 0.35) !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12) !important;
        }

        /* Flechas del año (arriba y abajo del input de año) */
        .flatpickr-current-month .numInputWrapper span {
            display: none !important;
        }

        /* Flechas de navegación de mes - CENTRADAS */
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            position: static !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            fill: white !important;
            padding: 10px !important;
            border-radius: 10px !important;
            transition: all 0.2s ease !important;
            width: 44px !important;
            height: 44px !important;
            min-width: 44px !important;
            min-height: 44px !important;
            top: auto !important;
            transform: none !important;
            flex-shrink: 0 !important;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: scale(1.1) !important;
        }

        .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-months .flatpickr-next-month svg {
            fill: white !important;
            width: 18px !important;
            height: 18px !important;
        }

        /* Días de la semana */
        .flatpickr-weekdays {
            background: #f8fafc !important;
            padding: 12px 0 8px 0 !important;
            border-bottom: 1px solid #e5e7eb !important;
        }

        .flatpickr-weekday {
            color: #64748b !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            text-transform: uppercase !important;
        }

        /* Contenedor de días */
        .flatpickr-days {
            padding: 8px !important;
        }

        /* Días individuales */
        .flatpickr-day {
            border-radius: 10px !important;
            border: none !important;
            color: #334155 !important;
            font-weight: 500 !important;
            margin: 2px !important;
            transition: all 0.2s ease !important;
            height: 38px !important;
            line-height: 38px !important;
        }

        /* Día actual (hoy) */
        .flatpickr-day.today {
            border: 2px solid #3b82f6 !important;
            background: white !important;
            color: #3b82f6 !important;
            font-weight: 700 !important;
        }

        .flatpickr-day.today:hover {
            background: #eff6ff !important;
            border-color: #3b82f6 !important;
        }

        /* Día seleccionado */
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            border: none !important;
            color: white !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important;
        }

        /* Hover en días normales */
        .flatpickr-day:not(.selected):not(.startRange):not(.endRange):not(.flatpickr-disabled):hover {
            background: #eff6ff !important;
            border: none !important;
            color: #3b82f6 !important;
            transform: scale(1.05) !important;
        }

        /* Días deshabilitados (fuera del mes) */
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #cbd5e1 !important;
        }

        .flatpickr-day.flatpickr-disabled {
            color: #e2e8f0 !important;
        }

        .flatpickr-day.flatpickr-disabled:hover {
            background: transparent !important;
            transform: none !important;
            cursor: not-allowed !important;
        }
    </style>

    {{-- Scripts de Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
</div>

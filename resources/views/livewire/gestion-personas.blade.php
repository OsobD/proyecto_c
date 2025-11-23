{{--
    Vista: Gestión de Personas
    Descripción: CRUD completo de personas con búsqueda, filtros (modal), ordenamiento y gestión de tarjetas de responsabilidad.
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Personas'],
    ]" />

    {{-- Encabezado con título --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Personas</h1>
    </div>

    {{-- Barra de búsqueda y filtros --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            {{-- Búsqueda --}}
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar persona</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        placeholder="Buscar por nombre, apellido, DPI, correo..."
                        autocomplete="off">
                </div>
            </div>

            {{-- Botón de Filtros / Ajustes --}}
            <div>
                <button
                    wire:click="openFilterModal"
                    class="flex items-center gap-2 bg-white border-2 border-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span>Filtros / Ajustes</span>
                    @if($showInactive)
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                        </span>
                    @endif
                </button>
            </div>

            {{-- Botón Nueva Persona --}}
            <div>
                <button
                    wire:click="openModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg whitespace-nowrap shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                    + Nueva Persona
                </button>
            </div>
        </div>
    </div>

    {{-- Mensajes flash --}}
    @if (session()->has('message'))
        <div class="relative fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg z-50 animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="relative fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg z-50 animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    {{-- Contenedor principal --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        {{-- Tabla de personas --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">
                            <button
                                wire:click="sortBy('id')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                ID
                                @if($sortField === 'id')
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
                                wire:click="sortBy('nombres')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                Nombres
                                @if($sortField === 'nombres')
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
                                wire:click="sortBy('apellidos')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                Apellidos
                                @if($sortField === 'apellidos')
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
                                wire:click="sortBy('dpi')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                DPI
                                @if($sortField === 'dpi')
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
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($personas as $persona)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <span class="font-semibold text-gray-800">{{ $persona->id }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="font-medium">{{ $persona->nombres }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="font-medium">{{ $persona->apellidos }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="font-mono text-gray-700">{{ $persona->dpi ?? 'N/A' }}</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <button
                                    wire:click="toggleEstado({{ $persona->id }})"
                                    wire:confirm="¿Está seguro de que desea {{ $persona->estado ? 'desactivar' : 'activar' }} a {{ $persona->nombres }} {{ $persona->apellidos }}?{{ $persona->estado ? '\n\nEsto también desactivará todas sus tarjetas de responsabilidad activas.' : '' }}"
                                    class="py-1 px-3 rounded-full text-xs font-semibold {{ $persona->estado ? 'bg-green-200 text-green-800 hover:bg-green-300' : 'bg-red-200 text-red-800 hover:bg-red-300' }}">
                                    {{ $persona->estado ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Botón para ver productos solicitados --}}
                                    <button
                                        wire:click="toggleConsumibles({{ $persona->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-md transition-all duration-200 {{ $personaIdConsumiblesExpandida === $persona->id ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        title="Ver historial de productos solicitados">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </button>

                                    {{-- Botón Editar --}}
                                    <x-action-button
                                        type="edit"
                                        wire:click="edit({{ $persona->id }})"
                                        title="Editar persona" />
                                </div>
                            </td>
                        </tr>

                        {{-- Expansión de productos consumibles solicitados (acordeón) --}}
                        @if($personaIdConsumiblesExpandida === $persona->id)
                            <tr>
                                <td colspan="6" class="bg-green-50 p-6 shadow-inner">
                                    <div class="mb-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                Historial de productos solicitados por {{ $persona->nombres }} {{ $persona->apellidos }}
                                            </h3>
                                        </div>

                                        @php
                                            // Obtener las salidas de la persona
                                            $salidasPersona = $persona->salidas()
                                                ->with(['detalles.producto.categoria', 'detalles.lote.bodega', 'tipoSalida'])
                                                ->orderBy('fecha', 'desc')
                                                ->get();

                                            // Obtener SOLO los productos CONSUMIBLES de las salidas
                                            $productosSalidas = collect();
                                            foreach ($salidasPersona as $salida) {
                                                foreach ($salida->detalles as $detalle) {
                                                    // Filtrar solo productos consumibles (es_consumible == 1 o true)
                                                    if ($detalle->producto && $detalle->producto->es_consumible == 1) {
                                                        $productosSalidas->push([
                                                            'salida_id' => $salida->id,
                                                            'fecha_salida' => $salida->fecha,
                                                            'tipo_salida' => $salida->tipoSalida ? $salida->tipoSalida->nombre : 'N/A',
                                                            'producto_codigo' => $detalle->producto->id,
                                                            'producto_nombre' => $detalle->producto->descripcion,
                                                            'categoria' => $detalle->producto->categoria ? $detalle->producto->categoria->nombre : 'Sin categoría',
                                                            'es_consumible' => $detalle->producto->es_consumible,
                                                            'lote_id' => $detalle->lote ? $detalle->lote->id : 'N/A',
                                                            'cantidad' => $detalle->cantidad,
                                                            'precio_unitario' => $detalle->precio_salida,
                                                            'total' => $detalle->cantidad * $detalle->precio_salida,
                                                            'bodega' => $detalle->lote && $detalle->lote->bodega ? $detalle->lote->bodega->nombre : 'N/A',
                                                            'descripcion_salida' => $salida->descripcion ?? '-',
                                                        ]);
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if($productosSalidas->count() > 0)
                                            <div class="overflow-x-auto rounded-lg border border-gray-300">
                                                <table class="min-w-full bg-white">
                                                    <thead class="bg-green-100 text-gray-700 text-sm">
                                                        <tr>
                                                            <th class="py-3 px-4 text-center">Salida ID</th>
                                                            <th class="py-3 px-4 text-center">Fecha</th>
                                                            <th class="py-3 px-4 text-left">Tipo</th>
                                                            <th class="py-3 px-4 text-left">Código</th>
                                                            <th class="py-3 px-4 text-left">Producto</th>
                                                            <th class="py-3 px-4 text-left">Categoría</th>
                                                            <th class="py-3 px-4 text-center">Lote ID</th>
                                                            <th class="py-3 px-4 text-center">Cantidad</th>
                                                            <th class="py-3 px-4 text-right">Precio Unit.</th>
                                                            <th class="py-3 px-4 text-right">Total</th>
                                                            <th class="py-3 px-4 text-left">Bodega</th>
                                                            <th class="py-3 px-4 text-left">Descripción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 text-sm">
                                                        @foreach($productosSalidas as $index => $pc)
                                                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors" wire:key="salida-{{ $persona->id }}-{{ $index }}">
                                                                <td class="py-3 px-4 text-center">
                                                                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-semibold">
                                                                        #{{ $pc['salida_id'] }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-center text-xs">
                                                                    {{ \Carbon\Carbon::parse($pc['fecha_salida'])->format('d/m/Y') }}
                                                                </td>
                                                                <td class="py-3 px-4 text-left">
                                                                    <span class="text-gray-700 text-xs">{{ $pc['tipo_salida'] }}</span>
                                                                </td>
                                                                <td class="py-3 px-4 text-left">
                                                                    <span class="font-mono text-gray-700 font-semibold">{{ $pc['producto_codigo'] }}</span>
                                                                </td>
                                                                <td class="py-3 px-4 text-left">
                                                                    <span class="font-medium text-gray-800">{{ $pc['producto_nombre'] }}</span>
                                                                </td>
                                                                <td class="py-3 px-4 text-left text-sm text-gray-500">
                                                                    {{ $pc['categoria'] }}
                                                                </td>
                                                                <td class="py-3 px-4 text-center">
                                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">
                                                                        #{{ $pc['lote_id'] }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-center">
                                                                    <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs font-bold">
                                                                        {{ $pc['cantidad'] }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-right">
                                                                    <span class="font-medium text-gray-700">Q{{ number_format($pc['precio_unitario'], 2) }}</span>
                                                                </td>
                                                                <td class="py-3 px-4 text-right">
                                                                    <span class="font-semibold text-green-600">Q{{ number_format($pc['total'], 2) }}</span>
                                                                </td>
                                                                <td class="py-3 px-4 text-left">
                                                                    <span class="text-gray-700">{{ $pc['bodega'] }}</span>
                                                                </td>
                                                                <td class="py-3 px-4 text-left text-xs text-gray-500 max-w-xs truncate">
                                                                    {{ $pc['descripcion_salida'] }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- Resumen --}}
                                            <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-gray-700 font-medium">Total de productos:</span>
                                                        <span class="text-2xl font-bold text-green-600">{{ $productosSalidas->count() }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-gray-700 font-medium">Total invertido:</span>
                                                        <span class="text-2xl font-bold text-green-600">Q{{ number_format($productosSalidas->sum('total'), 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-8 text-gray-500 bg-white rounded-lg border border-gray-200">
                                                <p>No hay productos solicitados por esta persona.</p>
                                                <p class="text-sm mt-2">El historial aparecerá aquí cuando se realicen salidas de productos.</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-gray-500">
                                No se encontraron personas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($personas->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $personas->links() }}
            </div>
        @endif
    </div>

    {{-- Modal: Crear/Editar Persona --}}
    <div x-data="{
            show: @entangle('showModal').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModal"
         wire:ignore.self>
        <div class="relative border w-full max-w-2xl shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-hidden"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="p-8 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        {{ $editMode ? 'Editar Persona' : 'Nueva Persona' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nombres --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombres *
                            </label>
                            <input type="text" wire:model="nombres"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('nombres') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('nombres')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Apellidos --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Apellidos *
                            </label>
                            <input type="text" wire:model="apellidos"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('apellidos') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('apellidos')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- DPI --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                DPI * (13 dígitos)
                            </label>
                            <input type="text" wire:model="dpi" maxlength="13"
                                   pattern="[0-9]{13}"
                                   inputmode="numeric"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('dpi') border-red-500 ring-2 ring-red-200 @enderror"
                                   placeholder="1234567890123">
                            @error('dpi')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Teléfono --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono (8 dígitos)</label>
                            <input type="text" wire:model="telefono"
                                   maxlength="8"
                                   pattern="[0-9]{8}"
                                   inputmode="numeric"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('telefono') border-red-500 ring-2 ring-red-200 @enderror"
                                   placeholder="55555555">
                            @error('telefono')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Correo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                            <input type="email" wire:model="correo"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('correo') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('correo')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="save">
                                {{ $editMode ? '✓ Actualizar' : '✓ Guardar' }}
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Filtros y Ajustes --}}
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
        <div class="relative border w-full max-w-lg shadow-2xl rounded-xl bg-white max-h-[85vh] flex flex-col overflow-hidden"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            
            {{-- Header (Fijo) --}}
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

            {{-- Body (Scrollable) --}}
            <div class="p-5 overflow-y-auto flex-1">
                {{-- Sección: Ordenar por --}}
                <div class="mb-6">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Ordenar por</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            'id' => 'ID',
                            'nombres' => 'Nombres',
                            'apellidos' => 'Apellidos',
                            'dpi' => 'DPI'
                        ] as $field => $label)
                            <button
                                wire:click="sortBy('{{ $field }}')"
                                class="flex items-center justify-between px-3 py-2 rounded-lg border {{ $sortField === $field ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }} transition-all text-sm font-medium">
                                <span>{{ $label }}</span>
                                @if($sortField === $field)
                                    <span class="text-xs font-bold">{{ $sortDirection === 'asc' ? 'ASC' : 'DESC' }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Sección: Opciones de Visualización --}}
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Visualización</h4>
                    
                    <label class="custom-checkbox-container gap-3 cursor-pointer select-none p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors w-full flex items-center">
                        <input type="checkbox" wire:model.live="showInactive">
                        <div class="custom-checkmark"></div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-800">Mostrar personas desactivadas</span>
                            <span class="text-xs text-gray-500">Incluir personas que han sido dadas de baja</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Footer (Fijo) --}}
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

    {{-- Modal Reutilizable: Crear Nueva Persona --}}
    @livewire('modal-persona')

    <style>
        /* Ocultar elementos hasta que Alpine.js esté listo */
        [x-cloak] {
            display: none !important;
        }

        /* Animación de mensajes flash */
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Checkbox personalizado */
        .custom-checkbox-container {
            display: inline-flex;
            align-items: center;
            position: relative;
        }

        .custom-checkbox-container input {
            display: none;
        }

        .custom-checkmark {
            position: relative;
            display: inline-block;
            height: 1.25em;
            width: 1.25em;
            background-color: transparent;
            border-radius: 0.25em;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .custom-checkmark:after {
            content: "";
            position: absolute;
            transition: all 0.3s ease;
            box-sizing: border-box;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            border: 0.125em solid #4B5563;
            border-radius: 0.25em;
            transform: rotate(0deg);
        }

        .custom-checkbox-container input:checked ~ .custom-checkmark {
            background-color: #2563EB;
        }

        .custom-checkbox-container input:checked ~ .custom-checkmark:after {
            left: 0.45em;
            top: 0.25em;
            width: 0.35em;
            height: 0.7em;
            border-color: transparent white white transparent;
            border-width: 0 0.15em 0.15em 0;
            border-radius: 0;
            transform: rotate(45deg);
        }
    </style>
</div>

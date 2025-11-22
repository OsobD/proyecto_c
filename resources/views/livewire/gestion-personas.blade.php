<div>
    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Personas</h1>
        <div class="flex items-center space-x-3">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="showAllPersonas" class="mr-2">
                <span class="text-sm text-gray-700">Mostrar inactivos</span>
            </label>
            <button wire:click="$dispatch('abrirModalPersona')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                + Nueva Persona
            </button>
        </div>
    </div>

    {{-- Mensajes --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 animate-fade-in" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animate-fade-in" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    {{-- Contenedor principal --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Búsqueda --}}
        <div class="mb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                       placeholder="Buscar por nombre, apellido, DPI, correo o teléfono...">
            </div>
        </div>

        {{-- Tabla --}}
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
                                        <span class="text-blue-600">↑</span>
                                    @else
                                        <span class="text-blue-600">↓</span>
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
                                        <span class="text-blue-600">↑</span>
                                    @else
                                        <span class="text-blue-600">↓</span>
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
                                        <span class="text-blue-600">↑</span>
                                    @else
                                        <span class="text-blue-600">↓</span>
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
                                        <span class="text-blue-600">↑</span>
                                    @else
                                        <span class="text-blue-600">↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-left">Teléfono</th>
                        <th class="py-3 px-6 text-left">Correo</th>
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
                            <td class="py-3 px-6 text-left">{{ $persona->telefono ?? 'N/A' }}</td>
                            <td class="py-3 px-6 text-left">{{ $persona->correo ?? 'N/A' }}</td>
                            <td class="py-3 px-6 text-center">
                                @if($persona->estado)
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">Activo</span>
                                @else
                                    <span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    {{-- Botón para ver productos solicitados --}}
                                    <button
                                        wire:click="toggleConsumibles({{ $persona->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-md transition-all duration-200 {{ $personaIdConsumiblesExpandida === $persona->id ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        title="Ver historial de productos solicitados">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </button>

                                    {{-- Botón Editar con colores originales --}}
                                    <button
                                        wire:click="edit({{ $persona->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-yellow-100 hover:bg-yellow-200 transition-colors"
                                        title="Editar persona">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                                        </svg>
                                    </button>

                                    @if($persona->estado)
                                        {{-- Botón Desactivar con colores originales --}}
                                        <button
                                            wire:click="toggleEstado({{ $persona->id }})"
                                            wire:confirm="¿Está seguro de que desea desactivar esta persona?"
                                            class="w-8 h-8 flex items-center justify-center rounded-md bg-red-100 hover:bg-red-200 transition-colors"
                                            title="Desactivar persona">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @else
                                        {{-- Botón Activar con colores originales --}}
                                        <button
                                            wire:click="toggleEstado({{ $persona->id }})"
                                            wire:confirm="¿Está seguro de que desea activar esta persona?"
                                            class="w-8 h-8 flex items-center justify-center rounded-md bg-green-100 hover:bg-green-200 transition-colors"
                                            title="Activar persona">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Expansión de productos consumibles solicitados (acordeón) --}}
                        @if($personaIdConsumiblesExpandida === $persona->id)
                            <tr>
                                <td colspan="8" class="bg-green-50 p-6">
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

                                            // Obtener TODOS los productos de las salidas
                                            $productosSalidas = collect();
                                            foreach ($salidasPersona as $salida) {
                                                foreach ($salida->detalles as $detalle) {
                                                    if ($detalle->producto) {
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
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full bg-white border border-gray-300">
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
                                            <div class="text-center py-8 text-gray-500">
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
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span class="font-medium">No se encontraron personas.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($personas->hasPages())
            <div class="mt-6 px-6 py-4 border-t border-gray-200">
                {{ $personas->links() }}
            </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 flex items-center justify-center"
             x-data="{ show: @entangle('showModal') }"
             x-show="show"
             @click.self="$wire.closeModal()">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6" @click.stop>
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="text-xl font-semibold text-gray-800">
                        {{ $editMode ? 'Editar Persona' : 'Nueva Persona' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
                </div>

                <form wire:submit.prevent="save" class="mt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombres <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="nombres"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombres') border-red-500 @enderror">
                            @error('nombres')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Apellidos <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="apellidos"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('apellidos') border-red-500 @enderror">
                            @error('apellidos')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                DPI <span class="text-red-500">*</span> (13 dígitos)
                            </label>
                            <input type="text" wire:model="dpi" maxlength="13"
                                   pattern="[0-9]{13}"
                                   inputmode="numeric"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('dpi') border-red-500 @enderror"
                                   placeholder="1234567890123">
                            @error('dpi')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono (8 dígitos)</label>
                            <input type="text" wire:model="telefono"
                                   maxlength="8"
                                   pattern="[0-9]{8}"
                                   inputmode="numeric"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('telefono') border-red-500 @enderror"
                                   placeholder="55555555">
                            @error('telefono')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                            <input type="email" wire:model="correo"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('correo') border-red-500 @enderror">
                            @error('correo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" wire:click="closeModal"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            {{ $editMode ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Reutilizable: Crear Nueva Persona --}}
    @livewire('modal-persona')

    <style>
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
    </style>
</div>

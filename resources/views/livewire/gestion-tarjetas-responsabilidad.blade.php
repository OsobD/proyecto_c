<div>
    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Tarjetas de Responsabilidad</h1>
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            + Nueva Tarjeta
        </button>
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

    {{-- Contenedor principal --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Búsqueda --}}
        <div class="mb-4">
            <input type="text" wire:model.live="search" class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Buscar por nombre de persona...">
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Persona</th>
                        <th class="py-3 px-6 text-left">Fecha Creación</th>
                        <th class="py-3 px-6 text-left">Total Asignado</th>
                        <th class="py-3 px-6 text-left">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($tarjetas as $tarjeta)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $tarjeta->id }}</td>
                            <td class="py-3 px-6 text-left">
                                <strong>{{ $tarjeta->persona->nombres }} {{ $tarjeta->persona->apellidos }}</strong>
                                @if($tarjeta->persona->correo || $tarjeta->persona->telefono)
                                    <div class="text-xs text-gray-500 mt-1">
                                        @if($tarjeta->persona->correo)
                                            <div>{{ $tarjeta->persona->correo }}</div>
                                        @endif
                                        @if($tarjeta->persona->telefono)
                                            <div>{{ $tarjeta->persona->telefono }}</div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($tarjeta->fecha_creacion)->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-6 text-left">Q{{ number_format($tarjeta->total, 2) }}</td>
                            <td class="py-3 px-6 text-left">
                                <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Activa</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    {{-- Botón para ver productos --}}
                                    <button
                                        wire:click="verProductos({{ $tarjeta->id }})"
                                        class="bg-gray-500 hover:bg-gray-700 text-white p-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg"
                                        title="Ver productos asignados">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </button>

                                    <x-action-button
                                        type="edit"
                                        wire:click="edit({{ $tarjeta->id }})"
                                        title="Editar tarjeta" />
                                    <x-action-button
                                        type="delete"
                                        wire:click="confirmDelete({{ $tarjeta->id }})"
                                        title="Desactivar tarjeta" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">No se encontraron tarjetas de responsabilidad.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $tarjetas->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 flex items-center justify-center"
             x-data
             @click.self="$wire.closeModal()">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6" @click.stop>
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="text-xl font-semibold text-gray-800">
                        {{ $editMode ? 'Editar Tarjeta de Responsabilidad' : 'Nueva Tarjeta de Responsabilidad' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
                </div>

                <form wire:submit.prevent="save" class="mt-4">
                    {{-- Selección de Persona --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Persona <span class="text-red-500">*</span>
                        </label>

                        @if($editMode)
                            {{-- En modo edición, solo mostrar la persona (no editable) --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <div>
                                        <strong>{{ $personaSeleccionada->nombres }} {{ $personaSeleccionada->apellidos }}</strong>
                                        @if($personaSeleccionada->correo)
                                            <div class="text-sm text-gray-600">{{ $personaSeleccionada->correo }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- En modo crear, permitir búsqueda --}}
                            <div class="relative">
                                <input type="text"
                                       wire:model.live="searchPersona"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_persona') border-red-500 @enderror {{ $personaSeleccionada ? 'bg-gray-100' : '' }}"
                                       placeholder="Buscar persona por nombre, apellido o correo..."
                                       {{ $personaSeleccionada ? 'disabled' : '' }}>

                                @if($personaSeleccionada)
                                    <button type="button"
                                            wire:click="clearPersona"
                                            class="absolute right-2 top-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @endif

                                @error('id_persona')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror

                                {{-- Lista de personas encontradas --}}
                                @if(count($personasDisponibles) > 0 && !$personaSeleccionada)
                                    <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                        @foreach($personasDisponibles as $persona)
                                            <button type="button"
                                                    wire:click="selectPersona({{ $persona->id }})"
                                                    class="w-full text-left px-4 py-3 hover:bg-gray-100 border-b border-gray-200 last:border-b-0">
                                                <strong>{{ $persona->nombres }} {{ $persona->apellidos }}</strong>
                                                @if($persona->correo)
                                                    <div class="text-sm text-gray-600">{{ $persona->correo }}</div>
                                                @endif
                                                @if($persona->telefono)
                                                    <div class="text-sm text-gray-600">{{ $persona->telefono }}</div>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Persona seleccionada --}}
                                @if($personaSeleccionada && !$editMode)
                                    <div class="bg-green-50 border border-green-200 rounded-md p-3 mt-2">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <strong>{{ $personaSeleccionada->nombres }} {{ $personaSeleccionada->apellidos }}</strong>
                                                @if($personaSeleccionada->correo)
                                                    <div class="text-sm text-gray-600">{{ $personaSeleccionada->correo }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <p class="text-xs text-gray-500 mt-2">
                                ℹ Solo se muestran personas sin tarjeta de responsabilidad activa.
                            </p>
                        @endif
                    </div>

                    {{-- Fecha de Creación y Total --}}
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Creación <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   wire:model="fecha_creacion"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fecha_creacion') border-red-500 @enderror">
                            @error('fecha_creacion')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Asignado</label>
                            <input type="number"
                                   wire:model="total"
                                   step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('total') border-red-500 @enderror"
                                   placeholder="0.00">
                            @error('total')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Este valor se actualizará automáticamente con las asignaciones de productos.</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" wire:click="closeModal"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            {{ $editMode ? 'Actualizar' : 'Crear Tarjeta' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal: Ver Productos Asignados --}}
    @if($showProductosModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[70] flex items-center justify-center"
             x-data
             @click.self="$wire.cerrarProductosModal()">
            <div class="bg-white rounded-lg shadow-2xl max-w-7xl w-full max-h-[90vh] overflow-hidden" @click.stop>
                {{-- Header del Modal --}}
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Productos Asignados</h3>
                            <p class="text-gray-600 text-sm mt-1">{{ $tarjetaNombre }}</p>
                        </div>
                        <button wire:click="cerrarProductosModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Contenido del Modal --}}
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-150px)]">
                    @if(count($tarjetaProductos) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <tr>
                                        <th class="py-3 px-4 text-left">Código</th>
                                        <th class="py-3 px-4 text-left">Producto</th>
                                        <th class="py-3 px-4 text-center">Lote ID</th>
                                        <th class="py-3 px-4 text-center">Cant. Asignada</th>
                                        <th class="py-3 px-4 text-right">Precio Unitario</th>
                                        <th class="py-3 px-4 text-right">Total Asignado</th>
                                        <th class="py-3 px-4 text-center">Fecha Ingreso</th>
                                        <th class="py-3 px-4 text-left">Bodega</th>
                                        <th class="py-3 px-4 text-center">Estado</th>
                                        <th class="py-3 px-4 text-left">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm">
                                    @foreach($tarjetaProductos as $tp)
                                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-4 text-left">
                                                <span class="font-mono text-gray-700 font-semibold">{{ $tp['producto_codigo'] }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-left">
                                                <span class="font-medium text-gray-800">{{ $tp['producto_nombre'] }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="bg-gray-100 text-gray-700 py-1 px-3 rounded-full text-xs font-semibold">
                                                    #{{ $tp['lote_id'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="bg-indigo-100 text-indigo-800 py-1 px-3 rounded-full text-xs font-bold">
                                                    {{ $tp['cantidad_asignada'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="font-medium text-gray-700">Q{{ number_format($tp['precio_ingreso'], 2) }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="font-semibold text-indigo-600">Q{{ number_format($tp['precio_asignacion'], 2) }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="text-gray-600 text-xs">{{ $tp['fecha_ingreso'] }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-left">
                                                <span class="text-gray-700">{{ $tp['bodega'] }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                @if($tp['estado'] === 'Activo')
                                                    <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                                        Activo
                                                    </span>
                                                @else
                                                    <span class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs font-semibold">
                                                        Inactivo
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-left max-w-xs">
                                                <span class="text-gray-600 text-xs line-clamp-2">{{ $tp['observaciones'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Resumen --}}
                        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700 font-medium">Total de productos asignados:</span>
                                <span class="text-2xl font-bold text-indigo-600">{{ count($tarjetaProductos) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="flex flex-col items-center gap-4">
                                <div class="bg-gray-100 p-4 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-600 font-medium text-lg">No hay productos asignados</p>
                                    <p class="text-gray-500 text-sm mt-1">Esta tarjeta aún no tiene productos registrados</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-200">
                    <button
                        wire:click="cerrarProductosModal"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition-all duration-200">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('confirm-delete', () => {
            if (confirm('¿Está seguro de que desea desactivar esta tarjeta de responsabilidad?')) {
                @this.call('delete');
            }
        });
    });
</script>
@endpush

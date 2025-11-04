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
                                            <div>✉ {{ $tarjeta->persona->correo }}</div>
                                        @endif
                                        @if($tarjeta->persona->telefono)
                                            <div>☎ {{ $tarjeta->persona->telefono }}</div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($tarjeta->fecha_creacion)->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-6 text-left">${{ number_format($tarjeta->total, 2) }}</td>
                            <td class="py-3 px-6 text-left">
                                <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Activa</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <button wire:click="edit({{ $tarjeta->id }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-100 hover:bg-yellow-200 mr-2"
                                            title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $tarjeta->id }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 hover:bg-red-200"
                                            title="Desactivar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
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

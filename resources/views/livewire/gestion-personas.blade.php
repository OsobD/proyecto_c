<div>
    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Personas</h1>
        <div class="flex items-center space-x-3">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="showAllPersonas" class="mr-2">
                <span class="text-sm text-gray-700">Mostrar inactivos</span>
            </label>
            <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                + Nueva Persona
            </button>
        </div>
    </div>

    {{-- Mensajes --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
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
            <input type="text" wire:model.live="search" class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Buscar por nombre, apellido, correo o teléfono...">
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Nombres</th>
                        <th class="py-3 px-6 text-left">Apellidos</th>
                        <th class="py-3 px-6 text-left">Teléfono</th>
                        <th class="py-3 px-6 text-left">Correo</th>
                        <th class="py-3 px-6 text-left">Género</th>
                        <th class="py-3 px-6 text-left">Fecha Nac.</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($personas as $persona)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $persona->id }}</td>
                            <td class="py-3 px-6 text-left">{{ $persona->nombres }}</td>
                            <td class="py-3 px-6 text-left">{{ $persona->apellidos }}</td>
                            <td class="py-3 px-6 text-left">{{ $persona->telefono ?? 'N/A' }}</td>
                            <td class="py-3 px-6 text-left">{{ $persona->correo ?? 'N/A' }}</td>
                            <td class="py-3 px-6 text-left">
                                @if($persona->genero === 'M')
                                    <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs">Masculino</span>
                                @elseif($persona->genero === 'F')
                                    <span class="bg-pink-200 text-pink-800 py-1 px-3 rounded-full text-xs">Femenino</span>
                                @else
                                    <span class="bg-gray-200 text-gray-800 py-1 px-3 rounded-full text-xs">N/A</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $persona->fecha_nacimiento ? \Carbon\Carbon::parse($persona->fecha_nacimiento)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($persona->estado)
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Activo</span>
                                @else
                                    <span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <button wire:click="edit({{ $persona->id }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-100 hover:bg-yellow-200 mr-2"
                                            title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    @if($persona->estado)
                                        <button wire:click="toggleEstado({{ $persona->id }})"
                                                wire:confirm="¿Está seguro de que desea desactivar esta persona?"
                                                class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 hover:bg-red-200"
                                                title="Desactivar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="toggleEstado({{ $persona->id }})"
                                                wire:confirm="¿Está seguro de que desea activar esta persona?"
                                                class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 hover:bg-green-200"
                                                title="Activar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-gray-500">No se encontraron personas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $personas->links() }}
        </div>
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                            <input type="text" wire:model="telefono"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('telefono') border-red-500 @enderror">
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento</label>
                            <input type="date" wire:model="fecha_nacimiento"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fecha_nacimiento') border-red-500 @enderror">
                            @error('fecha_nacimiento')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Género</label>
                            <select wire:model="genero"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('genero') border-red-500 @enderror">
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            @error('genero')
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
</div>

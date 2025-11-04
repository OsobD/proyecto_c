<div>
    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Bodegas Físicas</h1>
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            + Nueva Bodega
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
                   placeholder="Buscar bodega por nombre...">
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Nombre</th>
                        <th class="py-3 px-6 text-left">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($bodegas as $bodega)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $bodega->id }}</td>
                            <td class="py-3 px-6 text-left">
                                <span class="font-medium">{{ $bodega->nombre }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Activa</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <button wire:click="edit({{ $bodega->id }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-100 hover:bg-yellow-200 mr-2"
                                            title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $bodega->id }})"
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
                            <td colspan="4" class="text-center py-4 text-gray-500">No se encontraron bodegas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $bodegas->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 flex items-center justify-center"
             x-data
             @click.self="$wire.closeModal()">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="text-xl font-semibold text-gray-800">
                        {{ $editMode ? 'Editar Bodega' : 'Nueva Bodega' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
                </div>

                <form wire:submit.prevent="save" class="mt-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la Bodega <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nombre"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-500 @enderror">
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
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

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('confirm-delete', () => {
            if (confirm('¿Está seguro de que desea desactivar esta bodega?')) {
                @this.call('delete');
            }
        });
    });
</script>
@endpush

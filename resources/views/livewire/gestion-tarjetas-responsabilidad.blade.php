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
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Búsqueda --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar tarjeta</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live="search"
                       class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                       placeholder="Buscar por nombre de persona...">
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Persona</th>
                        <th class="py-3 px-6 text-left">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($tarjetas as $tarjeta)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $tarjeta->id }}</td>
                            <td class="py-3 px-6 text-left">
                                @if($tarjeta->persona)
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
                                @else
                                    <span class="text-red-500 text-sm">Sin persona asignada</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Activa</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    {{-- Botón para ver productos asignados --}}
                                    <button
                                        wire:click="toggleProductos({{ $tarjeta->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 {{ $tarjetaIdExpandida === $tarjeta->id ? 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        title="Ver productos asignados">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </button>

                                    <x-action-button
                                        type="delete"
                                        wire:click="confirmDelete({{ $tarjeta->id }})"
                                        title="Desactivar tarjeta" />
                                </div>
                            </td>
                        </tr>

                        {{-- Expansión de productos de la tarjeta (acordeón) --}}
                        @if($tarjetaIdExpandida === $tarjeta->id)
                            <tr>
                                <td colspan="4" class="bg-gray-50 p-6">
                                    <div class="mb-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                Productos asignados a {{ $tarjeta->persona ? $tarjeta->persona->nombres . ' ' . $tarjeta->persona->apellidos : 'Tarjeta #' . $tarjeta->id }}
                                            </h3>
                                        </div>

                                        @php
                                            // Obtener los productos asignados a esta tarjeta
                                            $tarjetaProductos = $tarjeta->tarjetasProducto()
                                                ->with(['producto', 'lote.bodega'])
                                                ->get();
                                        @endphp

                                        @if($tarjetaProductos->count() > 0)
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full bg-white border border-gray-300">
                                                    <thead class="bg-indigo-100 text-gray-700 text-sm">
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
                                                            @php
                                                                $lote = $tp->lote;
                                                                $producto = $tp->producto;

                                                                // Calcular la cantidad asignada
                                                                $cantidadAsignada = 0;
                                                                if ($lote && $lote->precio_ingreso > 0) {
                                                                    $cantidadAsignada = round($tp->precio_asignacion / $lote->precio_ingreso);
                                                                }
                                                            @endphp
                                                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors" wire:key="tp-{{ $tp->id }}">
                                                                <td class="py-3 px-4 text-left">
                                                                    <span class="font-mono text-gray-700 font-semibold">
                                                                        {{ $producto ? $producto->id : 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-left">
                                                                    <span class="font-medium text-gray-800">
                                                                        {{ $producto ? $producto->descripcion : 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-center">
                                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">
                                                                        #{{ $lote ? $lote->id : 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-center">
                                                                    <span class="bg-indigo-100 text-indigo-800 py-1 px-3 rounded-full text-xs font-bold">
                                                                        {{ $cantidadAsignada }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-right">
                                                                    <span class="font-medium text-gray-700">
                                                                        Q{{ $lote ? number_format($lote->precio_ingreso, 2) : '0.00' }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-right">
                                                                    <span class="font-semibold text-indigo-600">
                                                                        Q{{ number_format($tp->precio_asignacion, 2) }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-center text-xs">
                                                                    {{ $lote && $lote->fecha_ingreso ? \Carbon\Carbon::parse($lote->fecha_ingreso)->format('d/m/Y') : 'N/A' }}
                                                                </td>
                                                                <td class="py-3 px-4 text-left">
                                                                    <span class="text-gray-700">
                                                                        {{ $lote && $lote->bodega ? $lote->bodega->nombre : 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4 text-center">
                                                                    @if($lote && $lote->estado)
                                                                        <span class="bg-green-200 text-green-800 py-1 px-2 rounded-full text-xs">Activo</span>
                                                                    @else
                                                                        <span class="bg-red-200 text-red-800 py-1 px-2 rounded-full text-xs">Inactivo</span>
                                                                    @endif
                                                                </td>
                                                                <td class="py-3 px-4 text-left text-xs text-gray-500 max-w-xs truncate">
                                                                    {{ $lote ? ($lote->observaciones ?? '-') : '-' }}
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
                                                    <span class="text-2xl font-bold text-indigo-600">{{ $tarjetaProductos->count() }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-8 text-gray-500">
                                                <p>No hay productos asignados a esta tarjeta.</p>
                                                <p class="text-sm mt-2">Los productos aparecerán aquí cuando se realicen asignaciones.</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">No se encontraron tarjetas de responsabilidad.</td>
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
    <div x-data="{
            show: @entangle('showModal').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-[100] flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModal"
         wire:ignore.self>
        <div class="relative p-6 border w-full max-w-2xl shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-hidden"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        Nueva Tarjeta de Responsabilidad
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="mt-4">
                    {{-- Selección de Persona --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Persona <span class="text-red-500">*</span>
                        </label>

                        {{-- Campo de búsqueda --}}
                        <div class="relative">
                            <input type="text"
                                   wire:model.live="searchPersona"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('id_persona') border-red-500 ring-2 ring-red-200 @enderror {{ $personaSeleccionada ? 'bg-gray-100' : '' }}"
                                   placeholder="Buscar persona por nombre, apellido o correo..."
                                   {{ $personaSeleccionada ? 'disabled' : '' }}
                                   autocomplete="off">

                            @if($personaSeleccionada)
                                <button type="button"
                                        wire:click="clearPersona"
                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif

                            @error('id_persona')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror

                            {{-- Lista de personas encontradas con posición mejorada --}}
                            @if(!empty($personasDisponibles) && !$personaSeleccionada)
                                <div class="absolute z-[200] w-full mt-1 bg-white border-2 border-gray-300 rounded-lg shadow-xl max-h-64 overflow-y-auto">
                                    @foreach($personasDisponibles as $persona)
                                        <button type="button"
                                                wire:click="selectPersona({{ $persona->id }})"
                                                class="w-full text-left px-4 py-3 hover:bg-blue-50 border-b border-gray-200 last:border-b-0 transition-colors">
                                            <strong class="text-gray-900">{{ $persona->nombres }} {{ $persona->apellidos }}</strong>
                                            @if($persona->correo)
                                                <div class="text-sm text-gray-600">📧 {{ $persona->correo }}</div>
                                            @endif
                                            @if($persona->telefono)
                                                <div class="text-sm text-gray-600">📱 {{ $persona->telefono }}</div>
                                            @endif
                                        </button>
                                    @endforeach

                                    {{-- Botón para crear nueva persona --}}
                                    <button type="button"
                                            wire:click="$dispatch('abrirModalPersona')"
                                            class="w-full px-4 py-3 text-left text-blue-600 hover:bg-blue-50 font-semibold flex items-center gap-2 border-t-2 border-blue-200 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>Crear nueva persona</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Persona seleccionada --}}
                            @if($personaSeleccionada)
                                <div class="bg-green-50 border-2 border-green-300 rounded-lg p-4 mt-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <strong class="text-gray-900">{{ $personaSeleccionada->nombres }} {{ $personaSeleccionada->apellidos }}</strong>
                                                @if($personaSeleccionada->correo)
                                                    <div class="text-sm text-gray-600">{{ $personaSeleccionada->correo }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(!$personaSeleccionada)
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-xs text-blue-700 flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>
                                        Escribe al menos 2 caracteres para buscar. Solo se muestran personas sin tarjeta activa.
                                        <br>
                                        <button type="button"
                                                wire:click="$dispatch('abrirModalPersona')"
                                                class="text-blue-600 hover:text-blue-800 font-semibold underline mt-1 inline-block">
                                            Haz clic aquí para crear una nueva persona
                                        </button>
                                    </span>
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button"
                                wire:click="closeModal"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-all duration-200">
                            Cancelar
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                :disabled="!$personaSeleccionada"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="save">✓ Crear Tarjeta</span>
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

    {{-- Incluir el modal de creación de persona --}}
    @livewire('modal-persona')

    <style>
        /* Ocultar elementos hasta que Alpine.js esté listo */
        [x-cloak] {
            display: none !important;
        }

        /* Animaciones de entrada */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
            from { opacity: 1; }
            to { opacity: 0; }
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

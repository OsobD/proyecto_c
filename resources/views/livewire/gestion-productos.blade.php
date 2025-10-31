{{--
    Vista: Gestión de Productos
    Descripción: Interfaz CRUD para productos del inventario con búsqueda en tiempo real,
                 modal de edición y visualización de historial de compras
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Productos'],
    ]" />

    {{-- Encabezado con título e información sobre creación de productos --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
            <p class="text-sm text-gray-600 mt-1">
                Los productos se crean automáticamente al registrar compras o ingresos al inventario
            </p>
        </div>
    </div>

    {{-- Alerta de éxito para operaciones CRUD --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Contenedor principal --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Campo de búsqueda con filtrado reactivo --}}
        <div class="mb-6">
            <input
                type="text"
                wire:model.live.debounce.300ms="searchProducto"
                class="w-full md:w-1/2 px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent"
                placeholder="Buscar por código, descripción o categoría...">
        </div>

        {{-- Tabla de listado de productos --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-eemq-heather-100 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Código</th>
                        <th class="py-3 px-6 text-left">Descripción</th>
                        <th class="py-3 px-6 text-left">Categoría</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($this->productosFiltrados as $producto)
                        <tr class="border-b border-eemq-heather-100 hover:bg-eemq-athens-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <span class="font-medium font-mono">{{ $producto['codigo'] }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $producto['descripcion'] }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="bg-eemq-horizon-100 text-eemq-chambray text-xs font-semibold px-2 py-1 rounded">
                                    {{ $this->getNombreCategoria($producto['categoria_id']) }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($producto['activo'])
                                    <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">Activo</span>
                                @else
                                    <span class="bg-eemq-crimson-200 text-eemq-crimson-700 py-1 px-3 rounded-full text-xs font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    {{-- Ver Historial --}}
                                    @if(count($producto['historial']) > 0)
                                        <button
                                            wire:click="toggleHistorial({{ $producto['id'] }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-eemq-heather-100 hover:bg-eemq-heather-200"
                                            title="Ver historial de precios">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-eemq-chambray" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    @endif
                                    {{-- Editar --}}
                                    <button
                                        wire:click="editarProducto({{ $producto['id'] }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-eemq-horizon-100 hover:bg-eemq-horizon-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-eemq-horizon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    {{-- Toggle Estado --}}
                                    <button
                                        wire:click="toggleEstado({{ $producto['id'] }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-full {{ $producto['activo'] ? 'bg-eemq-crimson-100 hover:bg-eemq-crimson-200' : 'bg-green-100 hover:bg-green-200' }}">
                                        @if($producto['activo'])
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-eemq-crimson" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </button>
                                </div>
                            </td>
                        </tr>
                        {{-- Historial expandible --}}
                        @if($showHistorial === $producto['id'] && count($producto['historial']) > 0)
                            <tr class="bg-eemq-athens">
                                <td colspan="5" class="py-4 px-6">
                                    <div class="ml-8">
                                        <h4 class="font-semibold text-gray-700 mb-2">Historial de Precios</h4>
                                        <table class="min-w-full bg-white border border-eemq-heather-100 rounded">
                                            <thead class="bg-eemq-athens-100">
                                                <tr>
                                                    <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600">Fecha</th>
                                                    <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600">Proveedor</th>
                                                    <th class="py-2 px-4 text-right text-xs font-semibold text-gray-600">Costo</th>
                                                    <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600">Factura</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($producto['historial'] as $registro)
                                                    <tr class="border-t border-eemq-heather-100">
                                                        <td class="py-2 px-4 text-sm">{{ $registro['fecha'] }}</td>
                                                        <td class="py-2 px-4 text-sm">{{ $registro['proveedor'] }}</td>
                                                        <td class="py-2 px-4 text-sm text-right font-semibold">Q{{ number_format($registro['costo'], 2) }}</td>
                                                        <td class="py-2 px-4 text-sm font-mono text-gray-600">{{ $registro['factura'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">
                                No se encontraron productos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Crear/Editar Producto --}}
    <div x-data="{
            show: @entangle('showModal'),
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModal">
        <div class="relative p-6 border w-full max-w-lg shadow-lg rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    Editar Producto
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="guardarProducto">
                {{-- Código --}}
                <div class="mb-4">
                    <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">
                        Código del Producto
                    </label>
                    <input
                        type="text"
                        id="codigo"
                        wire:model="codigo"
                        class="w-full px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('codigo') border-eemq-crimson @enderror"
                        placeholder="Ej: PROD-001">
                    @error('codigo')
                        <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="mb-4">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <input
                        type="text"
                        id="descripcion"
                        wire:model="descripcion"
                        class="w-full px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('descripcion') border-eemq-crimson @enderror"
                        placeholder="Ej: Tornillos de acero inoxidable">
                    @error('descripcion')
                        <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Categoría --}}
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label for="categoriaId" class="block text-sm font-medium text-gray-700">
                            Categoría
                        </label>
                        <button
                            type="button"
                            wire:click="abrirSubModalCategoria"
                            class="text-eemq-horizon hover:text-eemq-horizon-700 text-sm font-semibold">
                            + Crear Categoría
                        </button>
                    </div>
                    <select
                        id="categoriaId"
                        wire:model="categoriaId"
                        class="w-full px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('categoriaId') border-eemq-crimson @enderror">
                        <option value="">Seleccione una categoría</option>
                        @foreach($this->categoriasActivas as $categoria)
                            <option value="{{ $categoria['id'] }}">{{ $categoria['nombre'] }}</option>
                        @endforeach
                    </select>
                    @error('categoriaId')
                        <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="bg-eemq-heather-200 hover:bg-eemq-heather-300 text-gray-800 font-semibold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-eemq-horizon hover:bg-eemq-horizon-600 text-white font-semibold py-2 px-4 rounded">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sub-modal Crear Categoría --}}
    <div x-data="{
            show: @entangle('showSubModalCategoria'),
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center"
         style="z-index: 9999 !important;"
         :style="(!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')) + ' z-index: 9999 !important;'"
         wire:click.self="closeSubModalCategoria">
        <div class="relative p-6 border w-full max-w-sm shadow-xl rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Nueva Categoría</h3>
                <button wire:click="closeSubModalCategoria" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="guardarNuevaCategoria">
                <div class="mb-6">
                    <label for="nuevaCategoriaNombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de la Categoría
                    </label>
                    <input
                        type="text"
                        id="nuevaCategoriaNombre"
                        wire:model="nuevaCategoriaNombre"
                        class="w-full px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('nuevaCategoriaNombre') border-eemq-crimson @enderror"
                        placeholder="Ej: Equipos de Protección">
                    @error('nuevaCategoriaNombre')
                        <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        wire:click="closeSubModalCategoria"
                        class="bg-eemq-heather-200 hover:bg-eemq-heather-300 text-gray-800 font-semibold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-eemq-horizon hover:bg-eemq-horizon-600 text-white font-semibold py-2 px-4 rounded">
                        Crear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
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
    </style>
</div>

{{--
    Vista: Gestión de Bodegas
    Descripción: CRUD de bodegas físicas con visualización de productos (lotes)
                 por bodega. Permite crear, editar y desactivar bodegas, así como
                 crear productos directamente desde aquí.
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Bodegas'],
    ]" />

    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Bodegas</h1>
        @if(auth()->user()->puedeCrear('bodegas'))
            <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                + Nueva Bodega
            </button>
        @endif
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
        {{-- Búsqueda y filtros --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar bodega</label>
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           wire:model.live="search"
                           class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="Buscar bodega por nombre...">
                </div>
                <button wire:click="openFilterModal"
                        class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg border-2 border-gray-300 transition-all duration-200 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span class="font-medium">Filtros / Ajustes</span>
                </button>
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
                                wire:click="sortBy('nombre')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold transition-colors">
                                Nombre
                                @if($sortField === 'nombre')
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
                        <th class="py-3 px-6 text-left">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($bodegas as $bodega)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $bodega->id }}</td>
                            <td class="py-3 px-6 text-left">
                                <a href="{{ route('bodegas.detalle', $bodega->id) }}" class="font-medium hover:text-blue-600 hover:underline transition-colors">
                                    {{ $bodega->nombre }}
                                </a>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Activa</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    <button
                                        wire:click="toggleProductos({{ $bodega->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-md transition-all duration-200 {{ $bodegaIdProductosExpandido === $bodega->id ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        title="Ver productos de la bodega">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </button>
                                    @if(auth()->user()->puedeEditar('bodegas'))
                                        <x-action-button
                                            type="edit"
                                            wire:click="edit({{ $bodega->id }})"
                                            title="Editar bodega" />
                                    @endif
                                    @if(auth()->user()->puedeEliminar('bodegas'))
                                        <x-action-button
                                            type="delete"
                                            wire:click="confirmDelete({{ $bodega->id }})"
                                            title="Desactivar bodega" />
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Expansión de productos de la bodega --}}
                        @if($bodegaIdProductosExpandido === $bodega->id)
                            <tr>
                                <td colspan="4" class="bg-gray-50 p-6">
                                    <div class="mb-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-semibold text-gray-800">Productos en {{ $bodega->nombre }}</h3>
                                            @if(auth()->user()->puedeCrear('productos'))
                                                <button
                                                    wire:click="abrirModalProducto"
                                                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg">
                                                    + Nuevo Producto
                                                </button>
                                            @endif
                                        </div>

                                        @php
                                            // Obtener los lotes de esta bodega agrupados por producto (LIMITADO A 5 RECIENTES)
                                            // Nota: La limitación real idealmente debería ser en la consulta, pero como agrupamos por producto,
                                            // tomaremos los 5 lotes más recientes para la vista previa.
                                            $lotes = $bodega->lotes()
                                                ->with(['producto.categoria'])
                                                ->orderBy('fecha_ingreso', 'desc')
                                                ->take(5) // LIMITAR A 5
                                                ->get()
                                                ->filter(function($lote) {
                                                    return $lote->producto !== null;
                                                });

                                            $productosAgrupados = $lotes->groupBy('id_producto');
                                            $totalLotes = $bodega->lotes()->count();
                                        @endphp

                                        @if($productosAgrupados->count() > 0)
                                            <div class="overflow-x-auto mb-4">
                                                <table class="min-w-full bg-white border border-gray-300">
                                                    <thead class="bg-indigo-100 text-gray-700 text-sm">
                                                        <tr>
                                                            <th class="py-3 px-4 text-left">Código</th>
                                                            <th class="py-3 px-4 text-left">Descripción</th>
                                                            <th class="py-3 px-4 text-left">Categoría</th>
                                                            <th class="py-3 px-4 text-center">Lote ID</th>
                                                            <th class="py-3 px-4 text-center">Cantidad</th>
                                                            <th class="py-3 px-4 text-center">Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 text-sm">
                                                        @foreach($productosAgrupados as $productoId => $lotesProducto)
                                                            @foreach($lotesProducto as $index => $lote)
                                                                <tr class="border-b border-gray-200 hover:bg-gray-50" wire:key="lote-{{ $lote->id }}">
                                                                    @if($index === 0)
                                                                        <td class="py-3 px-4 font-mono font-semibold" rowspan="{{ $lotesProducto->count() }}">
                                                                            {{ $lote->producto?->id ?? 'N/A' }}
                                                                        </td>
                                                                        <td class="py-3 px-4" rowspan="{{ $lotesProducto->count() }}">
                                                                            {{ $lote->producto?->descripcion ?? 'Producto no disponible' }}
                                                                        </td>
                                                                        <td class="py-3 px-4 text-sm text-gray-500" rowspan="{{ $lotesProducto->count() }}">
                                                                            {{ $lote->producto?->categoria?->nombre ?? 'Sin categoría' }}
                                                                        </td>
                                                                    @endif

                                                                    <td class="py-3 px-4 text-center">
                                                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">
                                                                            #{{ $lote->id }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="py-3 px-4 text-center">
                                                                        <span class="font-semibold {{ $lote->cantidad > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                            {{ $lote->cantidad }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="py-3 px-4 text-center">
                                                                        @if($lote->estado)
                                                                            <span class="bg-green-200 text-green-800 py-1 px-2 rounded-full text-xs">Activo</span>
                                                                        @else
                                                                            <span class="bg-red-200 text-red-800 py-1 px-2 rounded-full text-xs">Inactivo</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="flex justify-center mt-4">
                                                <a href="{{ route('bodegas.detalle', $bodega->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                    Ver inventario completo ({{ $totalLotes }} lotes)
                                                    <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-center py-8 text-gray-500">
                                                <p>No hay productos en esta bodega.</p>
                                                <p class="text-sm mt-2">Los productos aparecerán aquí cuando se registren compras.</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
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

    {{-- Modal de Bodega --}}
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
        <div class="relative border w-full max-w-md shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-hidden"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="p-8 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        {{ $editMode ? 'Editar Bodega' : 'Nueva Bodega' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la Bodega <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nombre"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('nombre') border-red-500 ring-2 ring-red-200 @enderror"
                               placeholder="Ej: Bodega Principal">
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" wire:click="closeModal"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-all duration-200">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                            {{ $editMode ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para crear nuevo producto --}}
    <div x-data="{
            show: @entangle('showModalProducto').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalProducto"
         wire:ignore.self>
        <div class="relative p-6 border w-full max-w-lg shadow-lg rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Crear Nuevo Producto</h3>
                <button wire:click="closeModalProducto" class="text-gray-400 hover:text-gray-600">
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
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('codigo') border-red-500 @enderror"
                        placeholder="Ej: PROD-001">
                    @error('codigo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="mb-4">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea
                        id="descripcion"
                        wire:model="descripcion"
                        rows="4"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('descripcion') border-red-500 @enderror"
                        placeholder="Ej: Basureros de plástico 50L"></textarea>
                    @error('descripcion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Categoría --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Categoría
                    </label>
                    <div class="relative">
                        @if($selectedCategoria)
                            <div class="flex items-center justify-between w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm @error('categoriaId') border-red-500 @enderror">
                                <span>{{ $selectedCategoria['nombre'] }}</span>
                                <button type="button" wire:click.prevent="clearCategoria" class="text-gray-400 hover:text-gray-600">
                                    ×
                                </button>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: @entangle('showCategoriaDropdown').live }" @click.outside="open = false">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchCategoria"
                                    @click="open = true"
                                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('categoriaId') border-red-500 @enderror"
                                    placeholder="Buscar categoría...">
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                    <ul>
                                        @foreach (array_slice($this->categoriaResults, 0, 6) as $categoria)
                                            <li wire:click.prevent="selectCategoria({{ $categoria['id'] }})"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $categoria['nombre'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="border-t border-gray-200">
                                        <button
                                            type="button"
                                            wire:click="abrirSubModalCategoria"
                                            class="w-full px-3 py-2 text-left text-blue-600 hover:bg-blue-50 font-semibold flex items-center gap-2">
                                            <span>+</span>
                                            <span>Crear nueva categoría</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('categoriaId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Es Consumible --}}
                <div class="mb-4">
                    <label class="flex items-center space-x-3">
                        <input
                            type="checkbox"
                            wire:model="esConsumible"
                            class="w-5 h-5 text-blue-600 border-2 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">
                            Este producto es consumible
                        </span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-8">
                        Marca esta opción si el producto se agota con el uso (ej: materiales, insumos)
                    </p>
                </div>

                <div class="flex justify-between mt-6">
                    <button
                        type="button"
                        wire:click="closeModalProducto"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                        Crear Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sub-modal para crear categoría al crear producto (modal anidado con z-index superior) --}}
    <div x-data="{
            show: @entangle('showSubModalCategoria').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center"
         style="z-index: 9999 !important;"
         :style="(!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')) + ' z-index: 9999 !important;'"
         wire:click.self="closeSubModalCategoria"
         wire:ignore.self>
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
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nuevaCategoriaNombre') border-red-500 @enderror"
                        placeholder="Ej: Artículos de Limpieza">
                    @error('nuevaCategoriaNombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between mt-6">
                    <button
                        type="button"
                        wire:click="closeSubModalCategoria"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                        Crear
                    </button>
                </div>
            </form>
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

    {{-- Modal de Filtros / Ajustes --}}
    @if($showFilterModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                {{-- Header --}}
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Filtros / Ajustes</h3>
                    <button wire:click="closeFilterModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-4 space-y-4">
                    {{-- Ordenamiento --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar por:</label>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="radio" wire:model.live="sortField" value="id" class="mr-2">
                                <span>ID</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="radio" wire:model.live="sortField" value="nombre" class="mr-2">
                                <span>Nombre</span>
                            </label>
                        </div>
                    </div>

                    {{-- Dirección --}}
                    @if($sortField)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección:</label>
                            <div class="flex gap-2">
                                <label class="flex-1 flex items-center justify-center cursor-pointer hover:bg-gray-50 p-2 rounded border {{ $sortDirection === 'asc' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input type="radio" wire:model.live="sortDirection" value="asc" class="mr-2">
                                    <span>Ascendente ↑</span>
                                </label>
                                <label class="flex-1 flex items-center justify-center cursor-pointer hover:bg-gray-50 p-2 rounded border {{ $sortDirection === 'desc' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input type="radio" wire:model.live="sortDirection" value="desc" class="mr-2">
                                    <span>Descendente ↓</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    {{-- Mostrar inactivas --}}
                    <div class="border-t pt-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="showInactive" class="w-4 h-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm font-medium text-gray-700">Mostrar bodegas desactivadas</span>
                        </label>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between p-4 border-t gap-2">
                    <button wire:click="clearFilters" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        Limpiar filtros
                    </button>
                    <button wire:click="closeFilterModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
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
            if (confirm('¿Está seguro de que desea desactivar esta bodega?')) {
                @this.call('delete');
            }
        });
    });
</script>
@endpush

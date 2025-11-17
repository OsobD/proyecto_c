{{--
    Vista: Gestión de Productos
    Descripción: Interfaz CRUD para productos del inventario con búsqueda en tiempo real,
                 modal de edición, visualización de lotes y CRUD completo de lotes
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Catálogo', 'url' => '#'],
        ['label' => 'Productos'],
    ]" />

    {{-- Encabezado con título e información sobre creación de productos --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
            <p class="text-sm text-gray-600 mt-1">
                Los productos se crean automáticamente al registrar compras o pueden crearse manualmente
            </p>
        </div>
        <button
            wire:click="abrirModal"
            class="bg-eemq-horizon hover:bg-eemq-horizon-600 text-white font-semibold py-2 px-4 rounded shadow-md transition-colors duration-150">
            + Crear Producto
        </button>
    </div>

    {{-- Alertas de éxito y error para operaciones CRUD --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
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
                        <th class="py-3 px-6 text-center">Tipo</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($productos as $producto)
                        <tr class="border-b border-eemq-heather-100 hover:bg-eemq-athens-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <span class="font-medium font-mono">{{ $producto->id }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <button
                                    wire:click="toggleLotes('{{ $producto->id }}')"
                                    class="text-left text-eemq-horizon hover:text-eemq-horizon-700 font-medium hover:underline">
                                    {{ $producto->descripcion }}
                                </button>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="bg-eemq-horizon-100 text-eemq-chambray text-xs font-semibold px-2 py-1 rounded">
                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($producto->es_consumible)
                                    <span class="bg-orange-200 text-orange-700 py-1 px-3 rounded-full text-xs font-semibold">Consumible</span>
                                @else
                                    <span class="bg-blue-200 text-blue-700 py-1 px-3 rounded-full text-xs font-semibold">No Consumible</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($producto->activo)
                                    <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">Activo</span>
                                @else
                                    <span class="bg-eemq-crimson-200 text-eemq-crimson-700 py-1 px-3 rounded-full text-xs font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    {{-- Editar --}}
                                    <x-action-button
                                        type="edit"
                                        wire:click="editarProducto('{{ $producto->id }}')"
                                        title="Editar producto" />
                                    {{-- Toggle Estado --}}
                                    @if($producto->activo)
                                        <x-action-button
                                            type="delete"
                                            wire:click="toggleEstado('{{ $producto->id }}')"
                                            title="Desactivar producto" />
                                    @else
                                        <x-action-button
                                            type="activate"
                                            wire:click="toggleEstado('{{ $producto->id }}')"
                                            title="Activar producto" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                        {{-- Lotes expandibles --}}
                        @if($productoIdLotesExpandido === $producto->id)
                            <tr class="bg-eemq-athens">
                                <td colspan="6" class="py-4 px-6">
                                    <div class="ml-8">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-semibold text-gray-700">Lotes de Inventario</h4>
                                            <button
                                                wire:click="abrirModalCrearLote('{{ $producto->id }}')"
                                                class="bg-eemq-horizon hover:bg-eemq-horizon-600 text-white font-semibold py-1 px-3 rounded text-sm">
                                                + Crear Lote
                                            </button>
                                        </div>
                                        @if($producto->lotes->count() > 0)
                                            <table class="min-w-full bg-white border border-eemq-heather-100 rounded">
                                                <thead class="bg-eemq-athens-100">
                                                    <tr>
                                                        <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600">Bodega</th>
                                                        <th class="py-2 px-4 text-center text-xs font-semibold text-gray-600">Cantidad</th>
                                                        <th class="py-2 px-4 text-center text-xs font-semibold text-gray-600">Cant. Inicial</th>
                                                        <th class="py-2 px-4 text-right text-xs font-semibold text-gray-600">Precio Ingreso</th>
                                                        <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600">Fecha Ingreso</th>
                                                        <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600">Observaciones</th>
                                                        <th class="py-2 px-4 text-center text-xs font-semibold text-gray-600">Estado</th>
                                                        <th class="py-2 px-4 text-center text-xs font-semibold text-gray-600">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($producto->lotes as $lote)
                                                        <tr class="border-t border-eemq-heather-100 hover:bg-gray-50">
                                                            <td class="py-2 px-4 text-sm">{{ $lote->bodega->nombre ?? 'Sin bodega' }}</td>
                                                            <td class="py-2 px-4 text-sm text-center font-semibold">{{ $lote->cantidad }}</td>
                                                            <td class="py-2 px-4 text-sm text-center">{{ $lote->cantidad_inicial }}</td>
                                                            <td class="py-2 px-4 text-sm text-right font-semibold">Q{{ number_format($lote->precio_ingreso, 2) }}</td>
                                                            <td class="py-2 px-4 text-sm">{{ $lote->fecha_ingreso ? \Carbon\Carbon::parse($lote->fecha_ingreso)->format('d/m/Y') : '-' }}</td>
                                                            <td class="py-2 px-4 text-sm text-gray-600">{{ $lote->observaciones ?? '-' }}</td>
                                                            <td class="py-2 px-4 text-center">
                                                                @if($lote->estado)
                                                                    <span class="bg-green-200 text-green-700 py-1 px-2 rounded-full text-xs font-semibold">Activo</span>
                                                                @else
                                                                    <span class="bg-gray-300 text-gray-700 py-1 px-2 rounded-full text-xs font-semibold">Inactivo</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-2 px-4 text-center">
                                                                <div class="flex item-center justify-center gap-1">
                                                                    <x-action-button
                                                                        type="edit"
                                                                        wire:click="editarLote({{ $lote->id }})"
                                                                        title="Editar lote" />
                                                                    @if($lote->estado)
                                                                        <x-action-button
                                                                            type="delete"
                                                                            wire:click="eliminarLote({{ $lote->id }})"
                                                                            title="Desactivar lote" />
                                                                    @else
                                                                        <x-action-button
                                                                            type="activate"
                                                                            wire:click="activarLote({{ $lote->id }})"
                                                                            title="Activar lote" />
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="text-center py-4 text-gray-500 bg-white border border-eemq-heather-100 rounded">
                                                No hay lotes registrados para este producto.
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">
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
                    {{ $editingId ? 'Editar Producto' : 'Crear Producto' }}
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
                        Código del Producto <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="codigo"
                        wire:model="codigo"
                        {{ $editingId ? 'disabled' : '' }}
                        class="w-full px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('codigo') border-eemq-crimson @enderror {{ $editingId ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                        placeholder="Ej: PROD-001">
                    @error('codigo')
                        <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="mb-4">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción <span class="text-red-500">*</span>
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
                            Categoría <span class="text-red-500">*</span>
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
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoriaId')
                        <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Es Consumible --}}
                <div class="mb-4">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="esConsumible"
                            class="mr-2 h-4 w-4 text-eemq-horizon focus:ring-eemq-horizon border-gray-300 rounded">
                        <span class="text-sm font-medium text-gray-700">Es producto consumible</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Los productos consumibles se agotan con el uso (ej. material de oficina)</p>
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
                        {{ $editingId ? 'Actualizar' : 'Crear' }}
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

    {{-- Modal Crear/Editar Lote --}}
    <div x-data="{
            show: @entangle('showModalLotes').live || @entangle('showModalEditarLote').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalLotes">
        <div class="relative p-6 border w-full max-w-2xl shadow-lg rounded-lg bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    {{ $editingLoteId ? 'Editar Lote' : 'Crear Lote' }}
                </h3>
                <button wire:click="closeModalLotes" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="guardarLote">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Cantidad --}}
                    <div class="mb-4">
                        <label for="loteCantidad" class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="loteCantidad"
                            wire:model="loteCantidad"
                            min="0"
                            class="w-full px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('loteCantidad') border-eemq-crimson @enderror"
                            placeholder="0">
                        @error('loteCantidad')
                            <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Precio de Ingreso --}}
                    <div class="mb-4">
                        <label for="lotePrecioIngreso" class="block text-sm font-medium text-gray-700 mb-2">
                            Precio de Ingreso <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="lotePrecioIngreso"
                            wire:model="lotePrecioIngreso"
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('lotePrecioIngreso') border-eemq-crimson @enderror"
                            placeholder="0.00">
                        @error('lotePrecioIngreso')
                            <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha de Ingreso --}}
                    <div class="mb-4">
                        <label for="loteFechaIngreso" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Ingreso <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="loteFechaIngreso"
                            wire:model="loteFechaIngreso"
                            class="w-full px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('loteFechaIngreso') border-eemq-crimson @enderror">
                        @error('loteFechaIngreso')
                            <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bodega --}}
                    <div class="mb-4">
                        <label for="loteBodegaId" class="block text-sm font-medium text-gray-700 mb-2">
                            Bodega <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="loteBodegaId"
                            wire:model="loteBodegaId"
                            class="w-full px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('loteBodegaId') border-eemq-crimson @enderror">
                            <option value="">Seleccione una bodega</option>
                            @foreach($bodegas as $bodega)
                                <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                            @endforeach
                        </select>
                        @error('loteBodegaId')
                            <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="mb-4">
                    <label for="loteObservaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones
                    </label>
                    <textarea
                        id="loteObservaciones"
                        wire:model="loteObservaciones"
                        rows="3"
                        class="w-full px-4 py-2 border-2 border-eemq-heather-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-eemq-horizon focus:border-transparent @error('loteObservaciones') border-eemq-crimson @enderror"
                        placeholder="Ingrese observaciones adicionales (opcional)"></textarea>
                    @error('loteObservaciones')
                        <p class="text-eemq-crimson text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button
                        type="button"
                        wire:click="closeModalLotes"
                        class="bg-eemq-heather-200 hover:bg-eemq-heather-300 text-gray-800 font-semibold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-eemq-horizon hover:bg-eemq-horizon-600 text-white font-semibold py-2 px-4 rounded">
                        {{ $editingLoteId ? 'Actualizar' : 'Crear' }}
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

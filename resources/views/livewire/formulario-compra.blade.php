{{--
    Vista para el Formulario de Registro de Nueva Compra.
    Esta vista es compleja y maneja la interfaz para:
    - Seleccionar o crear proveedores.
    - Ingresar datos de la factura.
    - Buscar y agregar productos a la compra.
    - Crear nuevos productos y categorías sobre la marcha a través de modales.
    Toda la interactividad es controlada por el componente `FormularioCompra`.
--}}
<div>
    {{-- Componente de Breadcrumbs para la navegación --}}
    <x-breadcrumbs :items="[
        ['label' => 'Compras', 'url' => '/compras', 'icon' => true],
        ['label' => 'Nueva Compra'],
    ]" />

    {{-- Encabezado de la página --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Registrar Nueva Compra</h1>
    </div>

    {{-- Contenedor principal del formulario --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form wire:submit.prevent="registrarCompra">

            {{-- Sección de Selección de Proveedor --}}
            <div class="mb-6">
                <label for="proveedor" class="block text-sm font-medium text-gray-700">Proveedor</label>
                <div class="relative">
                    {{-- Si ya hay un proveedor seleccionado, muestra sus detalles y un botón para limpiar la selección. --}}
                    @if($selectedProveedor)
                        <div class="flex items-center justify-between mt-1 w-full px-3 py-2 text-base border-2 border-gray-300 rounded-md shadow-sm">
                            <div class="flex flex-col gap-0.5 justify-center">
                                <span class="font-medium">{{ $selectedProveedor['nombre'] }}</span>
                                <span class="text-xs text-gray-500 mt-0.5">NIT: {{ $selectedProveedor['nit'] }}</span>
                            </div>
                            <button type="button" wire:click.prevent="clearProveedor" class="text-gray-400 hover:text-gray-600 text-xl">
                                ×
                            </button>
                        </div>
                    {{-- Si no hay proveedor seleccionado, muestra un campo de búsqueda con un dropdown. --}}
                    @else
                        <div class="relative" x-data="{ open: @entangle('showProveedorDropdown') }" @click.outside="open = false">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="searchProveedor"
                                @click="open = true"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm"
                                placeholder="Buscar proveedor..."
                            >
                            {{-- Dropdown con resultados de búsqueda y opción para crear un nuevo proveedor --}}
                            <div x-show="open" x-transition class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                <ul>
                                    @foreach (array_slice($this->proveedorResults, 0, 6) as $proveedor)
                                        <li wire:click.prevent="selectProveedor({{ $proveedor['id'] }})" class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                            {{ $proveedor['nombre'] }}
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="border-t border-gray-200">
                                    <button type="button" wire:click="abrirModalProveedor" class="w-full px-3 py-2 text-left text-blue-600 hover:bg-blue-50 font-semibold flex items-center gap-2">
                                        <span>+</span><span> Crear nuevo proveedor</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sección de Información de la Factura --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="numero_factura" class="block text-sm font-medium text-gray-700">Número de Factura:</label>
                    <input type="text" id="numero_factura" wire:model="numeroFactura" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Ingrese el número de factura...">
                </div>
                <div>
                    <label for="numero_serie" class="block text-sm font-medium text-gray-700">Número de Serie / Correlativo:</label>
                    <input type="text" id="numero_serie" wire:model="numeroSerie" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Ingrese el número de serie...">
                </div>
            </div>

            {{-- Sección de Búsqueda de Productos --}}
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center mb-2">
                    <label for="searchProducto" class="block text-sm font-medium text-gray-700">Buscar producto:</label>
                    <button type="button" wire:click="abrirModalProducto" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg">+ Nuevo Producto</button>
                </div>
                <div class="relative" x-data="{ open: @entangle('showProductoDropdown') }" @click.outside="open = false">
                    <input type="text" id="searchProducto" wire:model.live.debounce.300ms="searchProducto" @click="open = true" wire:keydown.enter.prevent="seleccionarPrimerResultado" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-md shadow-sm" placeholder="Buscar por código o descripción...">
                    {{-- Dropdown con resultados de búsqueda de productos --}}
                    <div x-show="open" x-transition class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto">
                        <ul>
                            @foreach ($this->productoResults as $producto)
                                <li wire:click.prevent="selectProducto({{ $producto['id'] }})" class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center">
                                    <span class="font-mono text-gray-500 mr-2">{{ $producto['codigo'] }}</span>
                                    <span>{{ $producto['descripcion'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Tabla de Productos Seleccionados --}}
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-800">Productos en la Compra</h2>
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Código</th>
                                <th class="py-3 px-6 text-left">Descripción</th>
                                <th class="py-3 px-6 text-center">Cantidad</th>
                                <th class="py-3 px-6 text-right">Costo Unitario</th>
                                <th class="py-3 px-6 text-right">Precio sin IVA</th>
                                <th class="py-3 px-6 text-right">Subtotal</th>
                                <th class="py-3 px-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($productosSeleccionados as $index => $producto)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left font-mono">{{ $producto['codigo'] }}</td>
                                    <td class="py-3 px-6 text-left">{{ $producto['descripcion'] }}</td>
                                    {{-- Input para la cantidad, bindeado a la propiedad del componente --}}
                                    <td class="py-3 px-6 text-center">
                                        <input type="number" wire:model.blur="productosSeleccionados.{{ $index }}.cantidad" min="1" placeholder="0" class="w-24 text-center border-2 border-blue-300 bg-blue-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-semibold">
                                    </td>
                                    {{-- Input para el costo, bindeado a la propiedad del componente --}}
                                    <td class="py-3 px-6 text-right">
                                        <div class="flex items-center justify-end">
                                            <span class="mr-1 px-3">Q</span>
                                            <input type="number" step="0.01" wire:model.blur="productosSeleccionados.{{ $index }}.costo" min="0" placeholder="0.00" class="w-28 text-right border-2 border-blue-300 bg-blue-50 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-semibold">
                                        </div>
                                    </td>
                                    {{-- Cálculo del precio sin IVA (asumiendo 12%) --}}
                                    <td class="py-3 px-6 text-right">
                                        <span class="text-gray-700 font-medium">Q{{ number_format((float)($producto['costo'] ?? 0) / 1.12, 2) }}</span>
                                    </td>
                                    {{-- Cálculo del subtotal por producto --}}
                                    <td class="py-3 px-6 text-right font-semibold">Q{{ number_format((float)($producto['cantidad'] ?? 0) * ((float)($producto['costo'] ?? 0) / 1.12), 2) }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <button type="button" wire:click="eliminarProducto({{ $producto['id'] }})" class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Sección de Totales --}}
            <div class="mt-8 flex justify-end">
                <div class="w-full max-w-sm">
                    <div class="flex justify-between py-2 border-b">
                        <span class="font-medium text-gray-700">Total (con IVA):</span>
                        <span class="font-bold text-lg text-gray-800">Q{{ number_format($this->total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Botón principal para registrar la compra --}}
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Registrar Compra
                </button>
            </div>
        </form>
    </div>

    {{-- Muestra mensajes flash de éxito --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Modal para Crear un Nuevo Producto --}}
    <div x-data="{ show: @entangle('showModalProducto').live }" x-show="show" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative p-6 border w-full max-w-lg shadow-lg rounded-lg bg-white" @click.away="show = false">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Crear Nuevo Producto</h3>
            <form wire:submit.prevent="guardarNuevoProducto">
                {{-- Campos del formulario del modal --}}
                <div class="mb-4">
                    <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">Código</label>
                    <input type="text" id="codigo" wire:model="codigo" class="w-full px-4 py-2 border-2 rounded-md @error('codigo') border-red-500 @enderror" placeholder="Ej: PROD-001">
                    @error('codigo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="descripcion" wire:model="descripcion" rows="4" class="w-full px-4 py-2 border-2 rounded-md @error('descripcion') border-red-500 @enderror"></textarea>
                    @error('descripcion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Campo de selección de categoría con búsqueda y opción de crear nueva --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <div x-data="{ open: @entangle('showCategoriaDropdown') }" @click.outside="open = false" class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchCategoria" @click="open = true" class="w-full px-4 py-2 border-2 rounded-md @error('categoriaId') border-red-500 @enderror" placeholder="Buscar categoría...">
                        <div x-show="open" class="absolute z-20 w-full bg-white border rounded-md mt-1 max-h-40 overflow-y-auto">
                            <ul>
                                @foreach ($this->categoriaResults as $categoria)
                                    <li wire:click.prevent="selectCategoria({{ $categoria['id'] }})" class="px-3 py-2 cursor-pointer hover:bg-gray-100">{{ $categoria['nombre'] }}</li>
                                @endforeach
                            </ul>
                            <button type="button" wire:click="abrirSubModalCategoria" class="w-full px-3 py-2 text-left text-blue-600 hover:bg-blue-50 font-semibold">+ Crear nueva categoría</button>
                        </div>
                    </div>
                    @if($selectedCategoria) <div class="mt-2 font-semibold">Seleccionado: {{ $selectedCategoria['nombre'] }} <button type="button" wire:click.prevent="clearCategoria" class="text-red-500 ml-2">x</button></div> @endif
                    @error('categoriaId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end mt-6 gap-4">
                    <button type="button" wire:click="closeModalProducto" class="bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Cancelar</button>
                    <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Crear y Agregar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal para Crear un Nuevo Proveedor --}}
    <div x-data="{ show: @entangle('showModalProveedor').live }" x-show="show" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative p-6 border w-full max-w-sm shadow-lg rounded-lg bg-white" @click.away="show = false">
             <h3 class="text-lg font-bold text-gray-900 mb-6">Crear Nuevo Proveedor</h3>
             <form wire:submit.prevent="guardarNuevoProveedor">
                <div class="mb-4">
                    <label for="nuevoProveedorNit" class="block text-sm font-medium text-gray-700">NIT</label>
                    <input type="text" id="nuevoProveedorNit" wire:model="nuevoProveedorNit" class="mt-1 w-full px-4 py-2 border-2 rounded-md @error('nuevoProveedorNit') border-red-500 @enderror">
                    @error('nuevoProveedorNit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Régimen</label>
                    <select wire:model="nuevoProveedorRegimen" class="mt-1 w-full px-4 py-2 border-2 rounded-md @error('nuevoProveedorRegimen') border-red-500 @enderror">
                        <option value="">Seleccione un régimen</option>
                        @foreach($regimenes as $regimen) <option value="{{ $regimen }}">{{ $regimen }}</option> @endforeach
                    </select>
                    @error('nuevoProveedorRegimen') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label for="nuevoProveedorNombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="nuevoProveedorNombre" wire:model="nuevoProveedorNombre" class="mt-1 w-full px-4 py-2 border-2 rounded-md @error('nuevoProveedorNombre') border-red-500 @enderror">
                    @error('nuevoProveedorNombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end mt-6 gap-4">
                    <button type="button" wire:click="closeModalProveedor" class="bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Cancelar</button>
                    <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Crear</button>
                </div>
             </form>
        </div>
    </div>

    {{-- Sub-modal para Crear una Nueva Categoría --}}
    <div x-data="{ show: @entangle('showSubModalCategoria').live }" x-show="show" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-75 h-full w-full flex items-center justify-center z-[60]">
        <div class="relative p-6 w-full max-w-sm bg-white rounded-lg" @click.away="show = false">
            <h3 class="text-lg font-bold mb-6">Nueva Categoría</h3>
            <form wire:submit.prevent="guardarNuevaCategoria">
                <div class="mb-4">
                    <label for="nuevaCategoriaNombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="nuevaCategoriaNombre" wire:model="nuevaCategoriaNombre" class="mt-1 w-full px-4 py-2 border-2 rounded-md @error('nuevaCategoriaNombre') border-red-500 @enderror">
                    @error('nuevaCategoriaNombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                 <div class="flex justify-end mt-6 gap-4">
                    <button type="button" wire:click="closeSubModalCategoria" class="bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Cancelar</button>
                    <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

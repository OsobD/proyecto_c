<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Registrar Nueva Compra</h1>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Provider Selection --}}
                <div>
                    <label for="proveedor" class="block text-sm font-medium text-gray-700">Proveedor</label>
                    <select id="proveedor" name="proveedor" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option>Seleccione un proveedor</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{ $proveedor['id'] }}">{{ $proveedor['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Product Search --}}
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Añadir Producto</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" placeholder="Buscar producto...">
                        {{-- Add Button --}}
                        <button type="button" class="absolute inset-y-0 right-0 px-4 py-2 bg-blue-600 text-white rounded-r-md">Añadir</button>
                    </div>
                </div>
            </div>

            {{-- Products List Table --}}
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-800">Productos en la Compra</h2>
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Descripción</th>
                                <th class="py-3 px-6 text-center">Cantidad</th>
                                <th class="py-3 px-6 text-right">Costo Unitario</th>
                                <th class="py-3 px-6 text-right">Subtotal</th>
                                <th class="py-3 px-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            {{-- Example Row --}}
                            <tr class="border-b border-gray-200">
                                <td class="py-3 px-6 text-left">Tornillos de acero inoxidable</td>
                                <td class="py-3 px-6 text-center"><input type="number" value="100" class="w-24 text-center border-gray-300 rounded-md"></td>
                                <td class="py-3 px-6 text-right"><input type="number" step="0.01" value="15.50" class="w-24 text-right border-gray-300 rounded-md"></td>
                                <td class="py-3 px-6 text-right">Q 1,550.00</td>
                                <td class="py-3 px-6 text-center">
                                    <button type="button" class="text-red-600 hover:text-red-800">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Totals --}}
            <div class="mt-8 flex justify-end">
                <div class="w-full max-w-sm">
                    <div class="flex justify-between py-2 border-b">
                        <span class="font-medium text-gray-700">Subtotal:</span>
                        <span class="text-gray-800">Q 1,550.00</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="font-medium text-gray-700">IVA (12%):</span>
                        <span class="text-gray-800">Q 186.00</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="font-bold text-lg text-gray-800">Total:</span>
                        <span class="font-bold text-lg text-gray-800">Q 1,736.00</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                    Registrar Compra
                </button>
            </div>
        </form>
    </div>
</div>

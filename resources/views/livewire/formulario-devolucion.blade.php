<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Registrar Devolución de Material</h1>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
        <form>
            <div class="space-y-6">
                {{-- Product Selection --}}
                <div>
                    <label for="producto" class="block text-sm font-medium text-gray-700">Producto a devolver</label>
                    <select id="producto" name="producto" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option>Seleccione un producto</option>
                        @foreach ($productos as $producto)
                            <option value="{{ $producto['codigo'] }}">{{ $producto['descripcion'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Quantity --}}
                <div>
                    <label for="cantidad" class="block text-sm font-medium text-gray-700">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Ingrese la cantidad">
                </div>

                {{-- Reason --}}
                <div>
                    <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo de la Devolución (Opcional)</label>
                    <textarea id="motivo" name="motivo" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Registrar Devolución
                </button>
            </div>
        </form>
    </div>
</div>

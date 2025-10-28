<div>
    {{-- Company Logo --}}
    <div class="flex justify-center my-8">
        <img src="https://via.placeholder.com/400x150" alt="Company Logo" class="rounded-lg shadow-md">
    </div>

    {{-- Main Navigation Menu --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('compras') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-eemq-horizon">
            <h3 class="text-lg font-semibold text-gray-800">Compras</h3>
            <p class="text-sm text-gray-600 mt-1">Gestionar compras y proveedores</p>
        </a>
        <a href="{{ route('traslados') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-eemq-horizon">
            <h3 class="text-lg font-semibold text-gray-800">Traslados</h3>
            <p class="text-sm text-gray-600 mt-1">Gestionar traslados y requisiciones</p>
        </a>
        <a href="{{ route('bodegas') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-eemq-horizon">
            <h3 class="text-lg font-semibold text-gray-800">Bodegas</h3>
            <p class="text-sm text-gray-600 mt-1">Gestionar bodegas</p>
        </a>
        <a href="{{ route('proveedores') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-eemq-horizon">
            <h3 class="text-lg font-semibold text-gray-800">Proveedores</h3>
            <p class="text-sm text-gray-600 mt-1">Gestionar proveedores</p>
        </a>
        <a href="{{ route('reportes') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-eemq-horizon">
            <h3 class="text-lg font-semibold text-gray-800">Reportes</h3>
            <p class="text-sm text-gray-600 mt-1">Generar reportes</p>
        </a>
        <a href="{{ route('bitacora') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-eemq-horizon">
            <h3 class="text-lg font-semibold text-gray-800">Bitácora</h3>
            <p class="text-sm text-gray-600 mt-1">Ver bitácora del sistema</p>
        </a>
        <a href="{{ route('configuracion') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-eemq-horizon">
            <h3 class="text-lg font-semibold text-gray-800">Configuración</h3>
            <p class="text-sm text-gray-600 mt-1">Configuración del sistema</p>
        </a>
    </div>
</div>

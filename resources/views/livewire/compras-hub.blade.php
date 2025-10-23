<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Compras'],
    ]" />

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Módulo de Compras</h1>
    </div>

    {{-- Estadísticas Rápidas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Compras este Mes</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['total_mes'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Monto Total</p>
                    <p class="text-2xl xl:text-3xl font-bold text-gray-800 mt-1">Q{{ number_format($estadisticas['monto_total_mes'], 2) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-purple-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Proveedores Activos</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['proveedores_activos'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Accesos Rápidos --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16 mt-12">
        <a href="{{ route('compras.nueva') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-blue-600">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Nueva Compra</h3>
                    <p class="text-xs text-gray-600 mt-0.5">Registrar una nueva compra</p>
                </div>
            </div>
        </a>

        <a href="{{ route('compras.historial') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-green-600">
            <div class="flex items-center gap-4">
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Historial</h3>
                    <p class="text-xs text-gray-600 mt-0.5">Ver todas las compras</p>
                </div>
            </div>
        </a>

        <a href="{{ route('proveedores') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-purple-600">
            <div class="flex items-center gap-4">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Proveedores</h3>
                    <p class="text-xs text-gray-600 mt-0.5">Gestionar proveedores</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Compras Recientes --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Compras Recientes</h2>
            <a href="{{ route('compras.historial') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Ver todas →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">No. Factura</th>
                        <th class="py-3 px-6 text-left">Proveedor</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-right">Monto</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach($comprasRecientes as $compra)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 text-left font-medium">{{ $compra['numero_factura'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $compra['proveedor'] }}</td>
                            <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($compra['fecha'])->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-right font-semibold">Q{{ number_format($compra['monto'], 2) }}</td>
                            <td class="py-3 px-6 text-center">
                                @if($compra['estado'] === 'Completada')
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Completada
                                    </span>
                                @else
                                    <span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <button class="text-blue-600 hover:text-blue-800 font-medium mr-2">Ver</button>
                                <button class="text-gray-600 hover:text-gray-800 font-medium">Editar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

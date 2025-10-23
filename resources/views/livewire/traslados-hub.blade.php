<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Traslados'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Módulo de Traslados</h1>
    </div>

    {{-- Estadísticas Rápidas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Requisiciones</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['requisiciones_mes'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Este mes</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Traslados</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['traslados_mes'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Este mes</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-purple-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Devoluciones</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['devoluciones_mes'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Este mes</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Pendientes</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $estadisticas['pendientes'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Requieren atención</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Accesos Rápidos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('requisiciones') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-blue-600">
            <div class="flex flex-col items-center text-center">
                <div class="bg-blue-100 p-4 rounded-lg mb-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Nueva Requisición</h3>
                <p class="text-sm text-gray-600 mt-1">Solicitar productos</p>
            </div>
        </a>

        <a href="{{ route('traslados.nuevo') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-green-600">
            <div class="flex flex-col items-center text-center">
                <div class="bg-green-100 p-4 rounded-lg mb-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Nuevo Traslado</h3>
                <p class="text-sm text-gray-600 mt-1">Trasladar entre bodegas</p>
            </div>
        </a>

        <a href="{{ route('devoluciones') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-purple-600">
            <div class="flex flex-col items-center text-center">
                <div class="bg-purple-100 p-4 rounded-lg mb-3">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Nueva Devolución</h3>
                <p class="text-sm text-gray-600 mt-1">Devolver productos</p>
            </div>
        </a>

        <a href="{{ route('traslados.historial') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow border-t-4 border-gray-600">
            <div class="flex flex-col items-center text-center">
                <div class="bg-gray-100 p-4 rounded-lg mb-3">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Historial</h3>
                <p class="text-sm text-gray-600 mt-1">Ver todos los movimientos</p>
            </div>
        </a>
    </div>

    {{-- Movimientos Recientes --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Movimientos Recientes</h2>
            <a href="{{ route('traslados.historial') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Ver todos →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Tipo</th>
                        <th class="py-3 px-6 text-left">Correlativo</th>
                        <th class="py-3 px-6 text-left">Origen</th>
                        <th class="py-3 px-6 text-left">Destino</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach($trasladosRecientes as $traslado)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 text-left">
                                @if($traslado['tipo'] === 'Requisición')
                                    <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Requisición
                                    </span>
                                @elseif($traslado['tipo'] === 'Traslado')
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Traslado
                                    </span>
                                @else
                                    <span class="bg-purple-200 text-purple-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Devolución
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left font-medium">{{ $traslado['correlativo'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $traslado['origen'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $traslado['destino'] }}</td>
                            <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($traslado['fecha'])->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-center">
                                @if($traslado['estado'] === 'Completado')
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        Completado
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

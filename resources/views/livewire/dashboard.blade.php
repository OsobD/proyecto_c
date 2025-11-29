<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-gray-600">Bienvenido al sistema de gestión de inventario EEMQ</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-gray-500 text-sm">Total Productos</p>
                    <p class="text-gray-900 text-2xl font-bold">{{ \App\Models\Producto::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-gray-500 text-sm">Bodegas</p>
                    <p class="text-gray-900 text-2xl font-bold">{{ \App\Models\Bodega::count() }}</p>
                </div>
            </div>
        </div>


        @if(auth()->user()->rol && auth()->user()->rol->nombre !== 'Colaborador de Bodega')
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-gray-500 text-sm">Usuarios</p>
                        <p class="text-gray-900 text-2xl font-bold">{{ \App\Models\Usuario::count() }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Información del Usuario</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(auth()->user()->persona)
                    <div>
                        <p class="text-sm text-gray-600">Nombre Completo</p>
                        <p class="text-gray-900 font-medium">{{ auth()->user()->persona->nombres }}
                            {{ auth()->user()->persona->apellidos }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Correo Electrónico</p>
                        <p class="text-gray-900 font-medium">{{ auth()->user()->persona->correo }}</p>
                    </div>
                @endif
                @if(auth()->user()->rol)
                    <div>
                        <p class="text-sm text-gray-600">Rol</p>
                        <p class="text-gray-900 font-medium">{{ auth()->user()->rol->nombre }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-sm text-gray-600">Estado</p>
                    <span
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ auth()->user()->estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ auth()->user()->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Nota de seguridad:</strong> Este sistema utiliza Argon2id para el hashing de contraseñas.
                </p>
            </div>
        </div>
    </div>
</div>
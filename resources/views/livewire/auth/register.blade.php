<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Crear Cuenta</h2>
        <p class="mt-2 text-sm text-gray-600">Completa el formulario para registrarte</p>
    </div>

    <form wire:submit.prevent="register" class="space-y-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="nombres" class="block text-sm font-medium text-gray-700">
                    Nombres <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="nombres"
                    id="nombres"
                    type="text"
                    class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nombres') border-red-500 @enderror"
                    placeholder="Juan"
                >
                @error('nombres')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="apellidos" class="block text-sm font-medium text-gray-700">
                    Apellidos <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="apellidos"
                    id="apellidos"
                    type="text"
                    class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('apellidos') border-red-500 @enderror"
                    placeholder="Pérez"
                >
                @error('apellidos')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
                Nombre de Usuario <span class="text-red-500">*</span>
            </label>
            <input
                wire:model="name"
                id="name"
                type="text"
                class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-500 @enderror"
                placeholder="juanperez"
            >
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                Correo Electrónico <span class="text-red-500">*</span>
            </label>
            <input
                wire:model="email"
                id="email"
                type="email"
                autocomplete="email"
                class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-500 @enderror"
                placeholder="juan@example.com"
            >
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="telefono" class="block text-sm font-medium text-gray-700">
                Teléfono
            </label>
            <input
                wire:model="telefono"
                id="telefono"
                type="text"
                class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('telefono') border-red-500 @enderror"
                placeholder="0000-0000"
            >
            @error('telefono')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Contraseña <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    autocomplete="new-password"
                    class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-500 @enderror"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    Confirmar Contraseña <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="password_confirmation"
                    id="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="••••••••"
                >
            </div>
        </div>

        <div>
            <button
                type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Crear Cuenta</span>
                <span wire:loading>Creando cuenta...</span>
            </button>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                ¿Ya tienes una cuenta?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Inicia sesión aquí
                </a>
            </p>
        </div>
    </form>
</div>

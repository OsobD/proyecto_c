<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Iniciar Sesión</h2>
        <p class="mt-2 text-sm text-gray-600">Ingresa tus credenciales para acceder al sistema</p>
    </div>

    <form wire:submit.prevent="login" class="space-y-6">
        @if (session()->has('message'))
            <div class="bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                Correo Electrónico
            </label>
            <div class="mt-1">
                <input
                    wire:model="email"
                    id="email"
                    type="email"
                    autocomplete="email"
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-500 @enderror"
                    placeholder="admin@eemq.com"
                >
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
                Contraseña
            </label>
            <div class="mt-1">
                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    autocomplete="current-password"
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-500 @enderror"
                    placeholder="••••••••"
                >
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input
                    wire:model="remember"
                    id="remember"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <label for="remember" class="ml-2 block text-sm text-gray-900">
                    Recordarme
                </label>
            </div>
        </div>

        <div>
            <button
                type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Iniciar Sesión</span>
                <span wire:loading>Iniciando sesión...</span>
            </button>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                ¿No tienes una cuenta?
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Regístrate aquí
                </a>
            </p>
        </div>
    </form>
</div>

<div>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">
            Cambiar Contraseña
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Por seguridad, configura tu nueva contraseña
        </p>
    </div>

    <!-- Mensajes -->
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="cambiarContrasena" class="space-y-4">
        <!-- Contraseña Actual -->
        <div>
            <label for="contrasena_actual" class="block text-sm font-medium text-gray-700">
                Contraseña Actual
            </label>
            <div class="mt-1">
                <input 
                    id="contrasena_actual" 
                    type="password" 
                    wire:model="contrasena_actual"
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('contrasena_actual') border-red-500 @enderror"
                    placeholder="••••••••"
                >
            </div>
            @error('contrasena_actual')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nueva Contraseña -->
        <div>
            <label for="contrasena_nueva" class="block text-sm font-medium text-gray-700">
                Nueva Contraseña
            </label>
            <div class="mt-1">
                <input 
                    id="contrasena_nueva" 
                    type="password" 
                    wire:model="contrasena_nueva"
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('contrasena_nueva') border-red-500 @enderror"
                    placeholder="••••••••"
                >
            </div>
            @error('contrasena_nueva')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirmar Contraseña -->
        <div>
            <label for="contrasena_confirmacion" class="block text-sm font-medium text-gray-700">
                Confirmar Contraseña
            </label>
            <div class="mt-1">
                <input 
                    id="contrasena_confirmacion" 
                    type="password" 
                    wire:model="contrasena_confirmacion"
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('contrasena_confirmacion') border-red-500 @enderror"
                    placeholder="••••••••"
                >
            </div>
            @error('contrasena_confirmacion')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Requisitos Compactos -->
        <div class="bg-gray-50 rounded-md p-3 text-xs text-gray-500 border border-gray-200">
            <p class="font-medium text-gray-700 mb-1">Requisitos:</p>
            <div class="grid grid-cols-2 gap-1">
                <span class="flex items-center"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>Min 8 caracteres</span>
                <span class="flex items-center"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>1 Mayúscula</span>
                <span class="flex items-center"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>1 Minúscula</span>
                <span class="flex items-center"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>1 Número</span>
                <span class="flex items-center col-span-2"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>1 Especial (@$!%*#?&)</span>
            </div>
        </div>

        <!-- Botón -->
        <div>
            <button 
                type="submit" 
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Guardar Contraseña</span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>
    </form>
</div>

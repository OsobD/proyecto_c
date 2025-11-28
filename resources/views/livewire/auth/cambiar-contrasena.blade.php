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
            <div class="mt-1 relative">
                <input id="contrasena_actual" type="password" wire:model="contrasena_actual"
                    class="appearance-none block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('contrasena_actual') border-red-500 @enderror"
                    placeholder="••••••••">
                <button type="button" onclick="togglePasswordVisibility('contrasena_actual', this)"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="h-5 w-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="h-5 w-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
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
            <div class="mt-1 relative">
                <input id="contrasena_nueva" type="password" wire:model="contrasena_nueva"
                    class="appearance-none block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('contrasena_nueva') border-red-500 @enderror"
                    placeholder="••••••••">
                <button type="button" onclick="togglePasswordVisibility('contrasena_nueva', this)"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="h-5 w-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="h-5 w-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
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
            <div class="mt-1 relative">
                <input id="contrasena_confirmacion" type="password" wire:model="contrasena_confirmacion"
                    class="appearance-none block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('contrasena_confirmacion') border-red-500 @enderror"
                    placeholder="••••••••">
                <button type="button" onclick="togglePasswordVisibility('contrasena_confirmacion', this)"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="h-5 w-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="h-5 w-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @error('contrasena_confirmacion')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Requisitos Compactos -->
        <div class="bg-gray-50 rounded-md p-3 text-xs text-gray-500 border border-gray-200">
            <p class="font-medium text-gray-700 mb-1">Requisitos:</p>
            <div class="grid grid-cols-2 gap-1">
                <span class="flex items-center"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>Min 8
                    caracteres</span>
                <span class="flex items-center"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>1
                    Mayúscula</span>
                <span class="flex items-center"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>1
                    Minúscula</span>
                <span class="flex items-center"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>1
                    Número</span>
                <span class="flex items-center col-span-2"><span
                        class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>1 Especial (@$!%*#?&)</span>
            </div>
        </div>

        <!-- Botón -->
        <div>
            <button type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Guardar Contraseña</span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>
    </form>

    <script>
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeIcon = button.querySelector('.eye-icon');
            const eyeOffIcon = button.querySelector('.eye-off-icon');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</div>
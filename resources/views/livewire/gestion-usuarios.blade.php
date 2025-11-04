{{--
    Vista: Gestión de Usuarios
    Descripción: CRUD completo de usuarios con búsqueda, filtros, ordenamiento,
                 sincronización dual (users + usuario), y gestión de contraseñas
--}}
<div>
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => '/', 'icon' => true],
        ['label' => 'Usuarios'],
    ]" />

    {{-- Encabezado con título --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h1>
    </div>

    {{-- Barra de búsqueda y filtros --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            {{-- Búsqueda --}}
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar usuario</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        placeholder="Nombre de usuario, nombre completo o email...">
                </div>
            </div>

            {{-- Filtro por Rol --}}
            <div class="w-full md:w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por rol</label>
                <select
                    wire:model.live="filterRol"
                    class="w-full px-4 py-3 border border-gray-300 rounded-md bg-white hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23666%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem] bg-[right_0.5rem_center] bg-no-repeat pr-10">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Botón Nuevo Usuario --}}
            <div>
                <button
                    wire:click="abrirModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg whitespace-nowrap shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                    + Nuevo Usuario
                </button>
            </div>
        </div>
    </div>

    {{-- Contenedor principal --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        {{-- Tabla de usuarios --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">
                            <button
                                wire:click="sortBy('nombre_usuario')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold">
                                Usuario
                                @if($sortField === 'nombre_usuario')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-left">
                            <button
                                wire:click="sortBy('nombre_completo')"
                                class="flex items-center gap-2 hover:text-gray-900 font-semibold">
                                Nombre Completo
                                @if($sortField === 'nombre_completo')
                                    @if($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">↕</span>
                                @endif
                            </button>
                        </th>
                        <th class="py-3 px-6 text-left">Email</th>
                        <th class="py-3 px-6 text-left">Rol</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($usuarios as $usuario)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 text-left">
                                <span class="font-mono font-medium text-gray-800">{{ $usuario->nombre_usuario }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="font-medium">{{ $usuario->persona->nombres }} {{ $usuario->persona->apellidos }}</span>
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $usuario->persona->correo }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span class="bg-purple-200 text-purple-800 py-1 px-3 rounded-full text-xs font-semibold">
                                    {{ $usuario->rol->nombre }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <button
                                    wire:click="toggleEstado({{ $usuario->id }})"
                                    class="py-1 px-3 rounded-full text-xs font-semibold {{ $usuario->estado ? 'bg-green-200 text-green-800 hover:bg-green-300' : 'bg-red-200 text-red-800 hover:bg-red-300' }}">
                                    {{ $usuario->estado ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Editar --}}
                                    <button
                                        wire:click="editarUsuario({{ $usuario->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 hover:bg-blue-200 text-blue-600"
                                        title="Editar usuario">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    {{-- Resetear Contraseña --}}
                                    <button
                                        wire:click="resetearPassword({{ $usuario->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-100 hover:bg-yellow-200 text-yellow-600"
                                        title="Resetear contraseña">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-gray-500">
                                No se encontraron usuarios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($usuarios->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>

    {{-- Mensajes flash --}}
    @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg z-50 animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg z-50 animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Modal: Crear Nuevo Usuario --}}
    <div x-data="{
            show: @entangle('showModal').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModal"
         wire:ignore.self>
        <div class="relative border w-full max-w-2xl shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-hidden"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="p-8 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Crear Nuevo Usuario</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="guardarUsuario">
                {{-- Sección: Datos de la Persona --}}
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Información Personal</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nombres --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                            <input
                                type="text"
                                wire:model="nombres"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('nombres') border-red-500 ring-2 ring-red-200 @enderror"
                                placeholder="Ej: Juan Carlos">
                            @error('nombres')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Apellidos --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos *</label>
                            <input
                                type="text"
                                wire:model="apellidos"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('apellidos') border-red-500 ring-2 ring-red-200 @enderror"
                                placeholder="Ej: Pérez García">
                            @error('apellidos')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Teléfono --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                            <input
                                type="text"
                                wire:model="telefono"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('telefono') border-red-500 ring-2 ring-red-200 @enderror"
                                placeholder="Ej: 5555-5555">
                            @error('telefono')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Correo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico *</label>
                            <input
                                type="email"
                                wire:model="correo"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('correo') border-red-500 ring-2 ring-red-200 @enderror"
                                placeholder="Ej: usuario@eemq.com">
                            @error('correo')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Fecha de Nacimiento --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento *</label>
                            <input
                                type="date"
                                wire:model="fecha_nacimiento"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md bg-white hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('fecha_nacimiento') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('fecha_nacimiento')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Género --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Género *</label>
                            <select
                                wire:model="genero"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md bg-white hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23666%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem] bg-[right_0.5rem_center] bg-no-repeat pr-10 @error('genero') border-red-500 ring-2 ring-red-200 @enderror">
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            @error('genero')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Sección: Datos de Usuario --}}
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Credenciales de Acceso</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nombre de Usuario --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de Usuario *</label>
                            <input
                                type="text"
                                wire:model="nombre_usuario"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('nombre_usuario') border-red-500 ring-2 ring-red-200 @enderror"
                                placeholder="Ej: jperez">
                            @error('nombre_usuario')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Rol --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rol *</label>
                            <div class="relative">
                                @if($selectedRol)
                                    <div class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm @error('rolId') border-red-500 ring-2 ring-red-200 @enderror">
                                        <span class="font-medium">{{ $selectedRol['nombre'] }}</span>
                                        <button type="button" wire:click.prevent="clearRol" class="text-gray-400 hover:text-gray-600 text-xl">
                                            ×
                                        </button>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showRolDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchRol"
                                            @click="open = true"
                                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('rolId') border-red-500 ring-2 ring-red-200 @enderror"
                                            placeholder="Buscar rol...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                            <ul>
                                                @foreach (array_slice($this->rolResults, 0, 6) as $rol)
                                                    <li wire:click.prevent="selectRol({{ $rol['id'] }})"
                                                        class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                        {{ $rol['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @error('rolId')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Generador de Contraseña --}}
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm text-yellow-800 font-medium">
                                    Se generará automáticamente una contraseña temporal segura al crear el usuario.
                                </p>
                                <p class="text-xs text-yellow-700 mt-1">
                                    La contraseña se mostrará una sola vez después de crear el usuario.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="guardarUsuario">✓ Crear Usuario</span>
                        <span wire:loading wire:target="guardarUsuario" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creando...
                        </span>
                    </button>
                </div>
            </form>
            </div>
        </div>
    </div>

    {{-- Modal: Editar Usuario --}}
    <div x-data="{
            show: @entangle('showModalEditar').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalEditar"
         wire:ignore.self>
        <div class="relative border w-full max-w-2xl shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-hidden"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="p-8 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Editar Usuario</h3>
                <button wire:click="closeModalEditar" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="actualizarUsuario">
                {{-- Información de Usuario (no editable) --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-md">
                    <p class="text-sm text-gray-600">Nombre de usuario:</p>
                    <p class="font-mono font-semibold text-gray-800">{{ $nombre_usuario }}</p>
                </div>

                {{-- Sección: Datos de la Persona --}}
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Información Personal</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nombres --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                            <input
                                type="text"
                                wire:model="nombres"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('nombres') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('nombres')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Apellidos --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos *</label>
                            <input
                                type="text"
                                wire:model="apellidos"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('apellidos') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('apellidos')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Teléfono --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                            <input
                                type="text"
                                wire:model="telefono"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('telefono') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('telefono')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Correo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico *</label>
                            <input
                                type="email"
                                wire:model="correo"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('correo') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('correo')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Fecha de Nacimiento --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento *</label>
                            <input
                                type="date"
                                wire:model="fecha_nacimiento"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md bg-white hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('fecha_nacimiento') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('fecha_nacimiento')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Género --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Género *</label>
                            <select
                                wire:model="genero"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md bg-white hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23666%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem] bg-[right_0.5rem_center] bg-no-repeat pr-10 @error('genero') border-red-500 ring-2 ring-red-200 @enderror">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            @error('genero')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Sección: Rol y Estado --}}
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Configuración</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Rol --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rol *</label>
                            <div class="relative">
                                @if($selectedRol)
                                    <div class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm @error('rolId') border-red-500 ring-2 ring-red-200 @enderror">
                                        <span class="font-medium">{{ $selectedRol['nombre'] }}</span>
                                        <button type="button" wire:click.prevent="clearRol" class="text-gray-400 hover:text-gray-600 text-xl">
                                            ×
                                        </button>
                                    </div>
                                @else
                                    <div class="relative" x-data="{ open: @entangle('showRolDropdown').live }" @click.outside="open = false">
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="searchRol"
                                            @click="open = true"
                                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('rolId') border-red-500 ring-2 ring-red-200 @enderror"
                                            placeholder="Buscar rol...">
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                            <ul>
                                                @foreach (array_slice($this->rolResults, 0, 6) as $rol)
                                                    <li wire:click.prevent="selectRol({{ $rol['id'] }})"
                                                        class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                                        {{ $rol['nombre'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @error('rolId')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <div class="flex items-center mt-3">
                                <input
                                    type="checkbox"
                                    wire:model="estado"
                                    id="estado-edit"
                                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="estado-edit" class="ml-2 text-sm text-gray-700">
                                    Usuario activo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        wire:click="closeModalEditar"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="actualizarUsuario">✓ Guardar Cambios</span>
                        <span wire:loading wire:target="actualizarUsuario" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>
            </div>
        </div>
    </div>

    {{-- Modal: Mostrar Contraseña Temporal --}}
    <div x-data="{
            show: @entangle('showModalPassword').live,
            animatingOut: false
         }"
         x-show="show || animatingOut"
         x-cloak
         x-init="$watch('show', value => { if (!value) animatingOut = true; })"
         @animationend="if (!show) animatingOut = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         :style="!show && animatingOut ? 'animation: fadeOut 0.2s ease-in;' : (show ? 'animation: fadeIn 0.2s ease-out;' : '')"
         wire:click.self="closeModalPassword"
         wire:ignore.self>
        <div class="relative p-8 border w-full max-w-md shadow-2xl rounded-xl bg-white"
             :style="!show && animatingOut ? 'animation: slideUp 0.2s ease-in;' : (show ? 'animation: slideDown 0.3s ease-out;' : '')"
             @click.stop>
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Contraseña Temporal Generada</h3>
                <p class="text-sm text-gray-600">
                    Copie esta contraseña y entréguela al usuario de forma segura.
                </p>
            </div>

            <div class="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-4 mb-4">
                <p class="text-xs text-yellow-800 font-semibold mb-2 text-center">
                    ⚠️ ESTA CONTRASEÑA SE MOSTRARÁ UNA SOLA VEZ
                </p>
                <div class="flex items-center gap-2 bg-white p-3 rounded border border-yellow-200">
                    <code class="flex-1 text-center font-mono text-lg font-bold text-gray-800 select-all">
                        {{ $passwordGenerada }}
                    </code>
                    <button
                        onclick="navigator.clipboard.writeText('{{ $passwordGenerada }}'); alert('Contraseña copiada al portapapeles');"
                        class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded"
                        title="Copiar contraseña">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6">
                <p class="text-xs text-blue-800">
                    <strong>Nota:</strong> El usuario puede cambiar esta contraseña desde su perfil después de iniciar sesión.
                </p>
            </div>

            <div class="flex justify-center">
                <button
                    wire:click="closeModalPassword"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                    ✓ Entendido
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Ocultar elementos hasta que Alpine.js esté listo */
        [x-cloak] {
            display: none !important;
        }

        /* Estilos para input date y select */
        input[type="date"] {
            position: relative;
            cursor: pointer;
        }

        /* Eliminar estilos del calendario nativo de Chrome/Edge */
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: opacity(0.6);
            transition: filter 0.2s;
        }

        input[type="date"]:hover::-webkit-calendar-picker-indicator {
            filter: opacity(1);
        }

        /* Eliminar la franja negra en Firefox */
        input[type="date"]::-moz-focus-inner {
            border: 0;
            padding: 0;
        }

        /* Mejorar apariencia del calendario en Firefox */
        input[type="date"]::-moz-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        input[type="date"]:hover::-moz-calendar-picker-indicator {
            opacity: 1;
        }

        /* Mejorar dropdown de select */
        select {
            cursor: pointer;
        }

        select option {
            padding: 8px;
        }

        /* Animaciones de entrada */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Animaciones de salida */
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(20px);
                opacity: 0;
            }
        }

        /* Animación de mensajes flash */
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
</div>

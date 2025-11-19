{{--
    Componente: Botón de Acción Estandarizado
    Descripción: Botón con forma cuadrada con bordes redondeados para acciones en tablas

    Props:
    - type: 'view' | 'edit' | 'delete' | 'activate' | 'lotes' | 'custom' (default: 'custom')
    - title: Texto del tooltip
    - badge: Número opcional para mostrar como badge (solo para tipo 'lotes')
    - wire:click / @click: Acción al hacer click (pasar como atributo)

    Uso:
    <x-action-button type="view" title="Ver detalle" wire:click="verDetalle({{ $id }})" />
    <x-action-button type="edit" title="Editar" wire:click="editar({{ $id }})" />
    <x-action-button type="delete" title="Eliminar" wire:click="eliminar({{ $id }})" />
    <x-action-button type="activate" title="Activar" wire:click="activar({{ $id }})" />
    <x-action-button type="lotes" title="Ver lotes" badge="5" wire:click="verLotes({{ $id }})" />
--}}

@props(['type' => 'custom', 'title' => '', 'badge' => null])

@php
    // Configuración de colores y iconos según el tipo - Colores consistentes de Tailwind
    $config = [
        'view' => [
            'bg' => 'bg-gray-100 hover:bg-gray-200',
            'text' => 'text-gray-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />'
        ],
        'edit' => [
            'bg' => 'bg-yellow-100 hover:bg-yellow-200',
            'text' => 'text-yellow-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />'
        ],
        'delete' => [
            'bg' => 'bg-red-100 hover:bg-red-200',
            'text' => 'text-red-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'
        ],
        'activate' => [
            'bg' => 'bg-green-100 hover:bg-green-200',
            'text' => 'text-green-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'
        ],
        'lotes' => [
            'bg' => 'bg-gray-100 hover:bg-gray-200',
            'text' => 'text-gray-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />'
        ],
        'custom' => [
            'bg' => 'bg-gray-100 hover:bg-gray-200',
            'text' => 'text-gray-600',
            'icon' => ''
        ]
    ];

    $currentConfig = $config[$type] ?? $config['custom'];
@endphp

<button
    {{ $attributes->merge([
        'class' => "relative w-8 h-8 flex items-center justify-center rounded-md {$currentConfig['bg']} transition-all duration-150",
        'title' => $title
    ]) }}
>
    @if($type !== 'custom' && isset($currentConfig['icon']))
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $currentConfig['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            {!! $currentConfig['icon'] !!}
        </svg>
    @else
        {{ $slot }}
    @endif

    @if($badge !== null && $type === 'lotes')
        <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-4 min-w-[1rem] flex items-center justify-center px-1">
            {{ $badge }}
        </span>
    @endif
</button>

@props([
    'label' => null,
    'placeholder' => 'Seleccionar...',
    'searchPlaceholder' => 'Buscar...',
    'selectedValue' => null,
    'selectedLabel' => null,
    'results' => [],
    'searchModel' => null, // Wire model for search input
    'onSelect' => null, // Method to call on selection
    'onClear' => null, // Method to call on clear
    'required' => false,
    'error' => null,
    'disabled' => false,
])

<div
    x-data="{ open: false }"
    @click.outside="open = false"
    @close-all-dropdowns.window="open = false"
    class="relative"
>
    @if($label)
        <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }} @if($required) * @endif
        </label>
    @endif

    <div class="relative">
        @if($selectedValue)
            {{-- Selected State --}}
            <div
                wire:click="{{ $onClear }}"
                @click.stop
                class="flex items-center justify-between w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm bg-white {{ !$disabled ? 'cursor-pointer hover:border-blue-400' : 'bg-gray-100 cursor-not-allowed' }} transition-colors @if($error) border-red-500 ring-2 ring-red-200 @endif">
                <div class="flex flex-col gap-0.5 overflow-hidden">
                    <span class="font-medium truncate">{{ $selectedLabel }}</span>
                    @if(isset($selectedSubtitle))
                        <span class="text-xs text-gray-500 truncate">{{ $selectedSubtitle }}</span>
                    @endif
                </div>
                @if(!$disabled)
                    <span class="text-gray-400 text-xl hover:text-gray-600">⟲</span>
                @endif
            </div>
        @else
            {{-- Search/Input State --}}
            <div class="relative" @click.stop>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="{{ $searchModel }}"
                    @click="open = true"
                    @focus="open = true"
                    @keydown.escape="open = false"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @if($error) border-red-500 ring-2 ring-red-200 @endif {{ $disabled ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                    placeholder="{{ $placeholder }}"
                    {{ $disabled ? 'disabled' : '' }}
                >
                
                {{-- Dropdown --}}
                <div
                    x-show="open"
                    x-transition
                    class="absolute z-50 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg"
                >
                    <ul>
                        @forelse ($results as $result)
                            <li
                                wire:click="{{ $onSelect }}({{ $result['id'] ?? $result->id }})"
                                @click="open = false"
                                class="px-4 py-3 cursor-pointer hover:bg-blue-50 border-b border-gray-100 last:border-0 transition-colors"
                            >
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $result['label'] ?? ($result['nombre'] ?? $result->nombre) }}</span>
                                    @if(isset($result['sublabel']) || isset($result['subtitle']))
                                        <span class="text-xs text-gray-500">{{ $result['sublabel'] ?? $result['subtitle'] }}</span>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="px-4 py-3 text-sm text-gray-500 text-center">
                                No se encontraron resultados
                            </li>
                        @endforelse
                    </ul>
                    
                    {{-- Optional Footer Slot (e.g. "Create New") --}}
                    @if(isset($footer))
                        <div class="border-t border-gray-200 bg-gray-50">
                            {{ $footer }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if($error)
        <p class="text-red-500 text-xs mt-2 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>

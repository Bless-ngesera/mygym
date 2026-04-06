@props(['id', 'title', 'maxWidth' => 'md'])

@php
    $maxWidths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];

    $modalMaxWidth = $maxWidths[$maxWidth] ?? $maxWidths['md'];
@endphp

<div id="{{ $id }}"
     {{ $attributes->merge(['class' => 'fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm']) }}
     x-data="{ open: false }"
     x-show="open"
     x-on:open-{{ $id }}.window="open = true"
     x-on:close-{{ $id }}.window="open = false"
     x-transition.opacity.duration.300ms>

    <div class="bg-white dark:bg-gray-800 rounded-2xl {{ $modalMaxWidth }} w-full p-6 shadow-2xl transform transition-all"
         x-on:click.stop
         x-transition.scale.origin.center.duration.300ms>

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
            <button type="button"
                    x-on:click="open = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{ $slot }}
    </div>
</div>

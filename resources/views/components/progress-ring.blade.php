@props(['percentage' => 0, 'size' => 120, 'color' => 'purple'])

@php
    $radius = ($size - 20) / 2;
    $circumference = 2 * pi() * $radius;
    $offset = $circumference - ($percentage / 100) * $circumference;

    $colors = [
        'purple' => 'stroke-purple-600 dark:stroke-purple-400',
        'green' => 'stroke-green-600 dark:stroke-green-400',
        'blue' => 'stroke-blue-600 dark:stroke-blue-400',
        'orange' => 'stroke-orange-600 dark:stroke-orange-400',
        'pink' => 'stroke-pink-600 dark:stroke-pink-400',
    ];

    $strokeColor = $colors[$color] ?? $colors['purple'];
@endphp

<div class="relative inline-flex items-center justify-center">
    <svg width="{{ $size }}" height="{{ $size }}" class="transform -rotate-90">
        <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $radius }}"
                fill="none" stroke="#e5e7eb" stroke-width="8"/>
        <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $radius }}"
                fill="none" stroke="currentColor" stroke-width="8"
                stroke-dasharray="{{ $circumference }}"
                stroke-dashoffset="{{ $offset }}"
                class="{{ $strokeColor }} transition-all duration-500 ease-out"/>
    </svg>
    <div class="absolute inset-0 flex items-center justify-center">
        <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ $percentage }}%</span>
    </div>
</div>

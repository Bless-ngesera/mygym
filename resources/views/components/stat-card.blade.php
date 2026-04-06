@props(['title', 'value', 'icon', 'color' => 'purple', 'trend' => null])

@php
    $colors = [
        'purple' => 'from-purple-500 to-indigo-600',
        'green' => 'from-green-500 to-emerald-600',
        'blue' => 'from-blue-500 to-cyan-600',
        'orange' => 'from-orange-500 to-red-600',
        'pink' => 'from-pink-500 to-rose-600',
        'yellow' => 'from-yellow-500 to-amber-600',
    ];

    $gradient = $colors[$color] ?? $colors['purple'];
@endphp

<div class="bg-gradient-to-br {{ $gradient }} rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-white/80 text-sm font-medium">{{ $title }}</p>
            <p class="text-3xl font-bold mt-2">{{ $value }}</p>
            @if($trend)
                <p class="text-xs text-white/70 mt-2 flex items-center gap-1">
                    @if($trend > 0)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                        <span>+{{ $trend }}% from last month</span>
                    @elseif($trend < 0)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                        <span>{{ $trend }}% from last month</span>
                    @endif
                </p>
            @endif
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
            {!! $icon !!}
        </div>
    </div>
</div>

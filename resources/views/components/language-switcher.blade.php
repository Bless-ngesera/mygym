@php
    $currentLocale = current_locale();
    $supportedLocales = get_supported_locales();
    $flagIcons = [
        'en' => '🇬🇧',
        'es' => '🇪🇸',
        'fr' => '🇫🇷',
        'de' => '🇩🇪',
        'it' => '🇮🇹',
        'pt' => '🇵🇹',
        'sw' => '🇹🇿',
        'ar' => '🇸🇦',
    ];
@endphp

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" @click.away="open = false"
        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
        <span class="text-lg">{{ $flagIcons[$currentLocale] ?? '🌐' }}</span>
        <span>{{ strtoupper($currentLocale) }}</span>
        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" x-cloak class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
        <div class="py-1">
            @foreach($supportedLocales as $code => $name)
                <a href="{{ route('locale.switch', $code) }}"
                    class="flex items-center gap-3 px-4 py-2 text-sm {{ $currentLocale === $code ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span class="text-lg">{{ $flagIcons[$code] ?? '🌐' }}</span>
                    <span>{{ $name }}</span>
                    @if($currentLocale === $code)
                        <svg class="w-4 h-4 ml-auto text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

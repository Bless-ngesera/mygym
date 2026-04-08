<?php

if (!function_exists('current_locale')) {
    /**
     * Get current locale
     *
     * @return string
     */
    function current_locale()
    {
        return app()->getLocale();
    }
}

if (!function_exists('locale_direction')) {
    /**
     * Get text direction for current locale
     *
     * @return string
     */
    function locale_direction()
    {
        $rtlLocales = ['ar', 'he', 'fa', 'ur'];
        return in_array(app()->getLocale(), $rtlLocales) ? 'rtl' : 'ltr';
    }
}

if (!function_exists('is_rtl')) {
    /**
     * Check if current locale is RTL
     *
     * @return bool
     */
    function is_rtl()
    {
        return locale_direction() === 'rtl';
    }
}

if (!function_exists('get_supported_locales')) {
    /**
     * Get all supported locales
     *
     * @return array
     */
    function get_supported_locales()
    {
        return \App\Http\Middleware\SetLocale::getSupportedLocales();
    }
}

if (!function_exists('locale_switcher')) {
    /**
     * Generate locale switcher HTML
     *
     * @return string
     */
    function locale_switcher()
    {
        $current = current_locale();
        $locales = get_supported_locales();

        $html = '<div class="relative inline-block text-left">';
        $html .= '<button type="button" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" id="locale-menu-button">';
        $html .= '<span>' . strtoupper($current) . '</span>';
        $html .= '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
        $html .= '</button>';
        $html .= '<div class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden" id="locale-menu">';
        $html .= '<div class="py-1">';

        foreach ($locales as $code => $name) {
            $active = $code === $current ? 'bg-gray-100 text-gray-900' : 'text-gray-700';
            $html .= '<a href="?locale=' . $code . '" class="block px-4 py-2 text-sm ' . $active . ' hover:bg-gray-100">';
            $html .= strtoupper($code) . ' - ' . $name;
            $html .= '</a>';
        }

        $html .= '</div></div></div>';

        return $html;
    }
}

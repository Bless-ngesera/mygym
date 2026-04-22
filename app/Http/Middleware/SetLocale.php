<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales - Now only English is supported
     */
    protected $supportedLocales = [
        'en' => 'English',
    ];

    /**
     * Default locale
     */
    protected $defaultLocale = 'en';

    /**
     * Cookie name for locale
     */
    protected $cookieName = 'user_locale';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Force English locale only
            $locale = 'en';

            // Set the application locale to English
            App::setLocale($locale);

            // Store locale in session for persistence
            Session::put('locale', $locale);

            // Also store in cookie for 1 year (365 days)
            Cookie::queue($this->cookieName, $locale, 60 * 24 * 365);

            // Set locale for Carbon dates to English
            if (class_exists('\Carbon\Carbon')) {
                \Carbon\Carbon::setLocale('en');
            }

            // Set locale for validation messages to English
            if (function_exists('trans')) {
                app('translator')->setLocale($locale);
            }

        } catch (\Exception $e) {
            // Log error but don't break the application
            Log::warning('Failed to set locale: ' . $e->getMessage());
            App::setLocale($this->defaultLocale);
        }

        return $next($request);
    }

    /**
     * Get locale from various sources in priority order - Now always returns 'en'
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getLocaleFromRequest(Request $request): string
    {
        // Force English only - ignore all other sources
        return 'en';
    }

    /**
     * Check if locale is supported - Only English is valid now
     *
     * @param  string  $locale
     * @return bool
     */
    protected function isValidLocale(?string $locale): bool
    {
        // Only English is valid
        return $locale === 'en';
    }

    /**
     * Get locale from browser's Accept-Language header - Ignored, always English
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function getBrowserLocale(Request $request): ?string
    {
        // Force English only
        return 'en';
    }

    /**
     * Get all supported locales
     *
     * @return array
     */
    public static function getSupportedLocales(): array
    {
        return (new static())->supportedLocales;
    }

    /**
     * Get the default locale
     *
     * @return string
     */
    public static function getDefaultLocale(): string
    {
        return 'en';
    }

    /**
     * Switch locale for the user - Now always English
     *
     * @param  string  $locale
     * @param  \Illuminate\Http\Request|null  $request
     * @return bool
     */
    public static function switchLocale(string $locale, ?Request $request = null): bool
    {
        // Force English only, ignore any switch attempts
        return false;
    }
}

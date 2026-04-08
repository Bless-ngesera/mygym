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
     * Supported locales
     */
    protected $supportedLocales = [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Français',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'pt' => 'Português',
        'sw' => 'Kiswahili',
        'ar' => 'العربية',
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
            $locale = $this->getLocaleFromRequest($request);

            // Set the application locale
            App::setLocale($locale);

            // Store locale in session for persistence
            Session::put('locale', $locale);

            // Also store in cookie for 1 year (365 days)
            Cookie::queue($this->cookieName, $locale, 60 * 24 * 365);

            // Set locale for Carbon dates
            if (class_exists('\Carbon\Carbon')) {
                \Carbon\Carbon::setLocale($locale);
            }

            // Set locale for validation messages
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
     * Get locale from various sources in priority order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getLocaleFromRequest(Request $request): string
    {
        // Priority 1: Locale from request parameter (URL)
        if ($request->has('locale')) {
            $locale = $request->get('locale');
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        // Priority 2: Locale from session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        // Priority 3: Locale from cookie
        if ($request->hasCookie($this->cookieName)) {
            $locale = $request->cookie($this->cookieName);
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        // Priority 4: Locale from authenticated user
        if ($request->user() && $request->user()->language) {
            $locale = $request->user()->language;
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        // Priority 5: Locale from browser Accept-Language header
        $browserLocale = $this->getBrowserLocale($request);
        if ($browserLocale && $this->isValidLocale($browserLocale)) {
            return $browserLocale;
        }

        // Priority 6: Application default locale
        return $this->defaultLocale;
    }

    /**
     * Check if locale is supported
     *
     * @param  string  $locale
     * @return bool
     */
    protected function isValidLocale(?string $locale): bool
    {
        if (!$locale) {
            return false;
        }

        // Check exact match
        if (array_key_exists($locale, $this->supportedLocales)) {
            return true;
        }

        // Check for locale variants (e.g., en-US -> en)
        $baseLocale = substr($locale, 0, 2);
        if (array_key_exists($baseLocale, $this->supportedLocales)) {
            return true;
        }

        return false;
    }

    /**
     * Get locale from browser's Accept-Language header
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function getBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $locales = explode(',', $acceptLanguage);

        foreach ($locales as $locale) {
            // Extract language code (e.g., "en-US" -> "en")
            $code = substr(trim($locale), 0, 2);

            if ($this->isValidLocale($code)) {
                return $code;
            }
        }

        return null;
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
        return (new static())->defaultLocale;
    }

    /**
     * Switch locale for the user (helper method)
     *
     * @param  string  $locale
     * @param  \Illuminate\Http\Request|null  $request
     * @return bool
     */
    public static function switchLocale(string $locale, ?Request $request = null): bool
    {
        $middleware = new static();

        if (!$middleware->isValidLocale($locale)) {
            return false;
        }

        Session::put('locale', $locale);
        Cookie::queue($middleware->cookieName, $locale, 60 * 24 * 365);

        // If user is authenticated, update their preference
        if ($request && $request->user()) {
            try {
                $request->user()->update(['language' => $locale]);
            } catch (\Exception $e) {
                Log::warning('Failed to update user language preference: ' . $e->getMessage());
            }
        }

        return true;
    }
}

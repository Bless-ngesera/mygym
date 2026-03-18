<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Display all settings (Admin view)
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        // Set default values for missing settings
        $defaults = [
            'site_name' => config('app.name'),
            'site_email' => '',
            'site_phone' => '',
            'site_address' => '',
            'currency' => 'USD',
            'tax_rate' => '0',
            'booking_lead_time' => '24',
            'max_booking_per_user' => '5',
            'enable_registration' => '1',
            'maintenance_mode' => '0',
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            'about_us' => '',
            'terms_conditions' => '',
            'privacy_policy' => '',
            'site_logo' => '',
            'site_favicon' => '',
        ];

        $settings = array_merge($defaults, $settings);

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Display user settings (for regular users)
     */
    public function userIndex()
    {
        /** @var User $user */
        $user = Auth::user();

        $settings = [
            'notification_email' => $user->notification_email ?? true,
            'email_frequency' => $user->email_frequency ?? 'daily',
            'language' => $user->language ?? 'en',
            'timezone' => $user->timezone ?? 'UTC',
            'theme' => $user->theme ?? 'system',
        ];

        return view('user.settings.index', compact('settings'));
    }

    /**
     * Update user settings
     */
    public function userUpdate(Request $request)
    {
        $validated = $request->validate([
            'notification_email' => 'boolean',
            'email_frequency' => 'in:instant,daily,weekly,never',
            'language' => 'in:en,es,fr,de',
            'timezone' => 'string|timezone',
            'theme' => 'in:light,dark,system',
        ]);

        try {
            /** @var User $user */
            $user = Auth::user();
            $user->update($validated);

            return redirect()->route('user.settings.index')
                ->with('success', 'Your settings have been updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Unable to update settings: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update admin settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_email' => 'nullable|email|max:255',
            'site_phone' => 'nullable|string|max:50',
            'site_address' => 'nullable|string|max:500',
            'currency' => 'nullable|string|max:10',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'booking_lead_time' => 'nullable|integer|min:0|max:365',
            'max_booking_per_user' => 'nullable|integer|min:1|max:100',
            'enable_registration' => 'nullable|boolean',
            'maintenance_mode' => 'nullable|boolean',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'about_us' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
        ]);

        try {
            foreach ($validated as $key => $value) {
                if ($value !== null) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value]
                    );
                }
            }

            // Clear all settings cache
            $this->clearSettingsCache();

            return redirect()->route('admin.settings.index')
                ->with('success', 'Settings updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Unable to update settings: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update logo
     */
    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $path = $request->file('logo')->store('settings', 'public');

            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => $path]
            );

            Cache::forget('setting_site_logo');

            return redirect()->route('admin.settings.index')
                ->with('success', 'Logo updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Unable to update logo: ' . $e->getMessage()]);
        }
    }

    /**
     * Update favicon
     */
    public function updateFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|mimes:ico,png|max:1024',
        ]);

        try {
            $path = $request->file('favicon')->store('settings', 'public');

            Setting::updateOrCreate(
                ['key' => 'site_favicon'],
                ['value' => $path]
            );

            Cache::forget('setting_site_favicon');

            return redirect()->route('admin.settings.index')
                ->with('success', 'Favicon updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Unable to update favicon: ' . $e->getMessage()]);
        }
    }

    /**
     * Reset settings to default
     */
    public function reset()
    {
        try {
            $defaults = [
                'site_name' => config('app.name'),
                'site_email' => '',
                'site_phone' => '',
                'site_address' => '',
                'currency' => 'USD',
                'tax_rate' => '0',
                'booking_lead_time' => '24',
                'max_booking_per_user' => '5',
                'enable_registration' => '1',
                'maintenance_mode' => '0',
                'facebook_url' => '',
                'twitter_url' => '',
                'instagram_url' => '',
                'youtube_url' => '',
                'about_us' => '',
                'terms_conditions' => '',
                'privacy_policy' => '',
            ];

            foreach ($defaults as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            $this->clearSettingsCache();

            return redirect()->route('admin.settings.index')
                ->with('success', 'Settings reset to default values.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Unable to reset settings: ' . $e->getMessage()]);
        }
    }

    /**
     * Clear all settings cache
     */
    public function clearCache()
    {
        $this->clearSettingsCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings cache cleared successfully.');
    }

    /**
     * Get a specific setting value
     */
    public static function get($key, $default = null)
    {
        return Cache::remember('setting_' . $key, 3600, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Get multiple settings at once
     */
    public static function getMany(array $keys)
    {
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $settings[$key] ?? null;
        }

        return $result;
    }

    /**
     * Get all settings as an array
     */
    public static function getAll()
    {
        return Cache::remember('site_settings', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear all settings cache
     */
    private function clearSettingsCache()
    {
        Cache::forget('site_settings');

        // Clear individual setting caches
        $settings = Setting::pluck('key')->toArray();
        foreach ($settings as $key) {
            Cache::forget('setting_' . $key);
        }
    }

    /**
     * Check if maintenance mode is enabled
     */
    public static function isMaintenanceMode()
    {
        return self::get('maintenance_mode', false) === '1';
    }

    /**
     * Check if registration is enabled
     */
    public static function isRegistrationEnabled()
    {
        return self::get('enable_registration', true) === '1';
    }

    /**
     * Get site logo URL
     */
    public static function getLogoUrl()
    {
        $logo = self::get('site_logo');
        return $logo ? asset('storage/' . $logo) : null;
    }

    /**
     * Get favicon URL
     */
    public static function getFaviconUrl()
    {
        $favicon = self::get('site_favicon');
        return $favicon ? asset('storage/' . $favicon) : null;
    }
}

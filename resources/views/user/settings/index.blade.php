cat > resources/views/user/settings/index.blade.php << 'EOF'
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Settings</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form action="{{ route('user.settings.update') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label for="notification_email" class="flex items-center">
                            <input type="checkbox" name="notification_email" value="1" {{ $settings['notification_email'] ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Receive Email Notifications</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label for="email_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Frequency</label>
                        <select name="email_frequency" id="email_frequency"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="instant" {{ $settings['email_frequency'] == 'instant' ? 'selected' : '' }}>Instant</option>
                            <option value="daily" {{ $settings['email_frequency'] == 'daily' ? 'selected' : '' }}>Daily Digest</option>
                            <option value="weekly" {{ $settings['email_frequency'] == 'weekly' ? 'selected' : '' }}>Weekly Digest</option>
                            <option value="never" {{ $settings['email_frequency'] == 'never' ? 'selected' : '' }}>Never</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Language</label>
                        <select name="language" id="language"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="en" {{ $settings['language'] == 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ $settings['language'] == 'es' ? 'selected' : '' }}>Spanish</option>
                            <option value="fr" {{ $settings['language'] == 'fr' ? 'selected' : '' }}>French</option>
                            <option value="de" {{ $settings['language'] == 'de' ? 'selected' : '' }}>German</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                        <select name="timezone" id="timezone"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="UTC" {{ $settings['timezone'] == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ $settings['timezone'] == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                            <option value="America/Chicago" {{ $settings['timezone'] == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                            <option value="America/Denver" {{ $settings['timezone'] == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                            <option value="America/Los_Angeles" {{ $settings['timezone'] == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                            <option value="Europe/London" {{ $settings['timezone'] == 'Europe/London' ? 'selected' : '' }}>London</option>
                            <option value="Europe/Paris" {{ $settings['timezone'] == 'Europe/Paris' ? 'selected' : '' }}>Central European</option>
                            <option value="Asia/Tokyo" {{ $settings['timezone'] == 'Asia/Tokyo' ? 'selected' : '' }}>Japan</option>
                            <option value="Australia/Sydney" {{ $settings['timezone'] == 'Australia/Sydney' ? 'selected' : '' }}>Eastern Australia</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Theme</label>
                        <select name="theme" id="theme"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="light" {{ $settings['theme'] == 'light' ? 'selected' : '' }}>Light</option>
                            <option value="dark" {{ $settings['theme'] == 'dark' ? 'selected' : '' }}>Dark</option>
                            <option value="system" {{ $settings['theme'] == 'system' ? 'selected' : '' }}>System Default</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
EOF

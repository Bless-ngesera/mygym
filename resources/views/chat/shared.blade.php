<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shared Chat - MyGym AI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .message-bubble {
            transition: transform 0.2s ease;
        }
        .message-bubble:hover {
            transform: translateX(2px);
        }
        [dir="rtl"] .message-bubble:hover {
            transform: translateX(-2px);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden border border-gray-700 animate-fade-in">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">Shared Conversation</h1>
                        <p class="text-sm text-purple-200">Shared by {{ $shared_by_name }} • {{ $session->created_at->format('M j, Y') }}</p>
                        <p class="text-xs text-purple-300 mt-1">Expires: {{ \Carbon\Carbon::parse($expires_at)->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="p-6 space-y-4 max-h-[600px] overflow-y-auto">
                @foreach($session->messages as $message)
                    <div class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }} message-bubble">
                        <div class="max-w-[80%] {{ $message->role === 'user' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-200' }} rounded-2xl px-4 py-3 shadow-lg">
                            <p class="text-sm leading-relaxed">{{ $message->message }}</p>
                            <p class="text-xs mt-2 opacity-70">{{ $message->created_at->format('g:i A') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-700 text-center">
                <p class="text-xs text-gray-500">Powered by MyGym AI • Shared conversation • Expires in 7 days</p>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      :dir="isRTL ? 'rtl' : 'ltr'"
      x-data="{
          darkMode: localStorage.getItem('theme') === 'dark' ||
                   (localStorage.getItem('theme') === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches),
          isRTL: {{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'true' : 'false' }},
          openLocaleMenu: false
      }"
      x-init="() => {
          // Initialize theme from localStorage or system preference
          const savedTheme = localStorage.getItem('theme') || 'system';

          if (savedTheme === 'system') {
              const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
              document.documentElement.classList.toggle('dark', systemDark);
              darkMode = systemDark;
          } else {
              document.documentElement.classList.toggle('dark', savedTheme === 'dark');
              darkMode = savedTheme === 'dark';
          }

          // Watch for system theme changes
          window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
              const currentTheme = localStorage.getItem('theme') || 'system';
              if (currentTheme === 'system') {
                  document.documentElement.classList.toggle('dark', e.matches);
                  darkMode = e.matches;
              }
          });

          // Watch for darkMode changes
          $watch('darkMode', (value) => {
              const currentTheme = localStorage.getItem('theme') || 'system';
              if (currentTheme === 'system') {
                  return;
              }
              document.documentElement.classList.toggle('dark', value);
          });

          // Watch for locale changes to update RTL
          $watch('isRTL', (value) => {
              document.documentElement.setAttribute('dir', value ? 'rtl' : 'ltr');
          });

          // Close locale menu when clicking outside
          document.addEventListener('click', (e) => {
              if (!e.target.closest('.locale-menu-container')) {
                  openLocaleMenu = false;
              }
          });
      }"
      :class="{ 'dark': darkMode }">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MyGym') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js for theme switching -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }

            /* Smooth theme transitions */
            * {
                transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
            }

            /* RTL Support */
            [dir="rtl"] {
                text-align: right;
            }

            [dir="rtl"] .space-x-4 > :not([hidden]) ~ :not([hidden]) {
                --tw-space-x-reverse: 1;
                margin-right: calc(1rem * var(--tw-space-x-reverse));
                margin-left: calc(1rem * calc(1 - var(--tw-space-x-reverse)));
            }

            [dir="rtl"] .flex-row {
                flex-direction: row-reverse;
            }

            /* Custom scrollbar for chat */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #555;
            }

            .dark ::-webkit-scrollbar-track {
                background: #374151;
            }

            .dark ::-webkit-scrollbar-thumb {
                background: #6b7280;
            }

            .dark ::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }

            /* Chat animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes bounce {
                0%, 60%, 100% {
                    transform: translateY(0);
                }
                30% {
                    transform: translateY(-8px);
                }
            }

            .animate-fade-in {
                animation: fadeIn 0.3s ease-out;
            }

            .animate-bounce {
                animation: bounce 1.2s infinite;
            }

            /* Typing indicator dots */
            .typing-dot {
                animation: bounce 1.2s infinite;
            }

            .typing-dot:nth-child(1) { animation-delay: 0s; }
            .typing-dot:nth-child(2) { animation-delay: 0.2s; }
            .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        </style>
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow transition-colors duration-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- AI Chat Sidebar Component --}}
        @include('components.ai-chat-sidebar')

        @stack('scripts')

        {{-- Complete Chat JavaScript Functions --}}
        <script>
            // Global chat functions
            window.openChat = function() {
                console.log('Opening AI Assistant...');
                const sidebar = document.getElementById('aiChatSidebar');
                const overlay = document.getElementById('chatOverlay');

                if (sidebar && overlay) {
                    sidebar.classList.remove('hidden');
                    overlay.classList.remove('hidden');

                    setTimeout(() => {
                        sidebar.classList.remove('translate-x-full');
                        overlay.classList.remove('opacity-0');
                        overlay.classList.add('opacity-100');
                    }, 10);

                    // Focus input after animation
                    setTimeout(() => {
                        const input = document.getElementById('chatInput');
                        if (input) {
                            input.focus();
                            // Place cursor at end of any existing text
                            const length = input.value.length;
                            input.setSelectionRange(length, length);
                        }
                    }, 300);
                } else {
                    console.error('Chat sidebar elements not found');
                }
            };

            window.closeChat = function() {
                const sidebar = document.getElementById('aiChatSidebar');
                const overlay = document.getElementById('chatOverlay');

                if (sidebar && overlay) {
                    sidebar.classList.add('translate-x-full');
                    overlay.classList.remove('opacity-100');
                    overlay.classList.add('opacity-0');

                    setTimeout(() => {
                        sidebar.classList.add('hidden');
                        overlay.classList.add('hidden');
                    }, 300);
                }
            };

            // Send message function
            window.sendChatMessage = async function() {
                const input = document.getElementById('chatInput');
                const message = input?.value.trim();

                if (!message) return;

                const messagesContainer = document.getElementById('chatMessages');
                if (!messagesContainer) return;

                // Add user message to chat
                const userMessageDiv = document.createElement('div');
                userMessageDiv.className = 'flex justify-end animate-fade-in';
                userMessageDiv.innerHTML = `
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl rounded-tr-none px-4 py-3 max-w-[85%] shadow-lg">
                        <p class="text-sm text-white leading-relaxed">${escapeHtml(message)}</p>
                        <span class="text-xs text-purple-200 mt-1 block">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                    </div>
                `;
                messagesContainer.appendChild(userMessageDiv);

                // Clear input
                input.value = '';
                input.style.height = 'auto';
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

                // Show typing indicator
                const typingDiv = document.createElement('div');
                typingDiv.id = 'typingIndicator';
                typingDiv.className = 'flex justify-start animate-fade-in';
                typingDiv.innerHTML = `
                    <div class="bg-gray-800 rounded-2xl rounded-tl-none px-4 py-3 border border-purple-500/20">
                        <div class="flex gap-1.5">
                            <div class="w-2 h-2 bg-purple-400 rounded-full typing-dot"></div>
                            <div class="w-2 h-2 bg-purple-400 rounded-full typing-dot"></div>
                            <div class="w-2 h-2 bg-purple-400 rounded-full typing-dot"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">AI is thinking...</p>
                    </div>
                `;
                messagesContainer.appendChild(typingDiv);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    const response = await fetch('/chat/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: message })
                    });

                    const data = await response.json();

                    // Remove typing indicator
                    document.getElementById('typingIndicator')?.remove();

                    // Add AI response
                    const aiMessageDiv = document.createElement('div');
                    aiMessageDiv.className = 'flex justify-start animate-fade-in';
                    aiMessageDiv.innerHTML = `
                        <div class="bg-gray-800 rounded-2xl rounded-tl-none px-4 py-3 max-w-[85%] border border-purple-500/20 shadow-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-5 h-5 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-purple-400">MyGym AI</span>
                                <span class="text-xs text-gray-500">• Groq</span>
                            </div>
                            <p class="text-sm text-gray-200 leading-relaxed">${formatMessage(escapeHtml(data.message || 'Sorry, I encountered an error. Please try again.'))}</p>
                            <span class="text-xs text-gray-500 mt-2 block">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                        </div>
                    `;
                    messagesContainer.appendChild(aiMessageDiv);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;

                } catch (error) {
                    console.error('Chat Error:', error);
                    document.getElementById('typingIndicator')?.remove();

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'flex justify-start animate-fade-in';
                    errorDiv.innerHTML = `
                        <div class="bg-red-900/50 rounded-2xl rounded-tl-none px-4 py-3 max-w-[85%] border border-red-500/20">
                            <p class="text-sm text-red-200">⚠️ Sorry, I'm having trouble connecting. Please check your internet connection and try again.</p>
                            <span class="text-xs text-red-300 mt-2 block">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                        </div>
                    `;
                    messagesContainer.appendChild(errorDiv);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            };

            // Use suggestion function
            window.useSuggestion = function(suggestion) {
                const input = document.getElementById('chatInput');
                if (input) {
                    input.value = suggestion;
                    input.focus();
                    // Auto-resize textarea
                    input.style.height = 'auto';
                    input.style.height = Math.min(input.scrollHeight, 120) + 'px';
                    sendChatMessage();
                }
            };

            // Helper functions
            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function formatMessage(message) {
                if (!message) return '';
                let formatted = message;
                // Bold text with ** **
                formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong class="text-purple-300">$1</strong>');
                // Italic text with * *
                formatted = formatted.replace(/\*(.*?)\*/g, '<em class="text-gray-300">$1</em>');
                // Line breaks
                formatted = formatted.replace(/\n/g, '<br>');
                // Bullet points
                formatted = formatted.replace(/• (.*?)(<br>|$)/g, '<li class="ml-4 list-disc">$1</li>');
                return formatted;
            }

            // Auto-resize textarea
            function autoResizeTextarea() {
                const textarea = document.getElementById('chatInput');
                if (textarea) {
                    textarea.addEventListener('input', function() {
                        this.style.height = 'auto';
                        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                    });
                }
            }

            // Enter key to send
            document.addEventListener('DOMContentLoaded', function() {
                const chatInput = document.getElementById('chatInput');
                if (chatInput) {
                    chatInput.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            sendChatMessage();
                        }
                    });
                    autoResizeTextarea();
                }

                // Close chat with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        const sidebar = document.getElementById('aiChatSidebar');
                        if (sidebar && !sidebar.classList.contains('hidden')) {
                            closeChat();
                        }
                    }
                });

                // Close chat when clicking overlay
                const overlay = document.getElementById('chatOverlay');
                if (overlay) {
                    overlay.addEventListener('click', closeChat);
                }
            });

            // Prevent body scroll when chat is open
            function toggleBodyScroll(disable) {
                if (disable) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }

            // Watch for chat open/close
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        const sidebar = document.getElementById('aiChatSidebar');
                        if (sidebar) {
                            const isHidden = sidebar.classList.contains('hidden');
                            toggleBodyScroll(!isHidden);
                        }
                    }
                });
            });

            // Start observing when DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('aiChatSidebar');
                if (sidebar) {
                    observer.observe(sidebar, { attributes: true });
                }
            });
        </script>
    </body>
</html>

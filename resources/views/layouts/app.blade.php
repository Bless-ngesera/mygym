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
          const savedTheme = localStorage.getItem('theme') || 'system';

          if (savedTheme === 'system') {
              const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
              document.documentElement.classList.toggle('dark', systemDark);
              darkMode = systemDark;
          } else {
              document.documentElement.classList.toggle('dark', savedTheme === 'dark');
              darkMode = savedTheme === 'dark';
          }

          window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
              const currentTheme = localStorage.getItem('theme') || 'system';
              if (currentTheme === 'system') {
                  document.documentElement.classList.toggle('dark', e.matches);
                  darkMode = e.matches;
              }
          });

          $watch('darkMode', (value) => {
              const currentTheme = localStorage.getItem('theme') || 'system';
              if (currentTheme === 'system') return;
              document.documentElement.classList.toggle('dark', value);
          });

          $watch('isRTL', (value) => {
              document.documentElement.setAttribute('dir', value ? 'rtl' : 'ltr');
          });

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

            /* Custom scrollbar */
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

            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }

            .animate-fade-in {
                animation: fadeIn 0.3s ease-out;
            }

            .animate-bounce {
                animation: bounce 1.2s infinite;
            }

            .animate-pulse {
                animation: pulse 2s infinite;
            }

            /* Typing indicator dots */
            .typing-dot {
                animation: bounce 1.2s infinite;
            }

            .typing-dot:nth-child(1) { animation-delay: 0s; }
            .typing-dot:nth-child(2) { animation-delay: 0.2s; }
            .typing-dot:nth-child(3) { animation-delay: 0.4s; }

            /* Line clamp for notifications */
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
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

                    setTimeout(() => {
                        const input = document.getElementById('chatInput');
                        if (input) {
                            input.focus();
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

                const userMessageDiv = document.createElement('div');
                userMessageDiv.className = 'flex justify-end animate-fade-in';
                userMessageDiv.innerHTML = `
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl rounded-tr-none px-4 py-3 max-w-[85%] shadow-lg">
                        <p class="text-sm text-white leading-relaxed">${escapeHtml(message)}</p>
                        <span class="text-xs text-purple-200 mt-1 block">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                    </div>
                `;
                messagesContainer.appendChild(userMessageDiv);

                input.value = '';
                input.style.height = 'auto';
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

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

                    document.getElementById('typingIndicator')?.remove();

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

            window.useSuggestion = function(suggestion) {
                const input = document.getElementById('chatInput');
                if (input) {
                    input.value = suggestion;
                    input.focus();
                    input.style.height = 'auto';
                    input.style.height = Math.min(input.scrollHeight, 120) + 'px';
                    sendChatMessage();
                }
            };

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function formatMessage(message) {
                if (!message) return '';
                let formatted = message;
                formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong class="text-purple-300">$1</strong>');
                formatted = formatted.replace(/\*(.*?)\*/g, '<em class="text-gray-300">$1</em>');
                formatted = formatted.replace(/\n/g, '<br>');
                formatted = formatted.replace(/• (.*?)(<br>|$)/g, '<li class="ml-4 list-disc">$1</li>');
                return formatted;
            }

            function autoResizeTextarea() {
                const textarea = document.getElementById('chatInput');
                if (textarea) {
                    textarea.addEventListener('input', function() {
                        this.style.height = 'auto';
                        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                    });
                }
            }

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

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        const sidebar = document.getElementById('aiChatSidebar');
                        if (sidebar && !sidebar.classList.contains('hidden')) {
                            closeChat();
                        }
                    }
                });

                const overlay = document.getElementById('chatOverlay');
                if (overlay) {
                    overlay.addEventListener('click', closeChat);
                }
            });

            function toggleBodyScroll(disable) {
                if (disable) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }

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

            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('aiChatSidebar');
                if (sidebar) {
                    observer.observe(sidebar, { attributes: true });
                }
            });

            // ==================== NOTIFICATION BELL FUNCTIONS ====================

            // Global notification functions
            window.fetchNotifications = async function() {
                try {
                    const response = await fetch('/notifications/recent', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch');

                    const data = await response.json();
                    if (data.success) {
                        updateNotificationUI(data);
                    }
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                }
            };

            window.markAsRead = async function(notificationId) {
                try {
                    const response = await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        fetchNotifications();
                    }
                } catch (error) {
                    console.error('Error marking as read:', error);
                }
            };

            window.markAllAsRead = async function() {
                try {
                    const response = await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        fetchNotifications();
                        const badge = document.getElementById('notification-badge');
                        if (badge) badge.classList.add('hidden');
                    }
                } catch (error) {
                    console.error('Error marking all as read:', error);
                }
            };

            window.handleNotificationClick = async function(notificationId, url) {
                await markAsRead(notificationId);
                if (url && url !== '#') {
                    window.location.href = url;
                }
            };

            function updateNotificationUI(data) {
                const badge = document.getElementById('notification-badge');
                const markAllBtn = document.getElementById('mark-all-read-btn');
                const notificationsList = document.getElementById('notifications-list');

                if (badge) {
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        badge.classList.remove('hidden');
                        badge.classList.add('animate-pulse');
                        if (markAllBtn) markAllBtn.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                        badge.classList.remove('animate-pulse');
                        if (markAllBtn) markAllBtn.classList.add('hidden');
                    }
                }

                if (notificationsList && data.notifications) {
                    if (data.notifications.length === 0) {
                        notificationsList.innerHTML = `
                            <div class="p-8 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <p class="text-gray-500 text-sm">No new notifications</p>
                            </div>
                        `;
                        return;
                    }

                    let html = '';
                    for (const notification of data.notifications) {
                        html += `
                            <div class="p-4 hover:bg-purple-50/30 transition-colors cursor-pointer ${!notification.read ? 'bg-purple-50/50' : ''}"
                                 onclick="handleNotificationClick(${notification.id}, '${notification.action_url || '#'}')">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <span class="text-2xl">${notification.icon || '🔔'}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="text-sm font-semibold text-gray-900 truncate">${escapeHtml(notification.title)}</p>
                                            ${!notification.read ? `
                                                <button onclick="event.stopPropagation(); markAsRead(${notification.id})"
                                                        class="text-purple-500 hover:text-purple-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            ` : ''}
                                        </div>
                                        <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">${escapeHtml(notification.message)}</p>
                                        <p class="text-xs text-gray-400 mt-1">${notification.created_at || 'Just now'}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    notificationsList.innerHTML = html;
                }
            }

            // Initialize notification polling
            let notificationInterval;

            function startNotificationPolling() {
                if (notificationInterval) clearInterval(notificationInterval);
                fetchNotifications();
                notificationInterval = setInterval(fetchNotifications, 30000);
            }

            function stopNotificationPolling() {
                if (notificationInterval) {
                    clearInterval(notificationInterval);
                    notificationInterval = null;
                }
            }

            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopNotificationPolling();
                } else {
                    startNotificationPolling();
                }
            });

            document.addEventListener('DOMContentLoaded', function() {
                startNotificationPolling();
            });
        </script>
    </body>
</html>

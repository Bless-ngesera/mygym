{{-- PREMIUM AI Chat Sidebar Component --}}
@php
    $user = Auth::user();
    $userRole = $user->role ?? 'member';

    // Role-based greeting
    $greetings = [
        'admin' => ['icon' => '👑', 'message' => 'Welcome back, Administrator', 'color' => 'from-purple-600 to-indigo-600'],
        'instructor' => ['icon' => '🧑‍🏫', 'message' => 'Ready to inspire your class today?', 'color' => 'from-blue-600 to-cyan-600'],
        'member' => ['icon' => '💪', 'message' => 'Ready for an amazing workout?', 'color' => 'from-purple-600 to-blue-600'],
    ];
    $greeting = $greetings[$userRole] ?? $greetings['member'];
@endphp

<div id="aiChatSidebar" class="fixed inset-y-0 right-0 z-50 hidden w-full sm:w-96 md:w-[480px] bg-gradient-to-b from-gray-900/95 via-gray-900/98 to-gray-950/95 backdrop-blur-xl shadow-2xl transform transition-all duration-500 ease-out translate-x-full">
    <div class="flex flex-col h-full">
        <!-- Premium Header with Glassmorphism -->
        <div class="relative overflow-hidden bg-gradient-to-r {{ $greeting['color'] }}/90 backdrop-blur-sm">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/20 rounded-full blur-2xl translate-y-1/2 -translate-x-1/3"></div>
            <div class="relative flex items-center justify-between p-5">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white animate-pulse shadow-lg"></div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight">MyGym AI</h3>
                        <p class="text-xs text-white/70 flex items-center gap-1">
                            <span>Powered by Groq</span>
                            <span class="w-1 h-1 bg-white/50 rounded-full"></span>
                            <span>Ultra-Fast</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="toggleChatHistory()" class="p-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/15 transition-all duration-200 backdrop-blur-sm" title="Chat History">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </button>
                    <button onclick="startNewChat()" class="p-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/15 transition-all duration-200 backdrop-blur-sm" title="New Chat">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                    <button onclick="closeChat()" class="p-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/15 transition-all duration-200 backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Contextual Greeting -->
        <div class="px-5 pt-4 pb-2 border-b border-gray-800/50 bg-gray-900/30">
            <div class="flex items-center gap-3 text-sm">
                <span class="text-2xl">{{ $greeting['icon'] }}</span>
                <div class="flex-1">
                    <p class="font-semibold text-white">{{ $greeting['message'] }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst($userRole) }} • {{ $user->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500" id="messageCount">0 messages</p>
                    <button onclick="exportChatHistory()" class="text-xs text-purple-400 hover:text-purple-300 transition mt-1 flex items-center gap-1 ml-auto">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Suggestions Section -->
        <div id="quickSuggestionsSection" class="px-5 py-3 border-b border-gray-800/50 bg-gray-900/20 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Quick Suggestions
                </p>
                <div class="flex items-center gap-2">
                    <button onclick="refreshSuggestions()" class="text-xs text-purple-400 hover:text-purple-300 transition flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                    <button id="toggleSuggestionsBtn" onclick="toggleSuggestions()" class="text-xs text-gray-500 hover:text-gray-400 transition p-1" title="Hide suggestions">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="quickSuggestions" class="flex flex-wrap gap-2 max-h-32 overflow-y-auto transition-all duration-300">
                <div class="animate-pulse flex gap-2">
                    <div class="h-8 w-20 bg-gray-700 rounded-full"></div>
                    <div class="h-8 w-24 bg-gray-700 rounded-full"></div>
                    <div class="h-8 w-28 bg-gray-700 rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4" id="chatMessages" style="scroll-behavior: smooth;">
            <!-- Welcome Message -->
            <div class="flex justify-start animate-fade-in welcome-message" id="welcomeMessage">
                <div class="bg-gradient-to-br from-purple-600/20 to-blue-600/20 backdrop-blur-sm border border-purple-500/30 rounded-2xl rounded-tl-none px-5 py-4 max-w-[90%] shadow-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-purple-400">MyGym AI</span>
                        <span class="text-xs text-gray-500">Online</span>
                    </div>
                    <p class="text-sm text-gray-200 leading-relaxed">👋 Hello! I'm your intelligent fitness assistant. I can help with workouts, nutrition, class bookings, and motivation. What brings you here today?</p>
                    <span class="text-xs text-gray-500 mt-2 block">{{ now()->format('g:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Premium Input Area -->
        <div class="border-t border-gray-800 bg-gradient-to-t from-gray-900 to-gray-950 p-4">
            <div class="relative">
                <textarea id="chatInput" rows="1" class="w-full px-5 py-4 bg-gray-800/50 backdrop-blur border border-gray-700 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400 resize-none transition-all duration-200 pr-28" placeholder="Ask me anything..." style="max-height: 120px;"></textarea>
                <div class="absolute right-2 bottom-2 flex items-center gap-2">
                    <span id="charCount" class="text-xs text-gray-500">0</span>
                    <button onclick="sendMessage()" id="sendButton" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl hover:opacity-90 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span class="hidden sm:inline text-sm">Send</span>
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between mt-2 px-1">
                <p class="text-xs text-gray-500">↵ Enter to send • Shift+Enter for new line</p>
                <button onclick="clearInput()" class="text-xs text-gray-500 hover:text-gray-400 transition">Clear</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Delete Confirmation Modal -->
<div id="deleteConfirmationModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/80 backdrop-blur-md transition-all duration-300">
    <div class="bg-gradient-to-b from-gray-800 to-gray-900 rounded-2xl w-96 max-w-[90%] shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="deleteModalContent">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-white text-center mb-2">Delete chat?</h3>
            <p class="text-gray-400 text-center text-sm mb-6" id="deleteModalMessage">This chat can't be recovered. Share links from it will be disabled.</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-xl transition-all duration-200 font-medium">
                    Cancel
                </button>
                <button onclick="confirmDeleteSession()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all duration-200 font-medium">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Rename Chat Modal -->
<div id="renameChatModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/80 backdrop-blur-md transition-all duration-300">
    <div class="bg-gradient-to-b from-gray-800 to-gray-900 rounded-2xl w-96 max-w-[90%] shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="renameModalContent">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-white">Rename Chat</h3>
                <button onclick="closeRenameModal()" class="text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <input type="text" id="renameChatInput" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Enter new name...">
            <div class="flex gap-3 mt-6">
                <button onclick="closeRenameModal()" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-xl transition-all duration-200">
                    Cancel
                </button>
                <button onclick="confirmRenameSession()" class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition-all duration-200">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Share Chat Modal -->
<div id="shareChatModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/80 backdrop-blur-md transition-all duration-300">
    <div class="bg-gradient-to-b from-gray-800 to-gray-900 rounded-2xl w-96 max-w-[90%] shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="shareModalContent">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-white">Share Chat</h3>
                <button onclick="closeShareModal()" class="text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="bg-gray-700/50 rounded-xl p-3 mb-4">
                <p class="text-gray-300 text-sm break-all" id="shareChatLink"></p>
            </div>
            <button onclick="copyShareLink()" class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                </svg>
                Copy Link
            </button>
        </div>
    </div>
</div>

<!-- Chat History Modal -->
<div id="chatHistoryModal" class="fixed top-20 right-24 z-50 hidden w-80 bg-gray-900/98 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-800 transform transition-all duration-300 scale-95 opacity-0">
    <div class="flex flex-col max-h-96">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center bg-gray-900/50">
            <h4 class="font-semibold text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Chat History
            </h4>
            <button onclick="closeHistoryModal()" class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="chatHistoryList" class="flex-1 overflow-y-auto p-2 space-y-1 max-h-80">
            <div class="text-center text-gray-500 py-8 text-sm">Loading history...</div>
        </div>
        <div class="p-3 border-t border-gray-800 bg-gray-900/50">
            <button onclick="clearAllHistory()" class="w-full px-3 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-xl text-xs transition flex items-center justify-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Clear All History
            </button>
        </div>
    </div>
</div>

<!-- Premium Overlay -->
<div id="chatOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden transition-all duration-300"></div>

<style>
    /* Previous styles remain the same, add these new ones */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes typingWave {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
        30% { transform: translateY(-8px); opacity: 1; }
    }

    .animate-fade-in { animation: fadeIn 0.3s ease-out; }
    .animate-slide-in { animation: slideIn 0.3s ease-out; }
    .animate-slide-in-right { animation: slideInRight 0.3s ease-out; }

    .typing-dot {
        animation: typingWave 1.2s infinite;
    }

    /* Premium Scrollbar */
    #chatMessages::-webkit-scrollbar,
    #chatHistoryList::-webkit-scrollbar,
    #quickSuggestions::-webkit-scrollbar {
        width: 4px;
    }

    #chatMessages::-webkit-scrollbar-track,
    #chatHistoryList::-webkit-scrollbar-track,
    #quickSuggestions::-webkit-scrollbar-track {
        background: rgba(31, 41, 55, 0.5);
        border-radius: 10px;
    }

    #chatMessages::-webkit-scrollbar-thumb,
    #chatHistoryList::-webkit-scrollbar-thumb,
    #quickSuggestions::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #8b5cf6, #3b82f6);
        border-radius: 10px;
    }

    /* Message bubble hover effects */
    .message-bubble {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .message-bubble:hover {
        transform: translateX(2px);
    }

    /* Glassmorphism effects */
    .glass-effect {
        background: rgba(17, 24, 39, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(139, 92, 246, 0.2);
    }

    /* Focus styles */
    #chatInput:focus {
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
    }

    /* History item hover */
    .history-item {
        transition: all 0.2s ease;
        position: relative;
    }

    .history-item:hover {
        background: rgba(139, 92, 246, 0.1);
        transform: translateX(4px);
    }

    .history-item:hover .history-actions {
        display: flex;
    }

    .history-actions {
        display: none;
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(31, 41, 55, 0.9);
        backdrop-filter: blur(8px);
        border-radius: 12px;
        padding: 4px;
        gap: 4px;
        z-index: 10;
    }

    .history-action-btn {
        padding: 6px;
        border-radius: 8px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .history-action-btn:hover {
        background: rgba(139, 92, 246, 0.3);
        transform: scale(1.05);
    }

    /* Suggestions collapsed state */
    .suggestions-collapsed {
        max-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        opacity: 0;
        overflow: hidden;
    }

    /* Copy button animation */
    .copy-btn {
        transition: all 0.2s ease;
    }

    .copy-btn:hover {
        transform: scale(1.1);
    }

    /* Toast animation */
    .toast-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .toast-error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .toast-info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    /* Mobile full-screen adjustment */
    @media (max-width: 640px) {
        #aiChatSidebar {
            width: 100% !important;
        }
        #chatHistoryModal {
            right: 16px !important;
            width: calc(100% - 32px) !important;
            top: 16px !important;
        }
        .history-actions {
            position: static;
            transform: none;
            margin-top: 8px;
            justify-content: flex-end;
        }
        .history-item {
            flex-direction: column;
        }
    }
</style>

<script>
    // State management
    let isProcessing = false;
    let messageCount = 0;
    let currentSessionId = null;
    let suggestionsVisible = true;
    let currentUserRole = '{{ $userRole }}';
    let pendingDeleteSessionId = null;
    let pendingRenameSessionId = null;
    let pendingShareSessionId = null;
    let pinnedSessions = JSON.parse(localStorage.getItem('pinnedSessions') || '[]');

    // DOM Elements
    let messagesContainer, chatInput, sendButton, charCountSpan, suggestionsContainer;

    // Initialize DOM elements after page load
    function initElements() {
        messagesContainer = document.getElementById('chatMessages');
        chatInput = document.getElementById('chatInput');
        sendButton = document.getElementById('sendButton');
        charCountSpan = document.getElementById('charCount');
        suggestionsContainer = document.getElementById('quickSuggestions');
    }

    // Auto-resize textarea
    function initTextarea() {
        if (!chatInput) return;

        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            if (charCountSpan) charCountSpan.textContent = this.value.length;

            if (this.value.length > 1800) {
                charCountSpan.classList.add('text-yellow-500');
                charCountSpan.classList.remove('text-gray-500');
            } else {
                charCountSpan.classList.remove('text-yellow-500');
                charCountSpan.classList.add('text-gray-500');
            }
        });

        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    // Beautiful Toast Notification System
    function showToast(message, type = 'success') {
        const existingToast = document.querySelector('.custom-toast');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.className = `custom-toast fixed bottom-24 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl transform transition-all duration-300 animate-slide-in-right ${
            type === 'success' ? 'toast-success' :
            type === 'error' ? 'toast-error' :
            'toast-info'
        } text-white font-medium`;

        const icon = type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ';

        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">${icon}</div>
                <span class="text-sm">${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // Toggle suggestions visibility
    window.toggleSuggestions = function() {
        suggestionsVisible = !suggestionsVisible;
        const suggestionsDiv = document.getElementById('quickSuggestions');
        const toggleBtn = document.getElementById('toggleSuggestionsBtn');
        const section = document.getElementById('quickSuggestionsSection');

        if (suggestionsVisible) {
            suggestionsDiv.classList.remove('suggestions-collapsed');
            section.style.paddingBottom = '12px';
            toggleBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
            </svg>`;
            toggleBtn.title = "Hide suggestions";
        } else {
            suggestionsDiv.classList.add('suggestions-collapsed');
            section.style.paddingBottom = '4px';
            toggleBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>`;
            toggleBtn.title = "Show suggestions";
        }
    };

    // Auto-hide suggestions helper
    function autoHideSuggestions() {
        if (suggestionsVisible) {
            toggleSuggestions();
        }
    }

    // Show suggestions helper
    function showSuggestions() {
        if (!suggestionsVisible) {
            toggleSuggestions();
        }
    }

    // Open history modal
    window.toggleChatHistory = async function() {
        const modal = document.getElementById('chatHistoryModal');

        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('scale-95', 'opacity-0');
                modal.classList.add('scale-100', 'opacity-100');
            }, 10);
            await loadChatHistoryList();
        } else {
            closeHistoryModal();
        }
    };

    function closeHistoryModal() {
        const modal = document.getElementById('chatHistoryModal');
        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Pin/Unpin session
    window.togglePinSession = function(sessionId, event) {
        event.stopPropagation();

        if (pinnedSessions.includes(sessionId)) {
            pinnedSessions = pinnedSessions.filter(id => id !== sessionId);
            showToast('Chat unpinned', 'info');
        } else {
            pinnedSessions.push(sessionId);
            showToast('Chat pinned', 'success');
        }

        localStorage.setItem('pinnedSessions', JSON.stringify(pinnedSessions));
        loadChatHistoryList();
    };

    // Open rename modal
    window.openRenameModal = function(sessionId, currentTitle, event) {
        event.stopPropagation();
        pendingRenameSessionId = sessionId;
        const input = document.getElementById('renameChatInput');
        input.value = currentTitle;

        const modal = document.getElementById('renameChatModal');
        const content = document.getElementById('renameModalContent');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
            input.focus();
            input.select();
        }, 10);
    };

    function closeRenameModal() {
        const modal = document.getElementById('renameChatModal');
        const content = document.getElementById('renameModalContent');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingRenameSessionId = null;
        }, 300);
    }

    window.confirmRenameSession = async function() {
        const newTitle = document.getElementById('renameChatInput').value.trim();
        if (!newTitle) {
            showToast('Please enter a title', 'error');
            return;
        }

        try {
            const response = await fetch(`/chat/sessions/${pendingRenameSessionId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ title: newTitle })
            });

            const data = await response.json();

            if (data.success) {
                showToast('Chat renamed successfully', 'success');
                closeRenameModal();
                await loadChatHistoryList();
                if (currentSessionId == pendingRenameSessionId) {
                    // Update current session title in header if needed
                }
            } else {
                showToast(data.message || 'Failed to rename', 'error');
            }
        } catch (error) {
            console.error('Error renaming session:', error);
            showToast('Failed to rename chat', 'error');
        }
    };

    // Open share modal
    window.openShareModal = function(sessionId, event) {
        event.stopPropagation();
        pendingShareSessionId = sessionId;
        const shareLink = `${window.location.origin}/chat/share/${sessionId}`;
        document.getElementById('shareChatLink').textContent = shareLink;

        const modal = document.getElementById('shareChatModal');
        const content = document.getElementById('shareModalContent');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    function closeShareModal() {
        const modal = document.getElementById('shareChatModal');
        const content = document.getElementById('shareModalContent');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingShareSessionId = null;
        }, 300);
    }

    window.copyShareLink = function() {
        const link = document.getElementById('shareChatLink').textContent;
        navigator.clipboard.writeText(link);
        showToast('Link copied to clipboard!', 'success');
        closeShareModal();
    };

    // Open delete confirmation modal
    window.openDeleteModal = function(sessionId, sessionTitle, event) {
        event.stopPropagation();
        pendingDeleteSessionId = sessionId;
        const messageEl = document.getElementById('deleteModalMessage');
        messageEl.innerHTML = `"<strong>${escapeHtml(sessionTitle)}</strong>" can't be recovered. Share links from it will be disabled.`;

        const modal = document.getElementById('deleteConfirmationModal');
        const content = document.getElementById('deleteModalContent');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    function closeDeleteModal() {
        const modal = document.getElementById('deleteConfirmationModal');
        const content = document.getElementById('deleteModalContent');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingDeleteSessionId = null;
        }, 300);
    }

    window.confirmDeleteSession = async function() {
        if (!pendingDeleteSessionId) return;

        try {
            const response = await fetch(`/chat/sessions/${pendingDeleteSessionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            closeDeleteModal();

            if (data.success) {
                showToast(data.message, 'success');
                await loadChatHistoryList();

                if (currentSessionId == pendingDeleteSessionId) {
                    await startNewChat();
                }

                // Remove from pinned if needed
                if (pinnedSessions.includes(pendingDeleteSessionId)) {
                    pinnedSessions = pinnedSessions.filter(id => id !== pendingDeleteSessionId);
                    localStorage.setItem('pinnedSessions', JSON.stringify(pinnedSessions));
                }
            } else {
                showToast(data.message || 'Failed to delete conversation', 'error');
            }
        } catch (error) {
            console.error('Error deleting session:', error);
            showToast('Failed to delete conversation', 'error');
        } finally {
            pendingDeleteSessionId = null;
        }
    };

    // Load chat sessions list with pin support
    async function loadChatHistoryList() {
        const container = document.getElementById('chatHistoryList');
        if (!container) return;

        container.innerHTML = '<div class="text-center text-gray-500 py-8 text-sm">Loading sessions...</div>';

        try {
            const response = await fetch('/chat/sessions', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (data.success && data.sessions && data.sessions.length > 0) {
                let sessions = data.sessions;

                // Sort: pinned first, then by updated_at
                sessions.sort((a, b) => {
                    const aPinned = pinnedSessions.includes(a.id);
                    const bPinned = pinnedSessions.includes(b.id);
                    if (aPinned && !bPinned) return -1;
                    if (!aPinned && bPinned) return 1;
                    return new Date(b.updated_at) - new Date(a.updated_at);
                });

                container.innerHTML = sessions.map(session => {
                    const isPinned = pinnedSessions.includes(session.id);
                    return `
                        <div class="history-item p-3 rounded-xl cursor-pointer bg-gray-800/30 hover:bg-gray-800/50 transition-all relative" onclick="loadChatSession('${session.id}')">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0 pr-2">
                                    <div class="flex items-center gap-2">
                                        ${isPinned ? '<svg class="w-3 h-3 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.5v6l.5.5.5-.5v-6H18v-2l-2-2z"/></svg>' : ''}
                                        <p class="text-sm text-white font-medium truncate">${escapeHtml(session.title || 'Conversation')}</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">${new Date(session.updated_at).toLocaleDateString()} • ${session.message_count || 0} messages</p>
                                    <p class="text-xs text-gray-400 truncate mt-1">${escapeHtml(session.preview || 'No messages')}</p>
                                </div>
                                <div class="history-actions">
                                    <button onclick="event.stopPropagation(); togglePinSession('${session.id}', event)" class="history-action-btn text-gray-400 hover:text-yellow-400" title="${isPinned ? 'Unpin' : 'Pin'}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                        </svg>
                                    </button>
                                    <button onclick="event.stopPropagation(); openRenameModal('${session.id}', '${escapeHtml(session.title || 'Conversation')}', event)" class="history-action-btn text-gray-400 hover:text-blue-400" title="Rename">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button onclick="event.stopPropagation(); openShareModal('${session.id}', event)" class="history-action-btn text-gray-400 hover:text-green-400" title="Share">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                        </svg>
                                    </button>
                                    <button onclick="event.stopPropagation(); openDeleteModal('${session.id}', '${escapeHtml(session.title || 'Conversation')}', event)" class="history-action-btn text-gray-400 hover:text-red-400" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                container.innerHTML = '<div class="text-center text-gray-500 py-8 text-sm">No conversations yet.<br>Start a new chat!</div>';
            }
        } catch (error) {
            console.error('Error loading sessions:', error);
            container.innerHTML = '<div class="text-center text-gray-500 py-8 text-sm">Failed to load sessions</div>';
            showToast('Failed to load chat history', 'error');
        }
    }

    // Load specific chat session
    window.loadChatSession = async function(sessionId) {
        try {
            const response = await fetch(`/chat/sessions/${sessionId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (data.success && messagesContainer) {
                currentSessionId = sessionId;
                messagesContainer.innerHTML = '';
                messageCount = 0;

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        addMessageToChat(msg.message, msg.role === 'user' ? 'user' : 'ai', false);
                        messageCount++;
                    });
                    updateMessageCount();
                } else {
                    addWelcomeMessage();
                }

                scrollToBottom();
                closeHistoryModal();
                showToast('Conversation loaded successfully', 'success');

                if (messageCount > 0 && suggestionsVisible) {
                    autoHideSuggestions();
                }
            } else {
                showToast(data.message || 'Failed to load conversation', 'error');
            }
        } catch (error) {
            console.error('Error loading session:', error);
            showToast('Failed to load conversation', 'error');
        }
    };

    // Start new chat
    window.startNewChat = async function() {
        try {
            const response = await fetch('/chat/sessions', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.session) {
                currentSessionId = data.session.id;
                messagesContainer.innerHTML = '';
                messageCount = 0;
                addWelcomeMessage();
                updateMessageCount();
                showToast('New conversation started', 'success');

                if (!suggestionsVisible) {
                    showSuggestions();
                }

                await loadChatHistoryList();
                if (chatInput) chatInput.focus();
            } else {
                currentSessionId = null;
                messagesContainer.innerHTML = '';
                messageCount = 0;
                addWelcomeMessage();
                updateMessageCount();
                showToast('New conversation started', 'success');

                if (!suggestionsVisible) {
                    showSuggestions();
                }

                if (chatInput) chatInput.focus();
            }
        } catch (error) {
            console.error('Error creating new session:', error);
            currentSessionId = null;
            messagesContainer.innerHTML = '';
            messageCount = 0;
            addWelcomeMessage();
            updateMessageCount();
            showToast('New conversation started', 'info');

            if (!suggestionsVisible) {
                showSuggestions();
            }

            if (chatInput) chatInput.focus();
        }
    };

    function addWelcomeMessage() {
        if (!messagesContainer) return;

        messagesContainer.innerHTML = `
            <div class="flex justify-start animate-fade-in welcome-message" id="welcomeMessage">
                <div class="bg-gradient-to-br from-purple-600/20 to-blue-600/20 backdrop-blur-sm border border-purple-500/30 rounded-2xl rounded-tl-none px-5 py-4 max-w-[90%] shadow-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-purple-400">MyGym AI</span>
                        <span class="text-xs text-gray-500">Online</span>
                    </div>
                    <p class="text-sm text-gray-200 leading-relaxed">✨ New conversation! How can I help you today?</p>
                    <span class="text-xs text-gray-500 mt-2 block">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                </div>
            </div>
        `;
    }

    // Clear all history with beautiful confirmation and toast
    window.clearAllHistory = async function() {
        if (confirm('⚠️ WARNING: This will delete ALL conversations permanently. This action cannot be undone. Are you sure?')) {
            try {
                const response = await fetch('/chat/clear-all', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    pinnedSessions = [];
                    localStorage.setItem('pinnedSessions', JSON.stringify(pinnedSessions));
                    await startNewChat();
                    await loadChatHistoryList();
                    closeHistoryModal();
                } else {
                    showToast(data.message || 'Failed to clear history', 'error');
                }
            } catch (error) {
                console.error('Error clearing history:', error);
                showToast('Failed to clear history', 'error');
            }
        }
    };

    // Load current session messages
    async function loadCurrentSession() {
        try {
            const response = await fetch('/chat/sessions/current', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (data.success && data.messages && data.messages.length > 0 && messagesContainer) {
                const welcomeMsg = document.getElementById('welcomeMessage');
                if (welcomeMsg) welcomeMsg.remove();

                messageCount = data.messages.length;
                updateMessageCount();

                data.messages.forEach(msg => {
                    addMessageToChat(msg.message, msg.role === 'user' ? 'user' : 'ai', false);
                });
                scrollToBottom();

                if (data.session_id) {
                    currentSessionId = data.session_id;
                }

                if (messageCount > 0 && suggestionsVisible) {
                    autoHideSuggestions();
                }
            } else if (data.success && (!data.messages || data.messages.length === 0)) {
                addWelcomeMessage();
                if (!suggestionsVisible) {
                    showSuggestions();
                }
            }
        } catch (error) {
            console.error('Error loading current session:', error);
            addWelcomeMessage();
        }
    }

    function updateMessageCount() {
        const countElement = document.getElementById('messageCount');
        if (countElement) {
            countElement.textContent = `${messageCount} message${messageCount !== 1 ? 's' : ''}`;
        }
    }

    // Load quick suggestions
    async function loadSuggestions() {
        if (!suggestionsContainer) return;

        suggestionsContainer.innerHTML = '<div class="flex gap-2"><div class="h-8 w-20 bg-gray-700 rounded-full animate-pulse"></div><div class="h-8 w-24 bg-gray-700 rounded-full animate-pulse"></div><div class="h-8 w-28 bg-gray-700 rounded-full animate-pulse"></div></div>';

        try {
            const response = await fetch('/chat/suggestions', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (data.success && data.suggestions && data.suggestions.length > 0) {
                suggestionsContainer.innerHTML = data.suggestions.map(suggestion => `
                    <button onclick="window.useSuggestion('${escapeHtml(suggestion).replace(/'/g, "\\'")}')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-800/80 backdrop-blur hover:bg-purple-600/30 text-gray-300 text-xs rounded-full transition-all duration-200 hover:scale-105 border border-gray-700 hover:border-purple-500/50">
                        <span>⚡</span>
                        ${escapeHtml(suggestion.length > 35 ? suggestion.substring(0, 35) + '...' : suggestion)}
                    </button>
                `).join('');
            } else {
                const fallbackSuggestions = getFallbackSuggestions();
                suggestionsContainer.innerHTML = fallbackSuggestions.map(suggestion => `
                    <button onclick="window.useSuggestion('${escapeHtml(suggestion).replace(/'/g, "\\'")}')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-800/80 backdrop-blur hover:bg-purple-600/30 text-gray-300 text-xs rounded-full transition-all duration-200 hover:scale-105 border border-gray-700 hover:border-purple-500/50">
                        <span>⚡</span>
                        ${escapeHtml(suggestion.length > 35 ? suggestion.substring(0, 35) + '...' : suggestion)}
                    </button>
                `).join('');
            }
        } catch (error) {
            console.error('Error loading suggestions:', error);
            const fallbackSuggestions = getFallbackSuggestions();
            suggestionsContainer.innerHTML = fallbackSuggestions.map(suggestion => `
                <button onclick="window.useSuggestion('${escapeHtml(suggestion).replace(/'/g, "\\'")}')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-800/80 backdrop-blur hover:bg-purple-600/30 text-gray-300 text-xs rounded-full transition-all duration-200 hover:scale-105 border border-gray-700 hover:border-purple-500/50">
                    <span>⚡</span>
                    ${escapeHtml(suggestion.length > 35 ? suggestion.substring(0, 35) + '...' : suggestion)}
                </button>
            `).join('');
        }
    }

    function getFallbackSuggestions() {
        const suggestions = {
            'admin': ['📊 Show me member analytics', '💰 View revenue reports', '👨‍🏫 Instructor performance stats', '📈 Monthly growth metrics', '🆕 Recent member signups', '⭐ Most popular classes'],
            'instructor': ['📅 What classes do I have today?', '👥 How many students do I have?', '💰 My earnings this month', '📊 Show my class attendance rates', '⭐ Which class is most popular?', '💡 Class preparation tips'],
            'member': ['💪 What is my next workout?', '📋 Suggest a workout plan', '🥗 Healthy meal ideas', '🔥 How to stay motivated?', '📅 Show my upcoming classes', '🎯 Help me set fitness goals']
        };
        return suggestions[currentUserRole] || suggestions['member'];
    }

    window.refreshSuggestions = function() {
        loadSuggestions();
        showToast('Suggestions refreshed', 'info');
    };

    window.useSuggestion = function(suggestion) {
        autoHideSuggestions();
        if (chatInput) {
            chatInput.value = suggestion;
            chatInput.dispatchEvent(new Event('input'));
            chatInput.focus();
            sendMessage();
        }
    };

    // Send message to AI
    window.sendMessage = async function() {
        if (isProcessing) return;
        if (!chatInput) return;

        const message = chatInput.value.trim();
        if (!message) {
            chatInput.classList.add('border-red-500');
            setTimeout(() => chatInput.classList.remove('border-red-500'), 500);
            return;
        }

        if (message.length > 2000) {
            showToast('Message is too long! Maximum 2000 characters.', 'error');
            return;
        }

        if (suggestionsVisible) {
            autoHideSuggestions();
        }

        const welcomeMsg = document.getElementById('welcomeMessage');
        if (welcomeMsg) welcomeMsg.remove();

        addMessageToChat(message, 'user');
        chatInput.value = '';
        chatInput.style.height = 'auto';
        if (charCountSpan) charCountSpan.textContent = '0';
        messageCount++;
        updateMessageCount();

        isProcessing = true;
        if (sendButton) sendButton.disabled = true;
        showTypingIndicator();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message, session_id: currentSessionId })
            });

            const data = await response.json();
            hideTypingIndicator();

            if (data.success) {
                if (!currentSessionId) currentSessionId = data.session_id;
                await streamMessageToChat(data.message, 'ai');
                messageCount++;
                updateMessageCount();
                await loadChatHistoryList();
            } else {
                addMessageToChat(data.message || 'Sorry, I encountered an error. Please try again.', 'ai');
                messageCount++;
                updateMessageCount();
                showToast(data.message || 'Failed to get response', 'error');
            }
        } catch (error) {
            console.error('Fetch error:', error);
            hideTypingIndicator();
            addMessageToChat('Network error. Please check your connection and try again.', 'ai');
            messageCount++;
            updateMessageCount();
            showToast('Network error. Please try again.', 'error');
        } finally {
            isProcessing = false;
            if (sendButton) sendButton.disabled = false;
            if (chatInput) chatInput.focus();
        }
    };

    // Streaming effect for messages
    async function streamMessageToChat(fullMessage, type) {
        const messageDiv = createMessageContainer(type, '');
        const contentDiv = messageDiv.querySelector('.message-content');
        if (!messagesContainer) return;
        messagesContainer.appendChild(messageDiv);
        scrollToBottom();
        await new Promise(resolve => setTimeout(resolve, 100));
        const words = fullMessage.split(/(\s+)/);
        let displayedText = '';
        for (let i = 0; i < words.length; i++) {
            displayedText += words[i];
            if (contentDiv) contentDiv.innerHTML = formatMessage(escapeHtml(displayedText));
            await new Promise(resolve => setTimeout(resolve, 8));
            scrollToBottom();
        }
        addCopyButton(messageDiv, fullMessage);
    }

    function createMessageContainer(type, content) {
        const div = document.createElement('div');
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        div.className = `flex ${type === 'user' ? 'justify-end' : 'justify-start'} animate-fade-in message-bubble`;
        if (type === 'user') {
            div.innerHTML = `
                <div class="bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl rounded-tr-none px-5 py-3 max-w-[85%] shadow-xl">
                    <div class="flex items-center gap-2 mb-1 justify-end">
                        <span class="text-xs font-semibold text-purple-200">You</span>
                        <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="message-content text-sm text-white leading-relaxed">${formatMessage(escapeHtml(content))}</p>
                    <div class="flex items-center justify-end gap-1 mt-2">
                        <span class="text-xs text-purple-200">${time}</span>
                    </div>
                </div>
            `;
        } else {
            div.innerHTML = `
                <div class="glass-effect rounded-2xl rounded-tl-none px-5 py-3 max-w-[85%] shadow-xl border border-purple-500/20">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-purple-400">MyGym AI</span>
                        <span class="text-xs text-gray-500">• Groq</span>
                    </div>
                    <p class="message-content text-sm text-gray-200 leading-relaxed">${formatMessage(escapeHtml(content))}</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-gray-500">${time}</span>
                        <div class="action-buttons opacity-0 transition-opacity duration-200"></div>
                    </div>
                </div>
            `;
        }
        return div;
    }

    function addMessageToChat(message, type, saveToHistory = true, timestamp = null) {
        if (!messagesContainer) return;
        const div = createMessageContainer(type, message);
        messagesContainer.appendChild(div);
        scrollToBottom();
        if (type === 'ai') addCopyButton(div, message);
    }

    function addCopyButton(messageDiv, text) {
        const actionDiv = messageDiv.querySelector('.action-buttons');
        if (!actionDiv) return;
        actionDiv.classList.remove('opacity-0');
        actionDiv.innerHTML = `
            <button onclick="window.copyToClipboard('${escapeHtml(text).replace(/'/g, "\\'")}')" class="copy-btn p-1 rounded-md text-gray-400 hover:text-purple-400 transition-all duration-200" title="Copy response">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </button>
            <button onclick="window.regenerateLastMessage()" class="copy-btn p-1 rounded-md text-gray-400 hover:text-purple-400 transition-all duration-200" title="Regenerate response">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        `;
    }

    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text);
        showToast('Copied to clipboard!', 'success');
    };

    window.regenerateLastMessage = async function() {
        if (!messagesContainer) return;
        const userMessages = Array.from(messagesContainer.querySelectorAll('.justify-end'));
        if (userMessages.length === 0) return;
        const lastUserMessage = userMessages[userMessages.length - 1];
        const messageText = lastUserMessage.querySelector('.message-content')?.innerText;
        if (messageText) {
            const aiMessages = Array.from(messagesContainer.querySelectorAll('.justify-start'));
            if (aiMessages.length > 0) {
                const lastAiMessage = aiMessages[aiMessages.length - 1];
                lastAiMessage.remove();
                messageCount--;
                updateMessageCount();
            }
            if (chatInput) {
                chatInput.value = messageText;
                sendMessage();
            }
        }
    };

    function formatMessage(message) {
        let formatted = message;
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong class="text-purple-300">$1</strong>');
        formatted = formatted.replace(/\*(.*?)\*/g, '<em class="text-gray-300">$1</em>');
        formatted = formatted.replace(/\n/g, '<br>');
        formatted = formatted.replace(/• (.*?)(<br>|$)/g, '<li class="ml-4 list-disc">$1</li>');
        return formatted;
    }

    function showTypingIndicator() {
        if (!messagesContainer) return;
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.className = 'flex justify-start animate-fade-in';
        typingDiv.innerHTML = `
            <div class="glass-effect rounded-2xl rounded-tl-none px-5 py-3">
                <div class="flex items-center gap-2">
                    <div class="flex gap-1.5">
                        <div class="w-2 h-2 bg-purple-400 rounded-full typing-dot" style="animation-delay: 0s"></div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full typing-dot" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full typing-dot" style="animation-delay: 0.4s"></div>
                    </div>
                    <span class="text-xs text-gray-400">AI is crafting response...</span>
                </div>
            </div>
        `;
        messagesContainer.appendChild(typingDiv);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) indicator.remove();
    }

    function scrollToBottom() {
        if (messagesContainer) messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    window.clearInput = function() {
        if (chatInput) {
            chatInput.value = '';
            chatInput.style.height = 'auto';
            if (charCountSpan) charCountSpan.textContent = '0';
            chatInput.focus();
        }
    };

    window.exportChatHistory = function() {
        window.open('/chat/export', '_blank');
        showToast('Exporting chat history...', 'info');
    };

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Open/Close functions
    window.openChat = function() {
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
            initElements();
            loadCurrentSession();
            loadSuggestions();
            setTimeout(() => {
                if (chatInput) chatInput.focus();
            }, 300);
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

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('chatHistoryModal');
        const historyBtn = event.target.closest('button[onclick*="toggleChatHistory"]');
        const deleteModal = document.getElementById('deleteConfirmationModal');
        const renameModal = document.getElementById('renameChatModal');
        const shareModal = document.getElementById('shareChatModal');

        if (modal && !modal.classList.contains('hidden')) {
            if (!modal.contains(event.target) && !historyBtn) {
                closeHistoryModal();
            }
        }

        if (deleteModal && !deleteModal.classList.contains('hidden')) {
            if (!deleteModal.contains(event.target)) {
                closeDeleteModal();
            }
        }

        if (renameModal && !renameModal.classList.contains('hidden')) {
            if (!renameModal.contains(event.target)) {
                closeRenameModal();
            }
        }

        if (shareModal && !shareModal.classList.contains('hidden')) {
            if (!shareModal.contains(event.target)) {
                closeShareModal();
            }
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const sidebar = document.getElementById('aiChatSidebar');
            if (sidebar && !sidebar.classList.contains('hidden')) closeChat();
            closeHistoryModal();
            closeDeleteModal();
            closeRenameModal();
            closeShareModal();
        }
    });

    // Close on overlay click
    document.getElementById('chatOverlay')?.addEventListener('click', closeChat);

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initElements();
        initTextarea();
        loadSuggestions();
    });
</script>

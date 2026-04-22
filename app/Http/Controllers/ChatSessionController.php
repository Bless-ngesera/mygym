<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Services\AIChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ChatSessionController extends Controller
{
    protected $aiService;

    public function __construct(AIChatService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get all sessions for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            $sessions = ChatSession::where('user_id', Auth::id())
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'title' => $session->title,
                        'preview' => $session->preview,
                        'message_count' => $session->message_count,
                        'updated_at' => $session->updated_at->toISOString(),
                        'last_activity' => $session->last_activity
                    ];
                });

            return response()->json([
                'success' => true,
                'sessions' => $sessions
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching sessions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sessions'
            ], 500);
        }
    }

    /**
     * Get current/latest session for the user
     */
    public function getCurrentSession(Request $request)
    {
        try {
            $session = ChatSession::where('user_id', Auth::id())
                ->latest()
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => true,
                    'messages' => [],
                    'session_id' => null
                ]);
            }

            $messages = ChatMessage::where('chat_session_id', $session->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'role' => $message->role,
                        'message' => $message->message,
                        'created_at' => $message->created_at->toISOString(),
                        'formatted_time' => $message->created_at->format('g:i A'),
                        'formatted_date' => $message->created_at->format('M j, Y')
                    ];
                });

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'session_id' => $session->id,
                'session_title' => $session->title
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching current session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'messages' => [],
                'session_id' => null
            ], 500);
        }
    }

    /**
     * Create a new chat session
     */
    public function store(Request $request)
    {
        try {
            $session = ChatSession::create([
                'user_id' => Auth::id(),
                'title' => 'New Conversation',
                'last_message_at' => now(),
                'message_count' => 0,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'title' => $session->title,
                    'created_at' => $session->created_at->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create session'
            ], 500);
        }
    }

    /**
     * Get a specific session with its messages
     */
    public function show($id)
    {
        try {
            $session = ChatSession::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();

            $messages = ChatMessage::where('chat_session_id', $session->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'role' => $message->role,
                        'message' => $message->message,
                        'created_at' => $message->created_at->toISOString(),
                        'formatted_time' => $message->created_at->format('g:i A'),
                        'formatted_date' => $message->created_at->format('M j, Y')
                    ];
                });

            return response()->json([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'title' => $session->title,
                    'created_at' => $session->created_at->toISOString()
                ],
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }
    }

    /**
     * Update session title
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255'
            ]);

            $session = ChatSession::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();

            $session->title = $request->title;
            $session->save();

            return response()->json([
                'success' => true,
                'message' => 'Session renamed successfully',
                'session' => $session
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to rename session'
            ], 500);
        }
    }

    /**
     * Delete a session and its messages
     */
    public function destroy($id)
    {
        try {
            $session = ChatSession::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();

            // Delete all messages first
            ChatMessage::where('chat_session_id', $session->id)->delete();

            // Delete the session
            $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Conversation deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete conversation'
            ], 500);
        }
    }

    /**
     * Share a chat session (NEW METHOD)
     */
    public function shareSession($id)
    {
        try {
            $session = ChatSession::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();

            // Generate a unique share token
            $shareToken = hash('sha256', $session->id . Auth::id() . now()->timestamp . random_bytes(16));

            // Store share data in cache (expires in 7 days)
            Cache::put('chat_share_' . $shareToken, [
                'session_id' => $session->id,
                'user_id' => Auth::id(),
                'shared_by_name' => Auth::user()->name,
                'shared_by_email' => Auth::user()->email,
                'expires_at' => now()->addDays(7)
            ], 604800); // 7 days

            $shareUrl = url('/chat/share/' . $shareToken);

            return response()->json([
                'success' => true,
                'share_url' => $shareUrl,
                'expires_in' => '7 days',
                'message' => 'Share link generated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error sharing session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate share link'
            ], 500);
        }
    }

    /**
     * View a shared chat session (public access - NEW METHOD)
     */
    public function getSharedSession($token)
    {
        try {
            $shareData = Cache::get('chat_share_' . $token);

            if (!$shareData) {
                abort(404, 'Share link expired or invalid');
            }

            $session = ChatSession::with('messages')->find($shareData['session_id']);

            if (!$session) {
                abort(404, 'Chat session not found');
            }

            // Return view for shared chat
            return view('chat.shared', [
                'session' => $session,
                'shared_by_name' => $shareData['shared_by_name'],
                'shared_by_email' => $shareData['shared_by_email'],
                'expires_at' => $shareData['expires_at']
            ]);
        } catch (\Exception $e) {
            Log::error('Error viewing shared session: ' . $e->getMessage());
            abort(404, 'Unable to load shared chat');
        }
    }

    /**
     * Send a message in a session
     * CRITICAL: This maintains ONE session per conversation
     */
    public function sendMessage(Request $request)
    {
        // Rate limiting
        $rateLimitKey = 'chat:' . Auth::id();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 20)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return response()->json([
                'success' => false,
                'message' => 'Too many messages. Please wait ' . ceil($seconds / 60) . ' minutes.'
            ], 429);
        }

        try {
            $request->validate([
                'message' => 'required|string|max:2000|min:1',
                'session_id' => 'nullable|exists:chat_sessions,id'
            ]);

            $user = Auth::user();
            $message = $request->message;
            $sessionId = $request->session_id;

            // CRITICAL FIX: Get or create session - ONE session per conversation
            if (!$sessionId) {
                // Check if there's an existing session with no messages or the most recent one
                $existingSession = ChatSession::where('user_id', $user->id)
                    ->latest()
                    ->first();

                // If there's an existing session that's empty or recent, use it
                if ($existingSession && $existingSession->message_count === 0) {
                    $session = $existingSession;
                    $sessionId = $session->id;
                    Log::info('Using existing empty session', ['session_id' => $sessionId]);
                } else {
                    // Create a brand new session for this conversation
                    $session = ChatSession::create([
                        'user_id' => $user->id,
                        'title' => substr($message, 0, 40) . (strlen($message) > 40 ? '...' : ''),
                        'last_message_at' => now(),
                        'message_count' => 0,
                        'is_active' => true
                    ]);
                    $sessionId = $session->id;
                    Log::info('Created new session', ['session_id' => $sessionId]);
                }
            } else {
                // Use existing session
                $session = ChatSession::where('user_id', $user->id)
                    ->where('id', $sessionId)
                    ->firstOrFail();
                Log::info('Using existing session', ['session_id' => $sessionId, 'current_messages' => $session->message_count]);
            }

            // Save user message
            $userMessage = ChatMessage::create([
                'user_id' => $user->id,
                'chat_session_id' => $sessionId,
                'role' => 'user',
                'message' => $message,
                'created_at' => now()
            ]);

            // Get conversation history (last 10 messages for context)
            $history = ChatMessage::where('chat_session_id', $sessionId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->message
                    ];
                })
                ->toArray();

            // Generate AI response
            $response = $this->aiService->generateResponse($user, $session, $message, $history);

            // Save AI response
            $aiMessage = ChatMessage::create([
                'user_id' => $user->id,
                'chat_session_id' => $sessionId,
                'role' => 'assistant',
                'message' => $response['message'],
                'tokens_used' => $response['tokens_used'] ?? null,
                'response_time_ms' => $response['response_time_ms'] ?? null,
                'created_at' => now()
            ]);

            // Update session stats - INCREMENT, not overwrite
            $session->last_message_at = now();
            $session->message_count = $session->message_count + 2; // Add both user and AI messages
            $session->save();

            // Update title if it's still default and has messages
            if ($session->title === 'New Conversation' && $session->message_count >= 2) {
                $session->title = substr($message, 0, 40) . (strlen($message) > 40 ? '...' : '');
                $session->save();
            }

            // Hit rate limiter
            RateLimiter::hit($rateLimitKey, 60);

            return response()->json([
                'success' => true,
                'session_id' => $sessionId,
                'message' => $response['message'],
                'message_id' => $aiMessage->id,
                'response_time' => $response['response_time_ms'] ?? null,
                'model' => $response['model'] ?? 'groq'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors()['message'] ?? ['Invalid input'])
            ], 422);
        } catch (\Exception $e) {
            Log::error('Send Message Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry, I encountered an error. Please try again.'
            ], 500);
        }
    }

    /**
     * Regenerate AI response for a message
     */
    public function regenerateMessage(Request $request, $messageId)
    {
        try {
            $user = Auth::user();

            // Get the AI message to regenerate
            $aiMessage = ChatMessage::where('id', $messageId)
                ->where('user_id', $user->id)
                ->where('role', 'assistant')
                ->firstOrFail();

            // Get the previous user message
            $userMessage = ChatMessage::where('chat_session_id', $aiMessage->chat_session_id)
                ->where('created_at', '<', $aiMessage->created_at)
                ->where('role', 'user')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$userMessage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot regenerate: No user message found'
                ], 400);
            }

            // Get session
            $session = ChatSession::find($aiMessage->chat_session_id);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            // Get conversation history (excluding the old AI response)
            $history = ChatMessage::where('chat_session_id', $session->id)
                ->where('created_at', '<', $aiMessage->created_at)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->message
                    ];
                })
                ->toArray();

            // Delete old AI response
            $aiMessage->delete();
            $session->message_count = $session->message_count - 1;
            $session->save();

            // Generate new response
            $response = $this->aiService->generateResponse($user, $session, $userMessage->message, $history);

            // Save new AI response
            $newAiMessage = ChatMessage::create([
                'user_id' => $user->id,
                'chat_session_id' => $session->id,
                'role' => 'assistant',
                'message' => $response['message'],
                'tokens_used' => $response['tokens_used'] ?? null,
                'response_time_ms' => $response['response_time_ms'] ?? null,
                'created_at' => now()
            ]);

            $session->message_count = $session->message_count + 1;
            $session->save();

            return response()->json([
                'success' => true,
                'message' => $response['message'],
                'message_id' => $newAiMessage->id,
                'response_time' => $response['response_time_ms'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Regenerate Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate response'
            ], 500);
        }
    }

    /**
     * Get role-based suggestions with multi-language support
     */
    public function getSuggestions(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $locale = app()->getLocale();

        // Multi-language suggestions
        $suggestions = [
            'admin' => [
                'en' => [
                    '📊 How many members joined this month?',
                    '💰 Show gym revenue statistics',
                    '👥 List inactive users',
                    '📈 What is the class attendance rate?',
                    '⭐ Which classes are most popular?',
                    '💵 Total instructor payouts this month',
                    '📅 Next week\'s schedule overview',
                    '🆕 Recent member signups'
                ],
                'es' => [
                    '📊 ¿Cuántos miembros se unieron este mes?',
                    '💰 Mostrar estadísticas de ingresos del gimnasio',
                    '👥 Listar usuarios inactivos',
                    '📈 ¿Cuál es la tasa de asistencia a clases?',
                    '⭐ ¿Qué clases son más populares?',
                    '💵 Total de pagos a instructores este mes',
                    '📅 Resumen del horario de la próxima semana',
                    '🆕 Nuevos miembros registrados'
                ],
                'fr' => [
                    '📊 Combien de membres ont rejoint ce mois-ci ?',
                    '💰 Afficher les statistiques de revenus du gymnase',
                    '👥 Lister les utilisateurs inactifs',
                    '📈 Quel est le taux de présence en classe ?',
                    '⭐ Quels sont les cours les plus populaires ?',
                    '💵 Total des paiements aux instructeurs ce mois-ci',
                    '📅 Aperçu de l\'emploi du temps de la semaine prochaine',
                    '🆕 Nouvelles inscriptions de membres'
                ],
                'sw' => [
                    '📊 Ni wanachama wangapi waliojiunga mwezi huu?',
                    '💰 Onyesha takwimu za mapato ya gym',
                    '👥 Orodhesha watumiaji wasio na shughuli',
                    '📈 Kiwango cha mahudhurio cha darasa ni kipi?',
                    '⭐ Ni madarasa gani yanayopendwa zaidi?',
                    '💵 Jumla ya malipo ya wakufunzi mwezi huu',
                    '📅 Muhtasari wa ratiba ya wiki ijayo',
                    '🆕 Wanachama wapya waliojiunga hivi karibuni'
                ],
                'ar' => [
                    '📊 كم عدد الأعضاء الذين انضموا هذا الشهر؟',
                    '💰 عرض إحصائيات إيرادات الصالة الرياضية',
                    '👥 قائمة المستخدمين غير النشطين',
                    '📈 ما هو معدل حضور الفصل؟',
                    '⭐ ما هي الفصول الأكثر شعبية؟',
                    '💵 إجمالي مدفوعات المدربين هذا الشهر',
                    '📅 نظرة عامة على جدول الأسبوع المقبل',
                    '🆕 اشتراكات الأعضاء الأخيرة'
                ]
            ],
            'instructor' => [
                'en' => [
                    '📅 What classes do I have today?',
                    '👥 How many students do I have?',
                    '💰 My earnings this month',
                    '📊 Show my class attendance rates',
                    '⭐ Which class is most popular?',
                    '📝 Class preparation tips',
                    '🎯 How to improve student engagement?',
                    '💡 New teaching techniques'
                ],
                'es' => [
                    '📅 ¿Qué clases tengo hoy?',
                    '👥 ¿Cuántos estudiantes tengo?',
                    '💰 Mis ganancias este mes',
                    '📊 Mostrar mis tasas de asistencia a clases',
                    '⭐ ¿Qué clase es más popular?',
                    '📝 Consejos para preparar clases',
                    '🎯 ¿Cómo mejorar la participación de los estudiantes?',
                    '💡 Nuevas técnicas de enseñanza'
                ],
                'fr' => [
                    '📅 Quels cours ai-je aujourd\'hui ?',
                    '👥 Combien d\'étudiants ai-je ?',
                    '💰 Mes revenus ce mois-ci',
                    '📊 Afficher mes taux de présence en classe',
                    '⭐ Quel cours est le plus populaire ?',
                    '📝 Conseils de préparation de cours',
                    '🎯 Comment améliorer l\'engagement des étudiants ?',
                    '💡 Nouvelles techniques d\'enseignement'
                ],
                'sw' => [
                    '📅 Nina madarasa gani leo?',
                    '👥 Nina wanafunzi wangapi?',
                    '💰 Mapato yangu mwezi huu',
                    '📊 Onyesha viwango vyangu vya mahudhurio ya darasa',
                    '⭐ Ni darasa gani linalopendwa zaidi?',
                    '📝 Vidokezo vya kuandaa darasa',
                    '🎯 Jinsi ya kuboresha ushirikishwaji wa wanafunzi?',
                    '💡 Mbinu mpya za ufundishaji'
                ],
                'ar' => [
                    '📅 ما هي الفصول التي لدي اليوم؟',
                    '👥 كم عدد الطلاب لدي؟',
                    '💰 أرباحي هذا الشهر',
                    '📊 عرض معدلات حضور فصولي',
                    '⭐ أي فصل هو الأكثر شعبية؟',
                    '📝 نصائح لإعداد الفصل',
                    '🎯 كيف تحسن مشاركة الطلاب؟',
                    '💡 تقنيات تدريس جديدة'
                ]
            ],
            'member' => [
                'en' => [
                    '💪 What is my next workout?',
                    '📋 Suggest a workout plan for beginners',
                    '🥗 Healthy post-workout meal ideas',
                    '🔥 How to stay motivated?',
                    '📅 Show my upcoming classes',
                    '🎯 Help me set fitness goals',
                    '💧 How much water should I drink?',
                    '😴 Importance of sleep for fitness'
                ],
                'es' => [
                    '💪 ¿Cuál es mi próximo entrenamiento?',
                    '📋 Sugerir un plan de entrenamiento para principiantes',
                    '🥗 Ideas de comidas saludables post-entrenamiento',
                    '🔥 ¿Cómo mantenerme motivado?',
                    '📅 Mostrar mis próximas clases',
                    '🎯 Ayúdame a establecer metas de fitness',
                    '💧 ¿Cuánta agua debo beber?',
                    '😴 Importancia del sueño para el fitness'
                ],
                'fr' => [
                    '💪 Quel est mon prochain entraînement ?',
                    '📋 Suggérer un plan d\'entraînement pour débutants',
                    '🥗 Idées de repas sains après l\'entraînement',
                    '🔥 Comment rester motivé ?',
                    '📅 Afficher mes prochains cours',
                    '🎯 Aidez-moi à fixer des objectifs de remise en forme',
                    '💧 Quelle quantité d\'eau dois-je boire ?',
                    '😴 Importance du sommeil pour la forme physique'
                ],
                'sw' => [
                    '💪 Mazoezi yangu yanayofuata ni yapi?',
                    '📋 Pendekeza mpango wa mazoezi kwa wanaoanza',
                    '🥗 Mawazo ya chakula bora baada ya mazoezi',
                    '🔥 Jinsi ya kukaa na motisha?',
                    '📅 Onyesha madarasa yangu yajayo',
                    '🎯 Nisaidie kuweka malengo ya siha',
                    '💧 Je, ninapaswa kunywa maji kiasi gani?',
                    '😴 Umuhimu wa usingizi kwa siha'
                ],
                'ar' => [
                    '💪 ما هو تمريني القادم؟',
                    '📋 اقتراح خطة تمرين للمبتدئين',
                    '🥗 أفكار وجبات صحية بعد التمرين',
                    '🔥 كيف تبقى متحفزاً؟',
                    '📅 عرض فصولي القادمة',
                    '🎯 ساعدني في تحديد أهداف اللياقة',
                    '💧 ما كمية الماء التي يجب أن أشربها؟',
                    '😴 أهمية النوم للياقة البدنية'
                ]
            ]
        ];

        // Get suggestions for the user's role and current locale
        $userSuggestions = $suggestions[$role][$locale] ?? $suggestions[$role]['en'] ?? $suggestions['member']['en'];

        // Randomize and return first 6 suggestions
        shuffle($userSuggestions);
        $userSuggestions = array_slice($userSuggestions, 0, 6);

        return response()->json([
            'success' => true,
            'suggestions' => $userSuggestions,
            'role' => $role,
            'locale' => $locale
        ]);
    }

    /**
     * Clear all history for the user
     */
    public function clearAllHistory(Request $request)
    {
        try {
            $sessions = ChatSession::where('user_id', Auth::id())->get();
            $deletedSessions = $sessions->count();
            $deletedMessages = 0;

            foreach ($sessions as $session) {
                $deletedMessages += ChatMessage::where('chat_session_id', $session->id)->delete();
                $session->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "Deleted {$deletedSessions} conversations with {$deletedMessages} messages",
                'deleted_sessions' => $deletedSessions,
                'deleted_messages' => $deletedMessages
            ]);
        } catch (\Exception $e) {
            Log::error('Clear All History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear chat history'
            ], 500);
        }
    }

    /**
     * Legacy method - Get history list (for backward compatibility)
     */
    public function getHistoryList(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Legacy method - Get history (for backward compatibility)
     */
    public function getHistory(Request $request)
    {
        return $this->getCurrentSession($request);
    }

    /**
     * Legacy method - Clear history (for backward compatibility)
     */
    public function clearHistory(Request $request)
    {
        $session = ChatSession::where('user_id', Auth::id())->latest()->first();
        if ($session) {
            ChatMessage::where('chat_session_id', $session->id)->delete();
            $session->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Current chat cleared successfully'
        ]);
    }

    /**
     * Legacy method - Delete message (for backward compatibility)
     */
    public function deleteMessage($messageId)
    {
        try {
            $message = ChatMessage::where('id', $messageId)
                ->whereHas('chatSession', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->firstOrFail();

            $session = ChatSession::find($message->chat_session_id);
            $message->delete();

            if ($session) {
                $session->message_count = max(0, $session->message_count - 1);
                $session->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Message Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message'
            ], 500);
        }
    }

    /**
     * Legacy method - Get message (for backward compatibility)
     */
    public function getMessage($messageId)
    {
        try {
            $message = ChatMessage::where('id', $messageId)
                ->whereHas('chatSession', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'content' => $message->message,
                    'role' => $message->role,
                    'created_at' => $message->created_at->toISOString(),
                    'formatted_time' => $message->created_at->format('g:i A'),
                    'formatted_date' => $message->created_at->format('M j, Y')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get Message Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
    }

    /**
     * Legacy method - Get statistics (for backward compatibility)
     */
    public function getStatistics(Request $request)
    {
        try {
            $totalSessions = ChatSession::where('user_id', Auth::id())->count();
            $totalMessages = ChatMessage::whereHas('chatSession', function ($query) {
                $query->where('user_id', Auth::id());
            })->count();

            $lastWeekMessages = ChatMessage::whereHas('chatSession', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('created_at', '>=', now()->subDays(7))->count();

            $averageMessagesPerSession = $totalSessions > 0 ? round($totalMessages / $totalSessions, 1) : 0;

            return response()->json([
                'success' => true,
                'statistics' => [
                    'total_sessions' => $totalSessions,
                    'total_messages' => $totalMessages,
                    'messages_last_week' => $lastWeekMessages,
                    'average_messages_per_session' => $averageMessagesPerSession,
                    'has_history' => $totalMessages > 0
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get Statistics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'statistics' => []
            ]);
        }
    }

    /**
     * Legacy method - Export history (for backward compatibility)
     */
    public function exportHistory(Request $request)
    {
        try {
            $sessions = ChatSession::where('user_id', Auth::id())
                ->with('messages')
                ->get();

            $export = [
                'user' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role
                ],
                'export_date' => now()->toISOString(),
                'total_sessions' => $sessions->count(),
                'total_messages' => $sessions->sum('message_count'),
                'chats' => $sessions->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'title' => $session->title,
                        'created_at' => $session->created_at->toISOString(),
                        'last_message_at' => $session->last_message_at,
                        'message_count' => $session->message_count,
                        'messages' => $session->messages->map(function ($message) {
                            return [
                                'role' => $message->role,
                                'message' => $message->message,
                                'timestamp' => $message->created_at->toISOString(),
                                'formatted_time' => $message->created_at->format('g:i A'),
                                'formatted_date' => $message->created_at->format('M j, Y')
                            ];
                        })
                    ];
                })
            ];

            $filename = 'chat_history_' . Auth::user()->name . '_' . date('Y-m-d_His') . '.json';

            return response()->json($export)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            Log::error('Export History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to export history'
            ], 500);
        }
    }

    /**
     * Legacy method - Submit feedback (for backward compatibility)
     */
    public function submitFeedback(Request $request)
    {
        try {
            $request->validate([
                'message_id' => 'required|exists:chat_messages,id',
                'rating' => 'required|integer|min:1|max:5',
                'feedback' => 'nullable|string|max:500'
            ]);

            Log::info('AI Feedback', [
                'user_id' => Auth::id(),
                'message_id' => $request->message_id,
                'rating' => $request->rating,
                'feedback' => $request->feedback,
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback!'
            ]);
        } catch (\Exception $e) {
            Log::error('Submit Feedback Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback'
            ], 500);
        }
    }
}

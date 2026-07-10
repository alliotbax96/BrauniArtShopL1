<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BuyerChatController extends Controller
{
    /**
     * Инициализация или получение существующего чата
     */
    public function initChat(Request $request)
    {
        try {
            $request->validate([
                'client_name' => 'required|string|max:255',
                'client_email' => 'nullable|email|max:255',
                'client_phone' => 'nullable|string|max:20',
                'current_page' => 'nullable|string|max:500',
                'guest_token' => 'nullable|string|max:255',
            ]);

            $userId = Auth::check() ? Auth::id() : null;
            $guestToken = $request->guest_token ?? session()->get('guest_token');

            if (!$userId && !$guestToken) {
                $guestToken = Str::uuid()->toString();
                session()->put('guest_token', $guestToken);
            }

            // Ищем существующий чат
            $chat = $this->findExistingChat($userId, $guestToken, session()->get('buyer_chat_session_id'));

            if ($chat) {
                $messages = $chat->messages()
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(function ($message) {
                        return $this->formatMessageResponse($message);
                    });

                return response()->json([
                    'success' => true,
                    'chat_id' => $chat->id,
                    'guest_token' => $guestToken,
                    'messages' => $messages,
                    'status' => $chat->status,
                    'admin_connected' => $chat->assigned_admin_id ? true : false
                ]);
            }

            // Создаем новый чат
            $chat = Chat::create([
                'name' => 'Чат с ' . $request->client_name,
                'type' => 'buyer_support',
                'status' => 'waiting',
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'current_page' => $request->current_page ?? 'Неизвестная страница',
                'client_user_id' => $userId,
                'guest_token' => $guestToken,
            ]);

            session()->put('buyer_chat_session_id', $chat->id);

            Log::info('New buyer chat created', [
                'chat_id' => $chat->id,
                'client_name' => $request->client_name,
                'user_id' => $userId,
                'guest_token' => $guestToken,
                'ip' => $request->ip()
            ]);

            event(new \App\Events\NewChatCreated($chat));
            return response()->json([
                'success' => true,
                'chat_id' => $chat->id,
                'guest_token' => $guestToken,
                'messages' => [],
                'status' => 'waiting',
                'admin_connected' => false
            ]);

        } catch (\Exception $e) {
            Log::error('Error initializing chat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка создания чата: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Отправка сообщения от покупателя
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:5000',
                'client_name' => 'sometimes|string|max:255',
                'guest_token' => 'nullable|string|max:255',
            ]);

            // Проверяем доступ к чату
            if (!$this->canAccessChat($chat, $request->guest_token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            // Обновляем информацию о клиенте если нужно
            if ($request->has('client_name') && $request->client_name) {
                $chat->update(['client_name' => $request->client_name]);
            }

            // Определяем ID пользователя (0 для гостей)
            $userId = Auth::check() ? Auth::id() : 0;
            $guestToken = $request->guest_token;

            // Создаем сообщение
            $message = new Message();
            $message->chat_id = $chat->id;
            $message->user_id = $userId;
            $message->guest_token = $guestToken;
            $message->content = $request->input('content');
            $message->is_read = false;
            $message->save();

            // Если чат в ожидании, меняем статус
            if ($chat->status === 'waiting') {
                $chat->update(['status' => 'active']);
            }

            Log::info('Buyer message sent', [
                'message_id' => $message->id,
                'chat_id' => $chat->id,
                'user_id' => $userId,
                'guest_token' => $guestToken,
                'content' => substr($request->input('content'), 0, 50)
            ]);

            // Отправляем событие
            event(new MessageSent($message));

            // Формируем ответ
            $messageData = $this->formatMessageResponse($message);

            return response()->json([
                'success' => true,
                'message' => $messageData
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending buyer message', [
                'chat_id' => $chat->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка отправки сообщения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получение сообщений чата
     */
    public function getMessages(Request $request, Chat $chat)
    {
        try {
            if (!$this->canAccessChat($chat, $request->guest_token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            $messages = $chat->messages()
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return $this->formatMessageResponse($message);
                });

            // Отмечаем как прочитанные
            $userId = Auth::check() ? Auth::id() : 0;
            $guestToken = $request->guest_token;

            Message::where('chat_id', $chat->id)
                ->where('is_read', false)
                ->where(function($query) use ($userId, $guestToken) {
                    if ($userId > 0) {
                        $query->where('user_id', '!=', $userId);
                    } else {
                        $query->where('guest_token', '!=', $guestToken)
                            ->orWhere('user_id', '>', 0);
                    }
                })
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'status' => $chat->status,
                'admin_connected' => $chat->assigned_admin_id ? true : false
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting messages', [
                'chat_id' => $chat->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки сообщений'
            ], 500);
        }
    }

    /**
     * Поиск существующего чата
     */
    private function findExistingChat($userId, $guestToken, $sessionChatId)
    {
        if ($sessionChatId) {
            $chat = Chat::where('id', $sessionChatId)
                ->where('status', '!=', 'closed')
                ->first();
            if ($chat) return $chat;
        }

        if ($userId) {
            $chat = Chat::where('client_user_id', $userId)
                ->where('type', 'buyer_support')
                ->where('status', '!=', 'closed')
                ->latest()
                ->first();
            if ($chat) return $chat;
        }

        if ($guestToken) {
            $chat = Chat::where('guest_token', $guestToken)
                ->where('type', 'buyer_support')
                ->where('status', '!=', 'closed')
                ->latest()
                ->first();
            if ($chat) return $chat;
        }

        return null;
    }

    /**
     * Проверка доступа к чату
     */
    private function canAccessChat(Chat $chat, $guestToken = null): bool
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return true;
        }

        if (Auth::check() && $chat->client_user_id === Auth::id()) {
            return true;
        }

        if ($guestToken && $chat->guest_token === $guestToken) {
            return true;
        }

        if (session()->get('buyer_chat_session_id') == $chat->id) {
            return true;
        }

        return false;
    }

    /**
     * Форматирование ответа сообщения
     */
    private function formatMessageResponse(Message $message): array
    {
        $userData = [
            'id' => 0,
            'name' => 'Гость',
            'avatar' => null
        ];

        if ($message->user_id > 0) {
            // Пытаемся загрузить пользователя
            if (!$message->relationLoaded('user')) {
                $message->load('user');
            }

            if ($message->user) {
                $userData = [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'avatar' => $message->user->avatar ?? null
                ];
            }
        } elseif ($message->chat && $message->chat->client_name) {
            $userData['name'] = $message->chat->client_name;
        }

        return [
            'id' => $message->id,
            'chat_id' => $message->chat_id,
            'user_id' => $message->user_id,
            'guest_token' => $message->guest_token,
            'content' => $message->content,
            'file_path' => $message->file_path,
            'file_name' => $message->file_name,
            'file_type' => $message->file_type,
            'file_size' => $message->file_size,
            'is_read' => $message->is_read,
            'is_edited' => $message->is_edited,
            'created_at' => $message->created_at instanceof \DateTime
                ? $message->created_at->format('Y-m-d H:i:s')
                : $message->created_at,
            'user' => $userData
        ];
    }
}

<?php

namespace App\Http\Controllers\Dashboard\Support;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends BaseController
{
    public function index()
    {
        $this->shareCommonData(); // вызываем один раз

        if (Auth::user()->isAdmin()) {
            // Для администратора: свои чаты + все чаты с type = 'support'
            $chats = Chat::whereHas('users', function ($query) {
                $query->where('user_id', auth()->id());
            })
                ->orWhere('type', 'support')
                ->with(['users', 'messages' => function ($query) {
                    $query->latest()->first();
                }])
                ->get();
        } else {
            // Для обычных пользователей: только свои чаты
            $chats = Chat::whereHas('users', function ($query) {
                $query->where('user_id', auth()->id());
            })
                ->with(['users', 'messages' => function ($query) {
                    $query->latest()->first();
                }])
                ->get();
        }

        $mainView = 'dashboard.support.index';
        return view('dashboard.index', compact('chats', 'mainView'));
    }

    public function ajax(Request $request)
    {
        $user = Auth::user();

        // Получаем чаты пользователя через связь users (таблица chat_user)
        $chats = Chat::whereHas('users', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->select('id', 'name', 'created_at')
            ->get();

        // Если запрос на проверку новых чатов
        if ($request->has('check_new')) {
            $lastCheck = $request->session()->get('last_chat_check', now()->subMinutes(30));

            $newChats = Chat::whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
                ->where('created_at', '>', $lastCheck)
                ->select('id', 'name', 'created_at')
                ->get();

            // Обновляем время последней проверки
            $request->session()->put('last_chat_check', now());

            return response()->json($newChats);
        }

        return response()->json($chats);
    }

    public function show(Chat $chat)
    {
        $this->shareCommonData(); // вызываем один раз
        $chats = Chat::whereHas('users', function($query) {
            $query->where('user_id', auth()->id());
        })->with(['users', 'messages' => function($query) {
            $query->latest()->first();
        }])->get();
        $chat->load(['users', 'messages.user']);
        $mainView = 'dashboard.support.show';
        return view('dashboard.index', compact('chat', 'chats', 'mainView'));
    }

    public function store()
    {
        try {
            // Начинаем транзакцию для атомарности операций
            DB::transaction(function () {
                // Создаём чат
                $chat = Chat::create([
                    'name' => 'Запрос в поддержку',
                    'type' => 'support', // Используем тип 'support' вместо 'private'
                ]);

                // Добавляем текущего пользователя в чат
                if (!$chat->addUser(Auth::user())) {
                    throw new \Exception('Не удалось добавить пользователя в чат');
                }

                // Обновляем название чата, добавляя ID
                $chat->update([
                    'name' => "Запрос в поддержку №{$chat->id}"
                ]);
                session('chatId', $chat->id);
            });
            return redirect(route('dashboard.seller.chat.show', session('chatId')));

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Ошибка БД при создании чата поддержки: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'error' => 'Произошла ошибка при работе с базой данных. Попробуйте ещё раз.'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при создании чата поддержки: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'error' => 'Не удалось создать чат поддержки. Пожалуйста, попробуйте ещё раз.'
            ]);
        }
    }

    public function delete(int $chatId)
    {
        try {
            // Проверяем существование чата
            $chat = Chat::find($chatId);

            if (!$chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Чат не найден'
                ], 404);
            }

            // Проверяем права доступа: пользователь должен быть участником чата
            $isParticipant = $chat->users()
                ->where('user_id', auth()->id())
                ->exists();

            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для удаления этого чата'
                ], 403);
            }

            // Выполняем удаление в транзакции для целостности данных
            DB::transaction(function () use ($chat) {
                // Сначала удаляем все сообщения чата (если есть каскадное удаление — можно пропустить)
                $chat->messages()->delete();

                // Затем удаляем связи пользователей с чатом
                $chat->users()->detach();

                // И только потом удаляем сам чат
                $chat->delete();
            });

            Log::info('Чат удалён успешно', [
                'chat_id' => $chatId,
                'user_id' => auth()->id(),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Чат успешно удалён'
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Ошибка БД при удалении чата: ' . $e->getMessage(), [
                'chat_id' => $chatId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при работе с базой данных'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Неожиданная ошибка при удалении чата: ' . $e->getMessage(), [
                'chat_id' => $chatId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Не удалось удалить чат. Попробуйте ещё раз.'
            ], 500);
        }
    }

    public function checkUserStatus(Request $request, $userId)
    {
        $chatId = $request->input('chat_id');

        try {
            $pusher = app('pusher');

            // Получаем информацию о канале
            $channelName = "presence-chat.{$chatId}";
            $result = $pusher->getChannelInfo($channelName);

            if ($result['status'] === 200) {
                $channelData = json_decode($result['body'], true);

                // Проверяем, есть ли пользователь в списке подключённых
                $isOnline = isset($channelData['users']) &&
                    collect($channelData['users'])->contains('id', $userId);

                return response()->json([
                    'user_id' => $userId,
                    'chat_id' => $chatId,
                    'is_online' => $isOnline,
                    'timestamp' => now()->toDateTimeString()
                ]);
            }

            return response()->json([
                'user_id' => $userId,
                'chat_id' => $chatId,
                'is_online' => false,
                'error' => 'Channel not found or no connection'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error checking user status', [
                'user_id' => $userId,
                'chat_id' => $chatId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'user_id' => $userId,
                'chat_id' => $chatId,
                'is_online' => false,
                'error' => 'Server error'
            ], 500);
        }
    }

}

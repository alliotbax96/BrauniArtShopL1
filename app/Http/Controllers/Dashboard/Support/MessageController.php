<?php

namespace App\Http\Controllers\Dashboard\Support;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\MessagesRead;

class MessageController extends BaseController
{
    public function store(Request $request, Chat $chat)
    {
        $user = Auth::user();

        // Проверка доступа: если пользователь не участник чата
        if (!$chat->users()->where('user_id', $user->id)->exists()) {
            // Если пользователь — администратор, добавляем его в чат
            if ($user->isAdmin()) {
                try {
                    $chat->addUser($user);
                } catch (\Exception $e) {
                    abort(500, 'Не удалось добавить администратора в чат');
                }
            } else {
                // Для обычных пользователей без доступа — запрещаем действие
                abort(403, 'У вас нет доступа к этому чату');
            }
        }

        // Создаём сообщение
        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'content' => $request->input('content'),
            'file_path' => null, // Добавить обработку файлов
            'file_name' => null,
            'file_type' => null
        ]);

        Log::info('Before dispatching MessageSent event', [
            'message_id' => $message->id,
            'chat_id' => $chat->id
        ]);

        event(new MessageSent($message));

        Log::info('After dispatching MessageSent event - event dispatched');

        return response()->json($message);
    }


    public function markAsRead(Chat $chat)
    {
        Message::where('chat_id', $chat->id)
            ->where('is_read', false)
            ->where('user_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        event(new MessagesRead($chat->id, Auth::id()));
        return response()->json(['success' => true]);
    }


    public function upload(Request $request, Chat $chat)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        if (!$chat->users()->where('user_id', auth()->id())->exists()) {
            abort(403);
        }

        $path = $request->file('file')->store('chat_files', 'public');

        return response()->json([
            'success' => true,
            'file_path' => $path,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_type' => $request->file('file')->getMimeType()
        ]);
    }
}

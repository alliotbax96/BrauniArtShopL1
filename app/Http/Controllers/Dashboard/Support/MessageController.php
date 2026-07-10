<?php

namespace App\Http\Controllers\Dashboard\Support;

use App\Events\MessageSent;
use App\Events\MessagesRead;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends BaseController
{
    /**
     * Отправка сообщения в чат
     */
    public function store(Request $request, Chat $chat)
    {
        $user = Auth::user();

        // Проверка доступа
        if (!$chat->users()->where('user_id', $user->id)->exists()) {
            if ($user->isAdmin()) {
                try {
                    $chat->addUser($user);
                } catch (\Exception $e) {
                    abort(500, 'Не удалось добавить администратора в чат');
                }
            } else {
                abort(403, 'У вас нет доступа к этому чату');
            }
        }

        // Создаём сообщение
        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'content' => $request->input('content', ''),
            'file_path' => $request->input('file_path'),
            'file_name' => $request->input('file_name'),
            'file_type' => $request->input('file_type'),
            'file_size' => $request->input('file_size'),
            'is_read' => false
        ]);

        // Загружаем отношение user
        $message->load('user');

        Log::info('Сообщение создано', [
            'message_id' => $message->id,
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);

        // Отправляем событие
        event(new MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => $message->toArray()
        ]);
    }

    /**
     * Обновление сообщения
     */
    public function update(Request $request, Message $message)
    {
        // Проверяем, что пользователь является автором сообщения
        if ($message->user_id !== Auth::id()) {
            abort(403, 'Вы не можете редактировать чужие сообщения');
        }

        // Проверяем, что сообщение не старше 24 часов
        if ($message->created_at->diffInHours(now()) > 24) {
            abort(400, 'Сообщение можно редактировать только в течение 24 часов');
        }

        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $message->update([
            'content' => $request->input('content'),
            'is_edited' => true
        ]);

        $message->load('user');

        Log::info('Сообщение отредактировано', [
            'message_id' => $message->id,
            'user_id' => Auth::id()
        ]);

        // Отправляем событие об обновлении
        event(new MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => $message->toArray()
        ]);
    }

    /**
     * Удаление сообщения
     */
    public function destroy(Message $message)
    {
        // Проверяем, что пользователь является автором сообщения
        if ($message->user_id !== Auth::id()) {
            abort(403, 'Вы не можете удалять чужие сообщения');
        }

        // Если есть файл, удаляем его
        if ($message->file_path && Storage::exists($message->file_path)) {
            Storage::delete($message->file_path);
        }

        $messageId = $message->id;
        $chatId = $message->chat_id;

        $message->delete();

        Log::info('Сообщение удалено', [
            'message_id' => $messageId,
            'chat_id' => $chatId,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message_id' => $messageId
        ]);
    }

    /**
     * Отметка сообщений как прочитанных
     */
    public function markAsRead(Chat $chat)
    {
        $updatedCount = Message::where('chat_id', $chat->id)
            ->where('is_read', false)
            ->where('user_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        if ($updatedCount > 0) {
            event(new MessagesRead($chat->id, Auth::id()));
        }

        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount
        ]);
    }

    /**
     * Загрузка файла в чат
     */
    public function upload(Request $request, Chat $chat)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        if (!$chat->users()->where('user_id', auth()->id())->exists()) {
            if (auth()->user()->isAdmin()) {
                try {
                    $chat->addUser(auth()->user());
                } catch (\Exception $e) {
                    abort(500, 'Не удалось добавить администратора в чат');
                }
            } else {
                abort(403, 'У вас нет доступа к этому чату');
            }
        }

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('chat_files', $fileName);

        Log::info('Файл загружен в чат', [
            'chat_id' => $chat->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize()
        ]);

        return response()->json([
            'success' => true,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize()
        ]);
    }

    /**
     * Скачивание файла из чата
     */
    public function downloadFile(Message $message)
    {
        $chat = $message->chat;
        if (!$chat->users()->where('user_id', auth()->id())->exists()) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'У вас нет доступа к этому файлу');
            }
        }

        if (!$message->file_path || !Storage::exists($message->file_path)) {
            abort(404, 'Файл не найден');
        }

        Log::info('Скачивание файла чата', [
            'message_id' => $message->id,
            'file_path' => $message->file_path,
            'user_id' => auth()->id()
        ]);

        return Storage::download($message->file_path, $message->file_name);
    }

    /**
     * Просмотр изображения из чата
     */
    public function showImage(Message $message)
    {
        $chat = $message->chat;
        if (!$chat->users()->where('user_id', auth()->id())->exists()) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'У вас нет доступа к этому изображению');
            }
        }

        if (!$message->file_path || !Storage::exists($message->file_path)) {
            abort(404, 'Изображение не найдено');
        }

        $mimeType = Storage::mimeType($message->file_path);
        if (!str_starts_with($mimeType, 'image/')) {
            abort(400, 'Файл не является изображением');
        }

        return response()->file(Storage::path($message->file_path));
    }
}

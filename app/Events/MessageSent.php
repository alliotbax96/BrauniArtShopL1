<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        // Загружаем отношение user
        $this->message = $message->load('user');

        Log::debug('MessageSent: Событие создано', [
            'message_id' => $this->message->id,
            'chat_id' => $this->message->chat_id,
            'user_id' => $this->message->user_id,
            'content' => substr($this->message->content, 0, 50),
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    public function broadcastOn(): array
    {
        $channelName = 'chat.' . $this->message->chat_id;

        Log::info('MessageSent: Вещание на канал', [
            'channel' => $channelName,
            'message_id' => $this->message->id
        ]);

        return [new Channel($channelName)];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Данные для вещания
     * ВАЖНО: Структура должна соответствовать тому, что ожидает JavaScript
     */
    public function broadcastWith(): array
    {
        $data = [
            'message' => [
                'id' => $this->message->id,
                'chat_id' => $this->message->chat_id,
                'user_id' => $this->message->user_id,
                'content' => $this->message->content,
                'file_path' => $this->message->file_path,
                'file_name' => $this->message->file_name,
                'file_type' => $this->message->file_type,
                'is_read' => $this->message->is_read,
                'created_at' => $this->message->created_at instanceof \DateTime
                    ? $this->message->created_at->format('Y-m-d H:i:s')
                    : $this->message->created_at,
                'updated_at' => $this->message->updated_at instanceof \DateTime
                    ? $this->message->updated_at->format('Y-m-d H:i:s')
                    : $this->message->updated_at,
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'avatar' => $this->message->user->avatar ?? null
                ]
            ]
        ];

        Log::info('MessageSent: Данные подготовлены', [
            'message_id' => $this->message->id,
            'user_id' => $this->message->user_id,
            'has_file' => !empty($this->message->file_path),
            'data_keys' => array_keys($data['message']),
            'timestamp' => now()->toDateTimeString()
        ]);

        return $data;
    }
}

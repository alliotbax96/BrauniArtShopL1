<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    public $message;

    public function __construct(Message $message)
    {
        // Отладочный лог: создание события
        Log::debug('MessageSent: Конструктор вызван', [
            'message_id' => $message->id ?? 'unknown',
            'chat_id' => $message->chat_id ?? 'unknown',
            'user_id' => $message->user_id ?? 'unknown',
            'timestamp' => now()->toDateTimeString()
        ]);

        $this->message = $message->load('user');

        // Дополнительный лог после загрузки отношений
        Log::debug('MessageSent: Отношения загружены (user)', [
            'message_with_user' => [
                'id' => $this->message->id,
                'content' => substr($this->message->content, 0, 50) . (strlen($this->message->content) > 50 ? '...' : ''),
                'user_name' => $this->message->user->name ?? 'unknown'
            ]
        ]);
    }

    public function broadcastOn(): array
    {
        $channelName = 'chat.' . $this->message->chat_id;

        // Лог: информация о канале вещания
        Log::info('MessageSent: Подготовка к вещанию на канале', [
            'channel' => $channelName,
            'message_id' => $this->message->id,
            'chat_id' => $this->message->chat_id,
            'broadcast_data' => [
                'user_id' => $this->message->user_id,
                'user_name' => $this->message->user->name ?? 'unknown'
            ],
            'timestamp' => now()->toDateTimeString()
        ]);

        return [new Channel($channelName)];
    }

    public function broadcastAs(): string
    {
        $eventName = 'message.sent';

        // Лог: используемое имя события
        Log::debug('MessageSent: Используется кастомное имя события', [
            'event_name' => $eventName,
            'message_id' => $this->message->id ?? 'unknown',
            'timestamp' => now()->toDateTimeString()
        ]);

        return $eventName;
    }

    /**
     * Опционально: метод для получения данных вещания с отладкой
     */
    public function broadcastWith()
    {
        $broadcastData = [
            'message' => [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'created_at' => $this->message->created_at,
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'avatar' => $this->message->user->avatar ?? null
                ]
            ]
        ];

        // Лог: данные, которые будут отправлены через WebSocket
        Log::info('MessageSent: Данные для вещания подготовлены', [
            'broadcast_data' => $broadcastData,
            'channel' => 'chat.' . $this->message->chat_id,
            'event' => $this->broadcastAs(),
            'timestamp' => now()->toDateTimeString()
        ]);

        return $broadcastData;
    }
}

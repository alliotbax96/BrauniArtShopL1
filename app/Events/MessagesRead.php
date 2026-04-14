<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesRead implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $chatId;
    public $readerUserId;

    public function __construct($chatId, $readerUserId)
    {
        $this->chatId = $chatId;
        $this->readerUserId = $readerUserId;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->chatId);
    }

    public function broadcastAs()
    {
        return 'messages.read';
    }

    public function broadcastWith()
    {
        return [
            'chat_id' => $this->chatId,
            'reader_user_id' => $this->readerUserId,
            'timestamp' => now()->toDateTimeString()
        ];
    }
}

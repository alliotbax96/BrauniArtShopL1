<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatCreated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat->load(['users', 'messages' => function($q) {
            $q->latest()->first();
        }, 'assignedAdmin', 'client']);
    }

    public function broadcastOn(): array
    {
        return [new Channel('admin.chats')];
    }

    public function broadcastAs(): string
    {
        return 'chat.created';
    }
}

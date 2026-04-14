<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    Log::debug('Channel authorization check', [
        'user_id' => $user->id,
        'chat_id' => $chatId,
        'authorized' => $user->can('participate-in-chat', Chat::find($chatId)),
        'timestamp' => now()->toDateTimeString()
    ]);

    return $user->can('participate-in-chat', Chat::find($chatId));
});

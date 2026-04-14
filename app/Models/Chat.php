<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Message;

class Chat extends Model
{
    protected $fillable = ['name', 'type'];
    /**
     * Связь «многие‑ко‑многим» с пользователями через вспомогательную таблицу chat_user
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_user');
    }

    /**
     * Связь «один‑ко‑многим» с сообщениями
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class,'chat_id');
    }

    /**
     * Проверяет, состоит ли пользователь в чате
     *
     * @param int|User $user ID пользователя или экземпляр модели User
     * @return bool
     */
    public function hasUser($user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->users()
            ->where('users.id', $userId)
            ->exists();
    }

    /**
     * Добавляет пользователя в чат (если его там ещё нет)
     *
     * @param int|User $user ID пользователя или экземпляр модели User
     * @return bool
     */
    public function addUser($user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        if (!$this->hasUser($userId)) {
            $this->users()->attach($userId);
            return true;
        }

        return false; // Пользователь уже в чате
    }

    /**
     * Удаляет пользователя из чата
     *
     * @param int|User $user ID пользователя или экземпляр модели User
     * @return bool
     */
    public function removeUser($user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($this->hasUser($userId)) {
            $this->users()->detach($userId);
            return true;
        }

        return false; // Пользователя не было в чате
    }

    /**
     * Получает участников чата с их статусами онлайн (требуется связь status в модели User)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParticipantsWithStatus()
    {
        return $this->users()
            ->with(['status' => function ($query) {
                $query->select('user_id', 'is_online', 'last_activity');
            }])
            ->get();
    }

    /**
     * Получает последнее сообщение пользователя в чате
     *
     * @param int|User $user ID пользователя или экземпляр модели User
     * @return \App\Models\Message|null
     */
    public function getLastMessageFromUser($user): ?Message
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->messages()
            ->where('user_id', $userId)
            ->latest()
            ->first();
    }

    /**
     * Считает количество непрочитанных сообщений для пользователя в чате
     *
     * @param int|User $user ID пользователя или экземпляр модели User
     * @return int
     */
    public function getUnreadMessagesCountForUser($user): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->messages()
            ->where('is_read', false)
            ->where('user_id', '!=', $userId) // Исключаем сообщения самого пользователя
            ->count();
    }
}

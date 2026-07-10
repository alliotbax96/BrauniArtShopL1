<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
        'client_name',
        'client_email',
        'client_phone',
        'ip_address',
        'country',
        'city',
        'region',
        'current_page',
        'user_agent',
        'client_user_id',
        'assigned_admin_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Связь с пользователями чата
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_user');
    }

    /**
     * Связь с сообщениями
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_id');
    }

    /**
     * Назначенный администратор
     */
    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    /**
     * Клиент (зарегистрированный пользователь)
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    /**
     * Последнее сообщение в чате
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'chat_id')->latest();
    }

    /**
     * Проверяет, состоит ли пользователь в чате
     */
    public function hasUser($user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->users()->where('users.id', $userId)->exists();
    }

    /**
     * Добавляет пользователя в чат
     */
    public function addUser($user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        if (!$this->hasUser($userId)) {
            $this->users()->attach($userId);
            return true;
        }

        return false;
    }

    /**
     * Удаляет пользователя из чата
     */
    public function removeUser($user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($this->hasUser($userId)) {
            $this->users()->detach($userId);
            return true;
        }

        return false;
    }

    /**
     * Получает количество непрочитанных сообщений для пользователя
     */
    public function getUnreadMessagesCountForUser($user): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->messages()
            ->where('is_read', false)
            ->where('user_id', '!=', $userId)
            ->count();
    }
}

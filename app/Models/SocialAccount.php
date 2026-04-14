<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'token',
        'refresh_token',
        'expires_in',
        'avatar',
    ];

    /**
     * Получить все аккаунты пользователя, сгруппированные по провайдерам
     *
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public static function getAllGroupedByProvider(int $userId)
    {
        return self::where('user_id', $userId)
            ->get()
            ->groupBy('provider');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

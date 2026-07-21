<?php
// app/Models/View.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class View extends Model
{
    protected $fillable = [
        'viewable_type',
        'viewable_id',
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'referer_url',
        'metadata',
        'viewed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'viewed_at' => 'datetime',
    ];

    public $timestamps = false;

    // Полиморфная связь с просматриваемой моделью
    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    // Связь с пользователем
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope для просмотров за период
    public function scopeLastDays($query, int $days = 7)
    {
        return $query->where('viewed_at', '>=', now()->subDays($days));
    }

    // Scope для популярных товаров
    public function scopePopular($query, int $limit = 10)
    {
        return $query->selectRaw('viewable_type, viewable_id, COUNT(*) as views_count')
            ->groupBy('viewable_type', 'viewable_id')
            ->orderByDesc('views_count')
            ->limit($limit);
    }
}

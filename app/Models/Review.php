<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'reviewable_id',
        'reviewable_type',
        'estimation',
        'comment',
    ];

    protected $casts = [
        'estimation' => 'integer',
    ];

    // Отношения
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Полиморфное отношение к любой сущности, которая может иметь отзывы
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }
}

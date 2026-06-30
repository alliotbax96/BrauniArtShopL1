<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBookmark extends Model
{
    protected $table = 'user_bookmarks';

    protected $fillable = [
        'user_id',
        'book_id',
        'chapter_id',
        'position',
        'scroll_position',
        'audio_position',
        'playback_speed',
        'note',
    ];

    protected $casts = [
        'position' => 'integer',
        'scroll_position' => 'integer',
        'audio_position' => 'integer',
        'playback_speed' => 'float',
    ];

    // Отношения
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(BookChapter::class);
    }
}

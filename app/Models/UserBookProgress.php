<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBookProgress extends Model
{
    protected $table = 'user_book_progress';

    protected $fillable = [
        'user_id',
        'book_id',
        'completed_chapters',
        'total_chapters',
        'progress_percentage',
        'total_reading_time',
        'last_read_at',
        'completed_at',
        'current_chapter_id',
        'current_position',
        'playback_speed',
        'last_read_at',
        'completed_at',
    ];

    protected $casts = [
        'completed_chapters' => 'integer',
        'total_chapters' => 'integer',
        'progress_percentage' => 'float',
        'total_reading_time' => 'integer',
        'last_read_at' => 'datetime',
        'completed_at' => 'datetime',
        'current_position' => 'integer',
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

    // Методы
    public function updateProgress(): void
    {
        $book = $this->book;
        $totalChapters = $book->getChapterCount();

        $this->total_chapters = $totalChapters;
        $this->progress_percentage = $totalChapters > 0
            ? ($this->completed_chapters / $totalChapters) * 100
            : 0;

        if ($this->completed_chapters >= $totalChapters && $totalChapters > 0) {
            $this->completed_at = now();
        }

        $this->save();
    }
}

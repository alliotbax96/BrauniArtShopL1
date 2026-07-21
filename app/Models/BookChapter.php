<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BookChapter extends Model
{
    protected $table = 'book_chapters';

    protected $fillable = [
        'book_id',
        'title',
        'order',
        'description',
        'content',
        'audio_file_path',
        'duration',
        'file_size',
        'status',
        'is_free_preview',
    ];

    protected $casts = [
        'order' => 'integer',
        'duration' => 'integer',
        'file_size' => 'integer',
        'is_free_preview' => 'boolean',
    ];

    // Добавляем виртуальные поля к сериализации
    protected $appends = ['characters_count', 'audio_url'];

    // Отношения
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Методы
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Получить временную ссылку на аудиофайл
     */
    public function getAudioUrl(): ?string
    {
        if ($this->audio_file_path) {
            return Storage::disk('s3')->temporaryUrl(
                $this->audio_file_path,
                now()->addHours(24)
            );
        }
        return null;
    }

    /**
     * Форматированная длительность (минуты:секунды)
     */
    public function getFormattedDuration(): string
    {
        if (!$this->duration) return '0:00';

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Аксессор для characters_count
     */
    public function getCharactersCountAttribute(): int
    {
        if (isset($this->attributes['characters_count']) && $this->attributes['characters_count'] !== null) {
            return (int)$this->attributes['characters_count'];
        }

        if (!empty($this->attributes['content'])) {
            return mb_strlen(strip_tags($this->attributes['content']));
        }

        return 0;
    }

    /**
     * Аксессор для audio_url
     */
    public function getAudioUrlAttribute(): ?string
    {
        return $this->getAudioUrl();
    }

    /**
     * Читабельный размер главы
     */
    public function getReadableSize(): string
    {
        if ($this->relationLoaded('book') && $this->book && $this->book->isAudiobook()) {
            return $this->getFormattedDuration();
        }

        $chars = $this->characters_count;

        if ($chars >= 1000) {
            return round($chars / 1000, 1) . ' тыс. зн.';
        }

        return $chars . ' зн.';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Storage;

class Book extends Model
{
    use SoftDeletes;

    protected $table = 'books';

    protected $casts = [
        'is_active' => 'boolean',
        'publication_year' => 'integer',
        'pages_count' => 'integer',
        'total_duration' => 'integer',
        'price' => 'decimal:2',
    ];

    protected $fillable = [
        'type',
        'name',
        'image_path',
        'genre_id',
        'status',
        'moderation_status',
        'is_active',
        'author',
        'publisher',
        'publication_year',
        'isbn',
        'language',
        'pages_count',
        'annotation',
        'narrator',
        'total_duration',
        'book_seller_id',
        'price', // Добавляем поле для цены
    ];

    // Отношения

    public function chapters(): HasMany
    {
        return $this->hasMany(BookChapter::class)->orderBy('order');
    }

    public function publishedChapters(): HasMany
    {
        return $this->chapters()->where('status', 'published');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(UserBookmark::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserBookProgress::class);
    }

    public function genre(): HasOne
    {
        return $this->hasOne(ProductGroup::class, 'id', 'genre_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'book_seller_id', 'id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 'approved');
    }

    public function scopeEbooks(Builder $query): Builder
    {
        return $query->where('type', 'ebook');
    }

    public function scopeAudiobooks(Builder $query): Builder
    {
        return $query->where('type', 'audiobook');
    }

    public function scopeComplete(Builder $query): Builder
    {
        return $query->where('status', 'complete');
    }

    // Методы
    public function isComplete(): bool
    {
        return $this->status === 'complete';
    }

    public function isEbook(): bool
    {
        return $this->type === 'ebook';
    }

    public function isAudiobook(): bool
    {
        return $this->type === 'audiobook';
    }

    public function getTotalDuration(): int
    {
        if ($this->isAudiobook()) {
            return $this->publishedChapters()->sum('duration');
        }
        return 0;
    }

    public function getChapterCount(): int
    {
        return $this->publishedChapters()->count();
    }

    public function getUserProgress(int $userId): ?UserBookProgress
    {
        return $this->progress()->where('user_id', $userId)->first();
    }

    public function getUserBookmark(int $userId): ?UserBookmark
    {
        return $this->bookmarks()->where('user_id', $userId)->first();
    }

    public function getPreviewChapters()
    {
        return $this->publishedChapters()->where('is_free_preview', true)->get();
    }

    public function updateTotalDuration(): void
    {
        if ($this->isAudiobook()) {
            $this->total_duration = $this->getTotalDuration();
            $this->save();
        }
    }

    // Аксессоры
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->total_duration) return '0:00:00';

        $hours = floor($this->total_duration / 3600);
        $minutes = floor(($this->total_duration % 3600) / 60);
        $seconds = $this->total_duration % 60;

        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }

    // Методы для работы с продуктом (аналогично модели Quest)
    public function getProductId(): int
    {
        return $this->id;
    }

    public function getProductName(): string
    {
        return $this->name;
    }

    public function getProductPrice(): string
    {
        if ($this->price) {
            return number_format($this->price, 0, '', ' ');
        }

        // Если книга бесплатная или цена не указана
        if ($this->isComplete() && $this->publishedChapters()->count() > 0) {
            return 'Бесплатно';
        }

        return 'Скоро';
    }

    public function getMainImage(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        // Проверяем, существует ли файл
        if (!Storage::disk('s3')->exists($this->image_path)) {
            // Опционально: очистить запись в БД, если файл удалён вручную из S3
            $this->update(['image_path' => null]);
            return null;
        }

        return Storage::disk('s3')->url($this->image_path);
    }

    public function getProductImages()
    {
        $images = [];

        if ($this->cover_image_path) {
            $images[] = [
                'url' => $this->cover_image_path,
                'url_full' => Storage::disk('s3')->url($this->cover_image_path),
            ];
        }

        return !empty($images) ? $images : null;
    }

    public function getProductSellerId(): int
    {
        // Для книг может не быть продавца, возвращаем 0 или null
        return $this->book_seller_id ?? 0;
    }

    public function getSeller()
    {
        return $this->seller ?? null;
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function getProductReviews()
    {
        return $this->reviews()->with('user')->latest()->get();
    }

    public function getProductEstimation(): float
    {
        return $this->reviews()->avg('estimation') ?? 0;
    }

    public function isNew(): bool
    {
        return $this->created_at >= now()->subWeek();
    }

    // Дополнительные методы специфичные для книг
    public function getBookTypeLabel(): string
    {
        return $this->isEbook() ? 'Электронная книга' : 'Аудиокнига';
    }

    public function getBookTypeBadgeClass(): string
    {
        return $this->isEbook() ? 'bg-primary' : 'bg-success';
    }

    public function getFormattedPagesCount(): string
    {
        if ($this->isEbook() && $this->pages_count) {
            return number_format($this->pages_count, 0, '', ' ') . ' стр.';
        }
        return '';
    }

    public function getFormattedPublicationYear(): string
    {
        return $this->publication_year ? (string)$this->publication_year : 'Не указан';
    }

    public function getGenreName(): string
    {
        return $this->genre ? $this->genre->name : 'Не указан';
    }

    public function getProductGroup(): ProductGroup
    {
        return $this->genre ? $this->genre : 'Не указан';
    }

    /**
     * Получить общий размер книги
     * Для электронных книг - сумма символов/страниц (число)
     * Для аудиокниг - суммарная длительность в формате времени
     *
     * @return string|int
     */
    public function getTotalSize()
    {
        if ($this->isEbook()) {
            return $this->getTotalCharactersCount();
        }

        if ($this->isAudiobook()) {
            return $this->getFormattedTotalDuration();
        }

        return 0;
    }

    /**
     * Получить общее количество символов во всех опубликованных главах
     *
     * @return int
     */
    public function getTotalCharactersCount(): int
    {
        return $this->publishedChapters()
         ->get()
         ->sum(function($chapter) {
             return mb_strlen(strip_tags($chapter->content));
         });
    }

    /**
     * Получить общее количество страниц (примерно)
     *
     * @return int
     */
    public function getTotalPagesCount(): int
    {
        $totalChars = $this->getTotalCharactersCount();

        // Примерно 1800-2000 знаков на страницу (стандартная страница А4)
        $charsPerPage = 1800;

        return (int)ceil($totalChars / $charsPerPage);
    }

    /**
     * Получить форматированную общую длительность для аудиокниг
     *
     * @return string
     */
    public function getFormattedTotalDuration(): string
    {
        $totalSeconds = $this->getTotalDuration();

        if ($totalSeconds <= 0) {
            return '0:00:00';
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        if ($hours > 0) {
            return sprintf('%d ч %d мин', $hours, $minutes);
        }

        if ($minutes > 0) {
            return sprintf('%d мин %d сек', $minutes, $seconds);
        }

        return sprintf('%d сек', $seconds);
    }

    /**
     * Получить читабельный размер книги с единицей измерения
     *
     * @return string
     */
    public function getReadableSize(): string
    {
        if ($this->isEbook()) {
            $chars = $this->getTotalCharactersCount();

            if ($chars >= 1000000) {
                return round($chars / 1000000, 1) . ' млн зн.';
            }

            if ($chars >= 1000) {
                return round($chars / 1000, 1) . ' тыс. зн.';
            }

            return $chars . ' зн.';
        }

        if ($this->isAudiobook()) {
            return $this->getFormattedTotalDuration();
        }

        return 'Н/Д';
    }

    /**
     * Получить общий размер файлов для аудиокниги
     *
     * @return string
     */
    public function getTotalAudioSize(): string
    {
        if (!$this->isAudiobook()) {
            return 'Н/Д';
        }

        $totalBytes = $this->publishedChapters()->sum('file_size');

        if ($totalBytes >= 1073741824) {
            return round($totalBytes / 1073741824, 1) . ' ГБ';
        }

        if ($totalBytes >= 1048576) {
            return round($totalBytes / 1048576, 1) . ' МБ';
        }

        if ($totalBytes >= 1024) {
            return round($totalBytes / 1024, 1) . ' КБ';
        }

        return $totalBytes . ' Б';
    }

    /**
     * Получить полную информацию о размере книги
     *
     * @return array
     */
    public function getSizeInfo(): array
    {
        $info = [
            'type' => $this->type,
            'is_ebook' => $this->isEbook(),
            'is_audiobook' => $this->isAudiobook(),
        ];

        if ($this->isEbook()) {
            $info['characters_count'] = $this->getTotalCharactersCount();
            $info['pages_count'] = $this->getTotalPagesCount();
            $info['readable_size'] = $this->getReadableSize();
            $info['chapters_count'] = $this->getChapterCount();
        }

        if ($this->isAudiobook()) {
            $info['total_duration_seconds'] = $this->getTotalDuration();
            $info['formatted_duration'] = $this->getFormattedTotalDuration();
            $info['readable_size'] = $this->getReadableSize();
            $info['audio_file_size'] = $this->getTotalAudioSize();
            $info['chapters_count'] = $this->getChapterCount();
        }

        return $info;
    }
}

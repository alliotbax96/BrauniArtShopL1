<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Storage;

class Quest extends Model
{
    protected $table = 'quests';
    protected $fillable = [
        'seller_id',
        'title',
        'address',
        'station',
        'description',
        'image_path',
        'type',
        'features',
        'difficulty',
        'fear_level',
        'duration',
        'min_age',
        'base_price',
        'base_player_count',
        'additional_player_price',
        'min_players',
        'max_players'
    ];

    protected $casts = [
        'features' => 'array'
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function timeslots()
    {
        return $this->hasMany(Timeslot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function AdditionalServices() {
        return $this->hasMany(AdditionalService::class, 'quest_id', 'id');
    }

    public function getFeatures(): array
    {
        return $this->features ?? [];
    }

    /**
     * Метод для фильтрации квестов по параметрам
     */
    public function scopeFilter($query, array $filters)
    {
        // Поиск по названию
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        // Фильтр по типу квеста
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Фильтр по сложности
        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        // Фильтр по уровню страха
        if (!empty($filters['fear_level'])) {
            $query->where('fear_level', $filters['fear_level']);
        }

        // Фильтр по минимальному возрасту
        if (!empty($filters['min_age'])) {
            $query->where('min_age', '>=', $filters['min_age']);
        }

        // Фильтр по продолжительности
        if (!empty($filters['duration'])) {
            $query->where('duration', $filters['duration']);
        }

        // Фильтр по количеству игроков (минимум)
        if (!empty($filters['min_players'])) {
            $query->where('max_players', '>=', $filters['min_players']);
        }

        // Фильтр по количеству игроков (максимум)
        if (!empty($filters['max_players'])) {
            $query->where('min_players', '<=', $filters['max_players']);
        }

        // Фильтр по цене (минимум)
        if (!empty($filters['min_price'])) {
            $query->where('base_price', '>=', $filters['min_price']);
        }

        // Фильтр по цене (максимум)
        if (!empty($filters['max_price'])) {
            $query->where('base_price', '<=', $filters['max_price']);
        }

        return $query;
    }

    public function getProductId(): int
    {
        return $this->id;
    }

    public function getProductName(): string
    {
        return $this->title;
    }

    public function getProductPrice(): string
    {
        return 'от ' . number_format($this->base_price, 0, '', ' ');
    }

    public function getProductImages()
    {
        // Получаем список файлов в папке
        $files = Storage::disk('s3')->files('uploads/quests/'.$this->image_path);

        // Фильтруем только изображения
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $images = array_filter($files, function ($file) use ($imageExtensions) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return in_array($extension, $imageExtensions);
        });

        if (!empty($images)) {
            // Берём первый файл из списка
            $result = [];
            foreach ($images as $image) {
                $result[] = [
                    'url' => $image,
                ];
            }
            return $result;
        } else {
            return null;
        }
    }

    public function getMainImage(): ?string
    {
        // Получаем список файлов в папке
        $files = Storage::disk('s3')->files('uploads/quests/'.$this->image_path);

        // Фильтруем только изображения
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $images = array_filter($files, function ($file) use ($imageExtensions) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return in_array($extension, $imageExtensions);
        });

        if (!empty($images)) {
            // Берём первый файл из списка
            $firstImagePath = reset($images);
            // Генерируем публичный URL
            return Storage::disk('s3')->url($firstImagePath);
        } else {
            return null;
        }
    }

    public function getProductSellerId(): int
    {
        return $this->seller_id;
    }

    public function getSeller()
    {
        return $this->seller;
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Методы для получения статистики
    public function getProductReviews()
    {
        return $this->reviews()->with('user')->latest()->get();
    }

    public function isNew(): bool
    {
        return $this->created_at >= now()->subWeek();
    }

    public function getProductEstimation(): float
    {
        return $this->reviews()->avg('estimation') ?? 0;
    }
}

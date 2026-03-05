<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProductPrice extends Model
{
    protected $table = 'productPrices'; // Убедитесь, что имя таблицы верно

    // Массово заполняемые поля
    protected $fillable = [
        'productId',
        'price',
        'priceType', // "Оптовая цена" или "Розничная цена"
        // другие поля...
    ];

    // Кастинг атрибутов (опционально)
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Фильтр по типу цены (price_type)
     *
     * @param Builder $query
     * @param string|null $type "Оптовая цена", "Розничная цена" или null
     * @return Builder
     */
    public function scopeByPriceType(Builder $query, ?string $type)
    {
        if (is_null($type)) {
            return $query; // Если тип не указан — не фильтруем
        }
        return $query->where('priceType', $type);
    }

    /**
     * Получение оптовых цен
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWholesale(Builder $query)
    {
        return $query->byPriceType('Оптовая цена');
    }

    /**
     * Получение розничных цен
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRetail(Builder $query)
    {
        return $query->byPriceType('Розничная цена');
    }

    /**
     * Проверка, является ли цена оптовой
     *
     * @return bool
     */
    public function isWholesale(): bool
    {
        return $this->price_type === 'Оптовая цена';
    }

    /**
     * Проверка, является ли цена розничной
     *
     * @return bool
     */
    public function isRetail(): bool
    {
        return $this->price_type === 'Розничная цена';
    }
}

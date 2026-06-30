<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductGroup extends Model
{
    protected $table = 'productGroups';

    protected $fillable = [
        'name',
        'image',
        'parent_id',
        'ShopMode'
    ];

    /**
     * Связь: группа может иметь родительскую группу
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class, 'parent_id');
    }

    public function ShopModeInfo(): BelongsTo {
        return $this->belongsTo(ShopMode::class, 'ShopMode');
    }

    /**
     * Связь: у группы могут быть дочерние группы
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProductGroup::class, 'parent_id');
    }

    /**
     * Рекурсивная связь для получения всех дочерних групп
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Связь с продуктами
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'productGroupId', 'id');
    }

    /**
     * Получение корневых групп (без родителя)
     */
    public static function rootGroups()
    {
        return static::whereNull('parent_id');
    }

    /**
     * Проверка, является ли группа корневой
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }
}

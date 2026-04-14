<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductGroup extends Model
{
    protected $table = 'productGroups'; // Убедитесь, что имя таблицы верно

    // Массово заполняемые поля (при необходимости)
    protected $fillable = [
        'id',
        'name',
        'image',
        'parent_id',
        // другие поля...
    ];

    /**
     * Связь: группа может иметь родительскую группу
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class, 'parent_id');
    }

    /**
     * Связь: у группы могут быть дочерние группы
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProductGroup::class, 'parent_id');
    }

    /**
     * Фильтр по parent_id
     *
     * @param int|null $parentId ID родительской группы или null для корневых групп
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function byParentId($parentId = null)
    {
        return static::query()->where('parent_id', $parentId);
    }

    /**
     * Получение корневых групп (без родителя)
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function rootGroups()
    {
        return static::byParentId(null);
    }

    public static function NoRootGroups()
    {
//        return static::byParentId(null);
        return static::whereNotNull('parent_id');
    }

    public function childrenRecursive()
    {
        return $this->hasMany(self::class, 'parent_id')->with('childrenRecursive');
    }

    /**
     * Получение дочерних групп для указанного родителя
     *
     * @param int $parentId ID родительской группы
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function childGroups(int $parentId)
    {
        return static::byParentId($parentId);
    }

    public function getName(){
        return $this->name;
    }

    public function comission(){
        return $this->belongsTo(Comission::class, 'id', 'product_group_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';
    protected $fillable = ['id', 'name', 'description', 'type', 'permissions'];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Проверяет, имеет ли группа право на действие
     * @param string $permission Название права
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        // Если permissions пустое или не задано — все права разрешены
        if (empty($this->permissions)) {
            return true;
        }

        // Если permissions заполнено — проверяем наличие конкретного права
        return isset($this->permissions[$permission]) && $this->permissions[$permission];
    }

    /**
     * Возвращает все доступные права группы
     * @return array
     */
    public function getAllPermissions(): array
    {
        // Если permissions пустое — возвращаем все возможные права
        if (empty($this->permissions)) {
            return $this->getAllPossiblePermissions();
        }

        // Иначе — только те, что указаны в permissions
        return array_keys(array_filter($this->permissions));
    }

    /**
     * Список всех возможных прав в системе
     * Можно вынести в отдельный класс/конфигурацию
     * @return array
     */
    private function getAllPossiblePermissions(): array
    {
        return [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_orders',
            'processing_orders',
            'view_finance',
            'view_settings',
            'edit_settings',
        ];
    }
}

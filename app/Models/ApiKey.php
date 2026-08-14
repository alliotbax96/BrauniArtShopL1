<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = [
        'name',
        'service_text_logo',
        'key',
        'is_active',
        'expires_at',
        'service_name',
        'service_logo',
        'primary_color',
        'secondary_color',
        'text_color',
        'bg_color',
        'company_name',
        'inn',
        'return_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function isValid(): bool
    {
        return $this->is_active && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Получить стили для брендирования
     */
    public function getStyles(): string
    {
        return "
            --primary-color: {$this->primary_color};
            --secondary-color: {$this->secondary_color};
            --text-color: {$this->text_color};
            --bg-color: {$this->bg_color};
        ";
    }

    /**
     * Получить имя сервиса (если не указано, используется название API ключа)
     */
    public function getServiceNameAttribute($value)
    {
        return $value ?? $this->name;
    }
}

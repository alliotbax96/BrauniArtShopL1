<?php
// app/Models/BudgetTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'category',
        'amount',
        'description',
        'notes',
        'balance_before',
        'balance_after',
        'manager_balance_before',
        'manager_balance_after',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'manager_balance_before' => 'decimal:2',
        'manager_balance_after' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Получить название категории на русском
    public function getCategoryNameAttribute(): string
    {
        return match($this->category) {
            'development' => 'Разработка',
            'testing' => 'Тестирование',
            'layout' => 'Верстка',
            'design' => 'Дизайн',
            'manager' => 'Руководитель',
            default => 'Не указана'
        };
    }

    // Цвет бейджа для категории
    public function getCategoryBadgeAttribute(): string
    {
        return match($this->category) {
            'development' => 'primary',
            'testing' => 'success',
            'layout' => 'info',
            'design' => 'warning',
            'manager' => 'danger',
            default => 'secondary'
        };
    }

    // Иконка для категории
    public function getCategoryIconAttribute(): string
    {
        return match($this->category) {
            'development' => 'code',
            'testing' => 'check-circle',
            'layout' => 'layout',
            'design' => 'pen-tool',
            'manager' => 'user-check',
            default => 'folder'
        };
    }
}

<?php
// app/Models/Budget.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Budget extends Model
{
    protected $table = 'project_budget';

    protected $fillable = [
        'total_budget',
        'current_balance',
        'manager_balance'
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'manager_balance' => 'decimal:2',
    ];

    // Всегда возвращает единственный бюджет проекта
    public static function getBudget(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'total_budget' => 0,
                'current_balance' => 0,
                'manager_balance' => 0,
            ]
        );
    }

    // Пополнение бюджета
    public function addIncome(float $amount, string $description, ?string $notes = null): BudgetTransaction
    {
        return DB::transaction(function () use ($amount, $description, $notes) {
            $transaction = BudgetTransaction::create([
                'user_id' => auth()->id(),
                'type' => 'income',
                'amount' => $amount,
                'description' => $description,
                'notes' => $notes,
                'balance_before' => $this->current_balance,
                'balance_after' => $this->current_balance + $amount,
            ]);

            $this->update([
                'total_budget' => $this->total_budget + $amount,
                'current_balance' => $this->current_balance + $amount,
            ]);

            return $transaction;
        });
    }

    // Списание из бюджета
    public function addExpense(float $amount, string $category, string $description, ?string $notes = null): BudgetTransaction
    {
        if ($this->current_balance < $amount) {
            throw new \Exception('Недостаточно средств в бюджете');
        }

        return DB::transaction(function () use ($amount, $category, $description, $notes) {
            $managerBefore = $this->manager_balance;
            $managerAfter = $managerBefore;

            // Если категория "Руководитель", деньги идут на баланс руководителя
            if ($category === 'manager') {
                $managerAfter = $managerBefore + $amount;
            }

            $transaction = BudgetTransaction::create([
                'user_id' => auth()->id(),
                'type' => 'expense',
                'category' => $category,
                'amount' => $amount,
                'description' => $description,
                'notes' => $notes,
                'balance_before' => $this->current_balance,
                'balance_after' => $this->current_balance - $amount,
                'manager_balance_before' => $managerBefore,
                'manager_balance_after' => $managerAfter,
            ]);

            $this->update([
                'current_balance' => $this->current_balance - $amount,
                'manager_balance' => $managerAfter,
            ]);

            return $transaction;
        });
    }

    // Обновление баланса руководителя (только для user_id = 1)
    public function updateManagerBalance(float $newBalance): BudgetTransaction
    {
        if (auth()->id() !== 1) {
            throw new \Exception('Только руководитель может изменять этот баланс');
        }

        return DB::transaction(function () use ($newBalance) {
            $oldBalance = $this->manager_balance;
            $difference = $newBalance - $oldBalance;

            $transaction = BudgetTransaction::create([
                'user_id' => auth()->id(),
                'type' => $difference >= 0 ? 'income' : 'expense',
                'category' => 'manager',
                'amount' => abs($difference),
                'description' => 'Корректировка баланса руководителя',
                'notes' => "Изменение с {$oldBalance} на {$newBalance}",
                'balance_before' => $this->current_balance,
                'balance_after' => $this->current_balance,
                'manager_balance_before' => $oldBalance,
                'manager_balance_after' => $newBalance,
            ]);

            $this->update(['manager_balance' => $newBalance]);

            return $transaction;
        });
    }

    public function transactions()
    {
        return $this->hasMany(BudgetTransaction::class, 'id', 'id')->whereNotNull('id');
    }

    // Получить все транзакции
    public static function getAllTransactions()
    {
        return BudgetTransaction::with('user')->orderBy('created_at', 'desc');
    }
}

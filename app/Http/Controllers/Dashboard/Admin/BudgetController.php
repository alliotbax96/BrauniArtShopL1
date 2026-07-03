<?php
// app/Http/Controllers/BudgetController.php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Models\Budget;
use App\Models\BudgetTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\BaseController;

class BudgetController extends BaseController
{
    public function index()
    {
        $this->shareCommonData();
        $budget = Budget::getBudget();

        // Статистика по категориям
        $statistics = BudgetTransaction::selectRaw("
            category,
            SUM(amount) as total_amount,
            COUNT(*) as count
        ")->where('type', 'expense')
            ->groupBy('category')
            ->get();

        $transactions = BudgetTransaction::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $mainView = 'dashboard.admin.projectFinance.index';
        return view('dashboard.index', compact('budget', 'transactions', 'statistics', 'mainView'));
    }


    public function getTransactions(Request $request)
    {
        $query = BudgetTransaction::with('user');

        // Фильтрация по типу
        if ($request->filled('type') && $request->type !== 'all') {
            if ($request->type === 'income') {
                $query->where('type', 'income');
            } elseif ($request->type === 'expense') {
                $query->where('type', 'expense');
            } else {
                // Фильтрация по конкретной категории
                $query->where('type', 'expense')
                    ->where('category', $request->type);
            }
        }

        // Поиск по описанию и заметкам
        if ($request->filled('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        // Фильтрация по дате
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Получаем общее количество записей ДО пагинации
        $totalRecords = $query->count();

        // Пагинация
        $perPage = $request->input('length', 20); // DataTables использует 'length'
        $page = ($request->input('start', 0) / $perPage) + 1;

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Форматируем данные для DataTables
        $data = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'date' => $transaction->created_at->format('d.m.Y'),
                'time' => $transaction->created_at->format('H:i'),
                'type' => $transaction->type,
                'type_label' => $transaction->type === 'income' ? 'Поступление' : 'Расход',
                'type_badge' => $transaction->type === 'income' ? 'success' : 'danger',
                'category' => $transaction->category,
                'category_name' => $transaction->category_name,
                'category_badge' => $transaction->category_badge,
                'category_icon' => $transaction->category_icon,
                'amount' => $transaction->amount,
                'amount_formatted' => number_format($transaction->amount, 2, '.', ' '),
                'description' => $transaction->description,
                'notes' => $transaction->notes,
                'balance_before' => $transaction->balance_before,
                'balance_before_formatted' => number_format($transaction->balance_before, 2, '.', ' '),
                'balance_after' => $transaction->balance_after,
                'balance_after_formatted' => number_format($transaction->balance_after, 2, '.', ' '),
                'manager_balance_after' => $transaction->manager_balance_after,
                'manager_balance_after_formatted' => $transaction->manager_balance_after ?
                    number_format($transaction->manager_balance_after, 2, '.', ' ') : null,
                'user_name' => $transaction->user->name ?? 'Система',
                'user_avatar' => $transaction->user->avatar ?? asset('assets/images/avatar/default.png'),
                'can_delete' => auth()->id() === 1 || auth()->id() === $transaction->user_id,
                'delete_url' => route('admin.budget.delete', $transaction->id),
            ];
        });

        // Формат ответа для DataTables
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'from' => $transactions->firstItem(),
                'to' => $transactions->lastItem(),
            ],
        ]);
    }

    // Добавление поступления
    public function addIncome(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $budget = Budget::getBudget();
        $budget->addIncome(
            $validated['amount'],
            $validated['description'],
            $validated['notes'] ?? null
        );

        return back()->with('success', 'Поступление добавлено');
    }

    // Добавление расхода
    public function addExpense(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|in:development,testing,layout,design,manager',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $budget = Budget::getBudget();
            $budget->addExpense(
                $validated['amount'],
                $validated['category'],
                $validated['description'],
                $validated['notes'] ?? null
            );

            return back()->with('success', 'Расход добавлен');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Обновление баланса руководителя
    public function updateManagerBalance(Request $request)
    {
        if (auth()->id() !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'new_balance' => 'required|numeric|min:0',
        ]);

        try {
            $budget = Budget::getBudget();
            $budget->updateManagerBalance($validated['new_balance']);

            return back()->with('success', 'Баланс руководителя обновлен');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Удаление транзакции
    public function deleteTransaction($id)
    {
        $transaction = BudgetTransaction::findOrFail($id);

        // Только руководитель или создатель может удалить
        if (auth()->id() !== 1 && $transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $budget = Budget::getBudget();

        // Возвращаем баланс
        if ($transaction->type === 'income') {
            $budget->update([
                'total_budget' => $budget->total_budget - $transaction->amount,
                'current_balance' => $budget->current_balance - $transaction->amount,
            ]);
        } else {
            $budget->update([
                'current_balance' => $budget->current_balance + $transaction->amount,
            ]);

            if ($transaction->category === 'manager') {
                $budget->update([
                    'manager_balance' => $budget->manager_balance - $transaction->amount,
                ]);
            }
        }

        $transaction->delete();

        return back()->with('success', 'Транзакция удалена');
    }
}

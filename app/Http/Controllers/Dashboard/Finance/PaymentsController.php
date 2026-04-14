<?php

namespace App\Http\Controllers\Dashboard\Finance;

use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SellerPayment;

class PaymentsController extends BaseController
{
    public function index() {
        if(!Auth::user()->groupInfo()->hasPermission('view_finance')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $this->shareCommonData(); // вызываем один раз
        return view('dashboard.index',[
            'View' => 'dashboard.finance.payments',
            'title'=>'Выплаты | Единая система BaID',
            'PageName'=>'Финансы',
            'InPageName'=>'Выплаты'
        ]);
    }

    public function ajaxPayments(Request $request)
    {
        // Проверка прав доступа
        if (!Auth::user()->groupInfo()->hasPermission('view_payments')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $seller = Auth::user()->getFirstSeller();

        // Получаем параметры от DataTables
        $draw = request()->input('draw', 1);
        $start = request()->input('start', 0);
        $length = request()->input('length', 10);
        $search = request()->input('search.value', '');
        $orderColumn = request()->input('order.0.column', 0);
        $orderDir = request()->input('order.0.dir', 'desc');

        // Определяем поле для сортировки
        $columns = ['id', 'paid_at', 'order_id', 'amount', 'paid'];
        $orderField = $columns[$orderColumn] ?? 'id';

        // Базовый запрос с фильтрацией по продавцу
        $query = SellerPayment::where('seller_id', 2406)
            ->with('order');

        // Поиск по номеру заказа или сумме
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        // Фильтр по статусу выплаты
        $paidFilter = request()->input('paid_filter');
        if ($paidFilter !== null) {
            $query->where('paid', (int)$paidFilter);
        }

        // Сортировка и подсчёт общего количества записей
        $query->orderBy($orderField, $orderDir);
        $total = $query->count();

        // Пагинация
        $payments = $query->skip($start)->take($length)->get();

        // Форматируем данные
        $data = [];
        foreach ($payments as $payment) {
            $data[] = [
                'id' => $payment->id,
                'created_at' => $payment->created_at ? $payment->created_at->format('d.m.Y H:i') : '—',
                'order_id' => $payment->order_id,
                'amount' => number_format($payment->amount, 2, ',', ' '),
                'paid' => $payment->paid,
                'status_badge' => $this->getStatusBadge($payment->paid),
                'order_link' => $payment->order ? '#' . $payment->order->id : 'Не указан'
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

// Вспомогательный метод для формирования бейджа статуса
    private function getStatusBadge($paid)
    {
        if ($paid) {
            return '<div class="badge bg-soft-success text-success">Выплачен</div>';
        } else {
            return '<div class="badge bg-soft-warning text-warning">Новый</div>';
        }
    }

}

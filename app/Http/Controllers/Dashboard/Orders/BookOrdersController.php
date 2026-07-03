<?php

namespace App\Http\Controllers\Dashboard\Orders;

use App\Models\BookOrder;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use App\Http\Controllers\Dashboard\BaseController;

class BookOrdersController extends BaseController
{
    public function index()
    {
        if (!Auth::user()->groupInfo()->hasPermission('view_orders')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $this->shareCommonData();

        if (Auth::user()->isAdmin()) {
            $SellerList = Seller::all();
        } else {
            $SellerList = null;
        }

        $SelectSellerList = $SellerList ? $SellerList : '';

        return view('dashboard.index', [
            'View' => 'dashboard.bookOrders.index',
            'title' => 'Заказы книг | Единая система BaID',
            'PageName' => 'Заказы книг',
            'InPageName' => 'Заказы на книги',
            'SelectSellerList' => $SelectSellerList,
        ]);
    }

    public function ajax(Request $request)
    {
        if (!Auth::user()->groupInfo()->hasPermission('view_orders')) {
            return response()->json(['error' => 'Нет прав'], 403);
        }

        Log::info('BookOrders AJAX request', [
            'sellerID' => $request->input('sellerID'),
            'user' => Auth::id(),
            'is_admin' => Auth::user()->isAdmin(),
        ]);

        // Определяем sellerId: админу можно выбирать, остальным — только свой первый
        if (Auth::user()->isAdmin()) {
            $sellerId = $request->input('sellerID') ?? Auth::user()->getFirstSeller()->id;
        } else {
            $sellerId = Auth::user()->getFirstSeller()->id;
        }

        // Параметры DataTables
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');

        // Колонки для сортировки
        $columns = ['id', 'amount', 'status', 'created_at', 'paid_at'];
        $orderField = $columns[$orderColumnIndex] ?? 'created_at';

        // Базовый запрос: заказы книг, где продавец совпадает с sellerId
        $query = BookOrder::where('seller_id', $sellerId)
            ->with(['book', 'user', 'seller']);

        // Поиск по ID, сумме, статусу, email пользователя
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");

                // Поиск по email пользователя (через relation user)
                $q->orWhereHas('user', function ($u) use ($search) {
                    $u->where('email', 'like', "%{$search}%");
                });
            });
        }

        // Сортировка
        $query->orderBy($orderField, $orderDir);

        // Общее количество записей (до пагинации)
        $total = $query->count();

        // Пагинация
        $bookOrders = $query->skip($start)->take($length)->get();

        // Подготовка данных для DataTables
        $data = [];

        foreach ($bookOrders as $order) {
            // Превью обложки книги
            $imageUrl = $order->book->getMainImage()
                ? $order->book->getMainImage() // у тебя уже возвращает полный URL (S3)
                : '/images/default-book.png';

            // Название книги + автор (можно адаптировать)
            $bookName = htmlspecialchars($order->book->getProductName() ?? 'Книга без названия');
            $author = htmlspecialchars($order->book->author ?? '');
            if ($author) {
                $bookName .= ' (' . $author . ')';
            }

            // Форматирование суммы
            $formattedAmount = number_format($order->amount, 2, '.', ' ');

            // Статус: можно добавить свою логику цветов, пока используем простую
            $statusName = match ($order->status) {
                'pending' => 'Ожидает оплаты',
                'paid' => 'Оплачен',
                'cancelled' => 'Отменён',
                'refunded' => 'Возвращён',
                default => $order->status,
            };

            $statusColor = match ($order->status) {
                'paid' => 'success',
                'pending' => 'warning',
                'cancelled', 'refunded' => 'danger',
                default => 'secondary',
            };

            // Дата оплаты (если есть)
            $paidAtDisplay = $order->paid_at ? date('d.m.Y H:i', strtotime($order->paid_at)) : '-';

            $data[] = [
                'id' => $order->id,
                'book_name' => $bookName,
                'image_html' => '
                    <a href="javascript:void(0)" class="hstack gap-3 align-items-center">
                        <div class="avatar-image avatar-md">
                            <img src="' . $imageUrl . '" alt="" class="img-fluid rounded" style="object-fit:cover;">
                        </div>
                        <div>
                            <span class="text-truncate-2-line">' . $bookName . '</span>
                            <small class="fs-12 fw-normal text-muted">' . $formattedAmount . ' руб.</small>
                        </div>
                    </a>',
                'amount' => $formattedAmount,
                'status' => $statusName,
                'status_color' => $statusColor,
                'created_at' => $order->created_at->format('d.m.Y H:i'),
                'paid_at' => $paidAtDisplay,
                'user_email' => $order->user->email ?? '-',
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }
}

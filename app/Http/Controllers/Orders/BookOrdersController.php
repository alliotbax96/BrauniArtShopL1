<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BookOrder;
use App\Http\Controllers\BaseController;

class BookOrdersController extends BaseController
{
    public function index(Request $request) {
        $this->shareCommonData($request);
        $orders = BookOrder::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('index', ['view'=>'pages.bookOrders.index', 'title'=>'Мои книги | Брауни Арт — маркетплейс качественных товаров с доставкой по России', 'orders' => $orders
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;

class HomeController extends BaseController
{
    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->shareCommonData(); // вызываем один раз
        $validPerPage = 12;
        // Используем новый метод с фильтрацией
        $products = $this->service->getAllWithConditions($validPerPage);

        $scripts = array();
        return view('index', [
            'view' => 'pages.home',
            'title'=> 'Главная | Брауни Арт — маркетплейс качественных товаров с доставкой по России',
            'scripts' => $scripts,
            'products' => compact('products')
        ]);
    }

    public function ajax_home(Request $request)
    {
        $this->shareCommonData(); // вызываем один раз
        $perPage = $request->input('perPage', 12);
        $validPerPage = in_array($perPage, [4, 8, 12, 16, 20]) ? $perPage : 12;
        // Используем новый метод с фильтрацией
        $products = $this->service->getAllWithConditions($validPerPage);
        return view('elements.ajaxHome', ['products' => compact('products')]);
    }


}

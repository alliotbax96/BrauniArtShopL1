<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use Cookie;
use App\Models\Quest;

class HomeController extends BaseController
{
    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->shareCommonData(); // вызываем один раз
        $validPerPage = 12;
        $shopMode = $request->cookie('ShopMode') ?? 1;

        switch ($shopMode) {
            case 1:
                $products = $this->indexProducts($validPerPage);
                $showUrl = '/products/';
            break;
            case 4:
                $products = $this->indexQuests($validPerPage);
                $showUrl = '/quests/';
            break;
        }

        return view('index', [
            'view' => 'pages.home',
            'showUrl' => $showUrl,
            'title'=> 'Главная | Брауни Арт — маркетплейс качественных товаров с доставкой по России',
            'products' => compact('products')
        ]);
    }

    private function indexProducts($perPage = 12){
        return $this->service->getAllWithConditions($perPage);
    }

    private function indexQuests($perPage = 12){
        return Quest::paginate($perPage);
    }

    public function ajax_home(Request $request)
    {
        $this->shareCommonData(); // вызываем один раз
        $perPage = $request->input('perPage', 12);
        $validPerPage = in_array($perPage, [4, 8, 12, 16, 20]) ? $perPage : 12;
        // Используем новый метод с фильтрацией
        $shopMode = $request->cookie('ShopMode') ?? 1;
        switch ($shopMode) {
            case 1:
                $products = $this->indexProducts($validPerPage);
            break;
            case 4:
                $products = $this->indexQuests($validPerPage);
            break;
        }
        return view('elements.ajaxHome', ['products' => compact('products')]);
    }



}

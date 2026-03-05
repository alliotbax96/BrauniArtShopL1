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
        $products = $this->service->getAll($validPerPage);
        $scripts[] = "assets/scripts/pages/home.js";
        return view('index', ['view' => 'pages.home', 'scripts' => $scripts, 'products' => compact('products')]);
    }

    public function ajax_home(Request $request){
        $this->shareCommonData(); // вызываем один раз
        $perPage = $request->input('perPage', 12);
        $validPerPage = in_array($perPage, [4, 8, 12, 16, 20]) ? $perPage : 12;
        $products = $this->service->getAll($validPerPage);
        return view('components.home.ajaxHome', ['products' => compact('products')]);
    }
}

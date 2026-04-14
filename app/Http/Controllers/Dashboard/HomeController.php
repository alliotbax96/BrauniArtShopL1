<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    public function index(){
        $this->shareCommonData(); // вызываем один раз
        return view('dashboard.index', ['View'=>'dashboard.home', 'title'=>'Главная | Единая система BaID', 'PageName'=>'Главная']);
    }
}

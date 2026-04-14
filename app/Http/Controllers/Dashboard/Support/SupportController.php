<?php

namespace App\Http\Controllers\Dashboard\Support;

use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Http\Request;

class SupportController extends BaseController
{
    public function index() {
        $this->shareCommonData(); // вызываем один раз
        return view('dashboard.index',[
            'mainView' => 'dashboard.support.index',
            'title'=>'Поддержка | Единая система BaID',
            'PageName'=>'Поддержка',
        ]);
    }
}

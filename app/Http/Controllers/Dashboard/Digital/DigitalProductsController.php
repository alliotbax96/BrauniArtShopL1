<?php

namespace App\Http\Controllers\Dashboard\Digital;

use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DigitalProductsController extends BaseController
{
    public function index() {
        if(!Auth::user()->groupInfo()->hasPermission('view_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $this->shareCommonData(); // вызываем один раз

        return view('dashboard.index',[
            'View' => 'dashboard.digital.index',
            'title'=>'Управление цифровыми товарами | Единая система BaID',
            'PageName'=>'Ассортимент',
            'InPageName'=>'Управление цифровыми товарами',
            'CreateObject' => '/seller/digital/create'
        ]);
    }
}

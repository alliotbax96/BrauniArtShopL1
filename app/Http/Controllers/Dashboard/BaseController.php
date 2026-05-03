<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\TbankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    protected function shareCommonData()
    {
        //TBankCustomerInit
        if(Auth::check()) {
            $tbankService = new TbankService();
            $tbankService->InitCustomer(Auth::id());
        }



        view()->share([
            'currentUser' => Auth::user(),
            'title' => 'Единая система BaID',
            'verName' => '3.0.1 Beta',
            'ver' => 1,
            'sellerId' => Auth::user()->getSellerId(),
            'scripts' => null,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ShopMode;
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
        $seller = Auth::check() ? Auth::user()->getFirstSeller() : null;

        view()->share([
            'currentUser' => Auth::user(),
            'title' => 'Единая система BaID',
            'verName' => '3.1.2 Beta',
            'ver' => 3,
            'sellerId' => Auth::user()->getSellerId(),
            'ShopModes' => ShopMode::where('status', 1)->get(),
            'scripts' => null,
            'Seller' => $seller,
        ]);
    }
}

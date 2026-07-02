<?php

namespace App\Http\Controllers;

use App\Models\ProductGroup;
use App\Services\CartService; // Добавляем импорт сервиса корзины
use App\Services\TbankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Models\ShopMode;

class BaseController extends Controller
{
    private ?CartService $cartService = null; // Инициализируем как null

    private array $cartData = []; // Кэш данных корзины

    // Ленивая загрузка CartService
    protected function getCartService(): CartService
    {
        if ($this->cartService === null) {
            $this->cartService = App::make(CartService::class);
        }
        return $this->cartService;
    }
    protected function shareCommonData(Request $request)
    {

        //TBankCustomerInit
        if(Auth::check()) {
            $tbankService = new TbankService();
            $tbankService->InitCustomer(Auth::id());
        }

        if (isset($shopMode)) {
            $CurrentShopMode = $shopMode;
        } elseif ($request->cookie('ShopMode') !== null) {
            $CurrentShopMode = $request->cookie('ShopMode');
        } else {
            $CurrentShopMode = 1;
        }

        $ShopMode = ShopMode::find($CurrentShopMode);
        // Получаем данные о корзине
        $cartData = $this->getCartData($ShopMode->Model);

        view()->share([
            'currentUser' => Auth::user(),
            'cartItemsCount' => $cartData['items_count'],
            'cartTotalPrice' => $cartData['total_price'],
            'cart' => $cartData['cart'],
            'ProductGroups' => $this->getProductGroups(),
            'title' => 'Брауни Арт — маркетплейс качественных товаров с доставкой по России',
            'ShopModes' => ShopMode::where('status', 1)->get(),
            'CartShopMode' => $ShopMode,
//            'ShopModes' => ShopMode::all(),
            'verName' => '3.1.2 Beta',
            'ver' => 3,
            'scripts' => null,
            'meta_description' => 'Маркетплейс Брауни Арт — широкий ассортимент товаров высокого качества от проверенных продавцов. Надёжный поставщик с многолетним опытом: гарантия сервиса, доступные цены и удобная доставка по России. Покупайте с комфортом!'
        ]);
    }

    protected function shareCommonDataForCart()
    {
        view()->share([
            'currentUser' => Auth::user(),
            'ProductGroups' => $this->getProductGroups(),
            'title' => 'Брауни Арт — маркетплейс качественных товаров с доставкой по России',
            'verName' => '2.0',
            'ver' => 1,
            'scripts' => null,
        ]);
    }

    private function getCartData($Model = 'App\Models\Product'): array
    {
        // Возвращаем закэшированные данные, если они есть
        if (!empty($this->cartData)) {
            return $this->cartData;
        }

        try {
            $cart = $this->getCartService()->getCart();
            $itemsCount = $cart->items->where('product_type', $Model)->sum('quantity');
            $totalPrice = $cart->items->where('product_type', $Model)->reduce(function ($sum, $item) {
                return $sum + ($item->getProduct()->getProductPrice() * $item->quantity);
            }, 0);

            // Кэшируем данные
            $this->cartData = [
                'items_count' => $itemsCount,
                'total_price' => $totalPrice,
                'cart' => $cart
            ];

            return $this->cartData;
        } catch (\Exception $e) {
            \Log::error('Error getting cart data: ' . $e->getMessage());
            // Возвращаем дефолтные значения при ошибке
            return [
                'items_count' => 0,
                'total_price' => 0,
                'cart' => null
            ];
        }
    }

    private function GetProductGroups()
    {
        return ProductGroup::rootGroups()
            ->with('children')
            ->get();
    }
}

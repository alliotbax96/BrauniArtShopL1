<?php

namespace App\Http\Controllers;

use App\Models\ProductGroup;
use App\Services\CartService; // Добавляем импорт сервиса корзины
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

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
    protected function shareCommonData()
    {
        // Получаем данные о корзине
        $cartData = $this->getCartData();

        view()->share([
            'currentUser' => Auth::user(),
            'cartItemsCount' => $cartData['items_count'],
            'cartTotalPrice' => $cartData['total_price'],
            'cart' => $cartData['cart'],
            'ProductGroups' => $this->getProductGroups(),
            'title' => 'BrauniArtмаркетплейс',
            'verName' => '2.0',
            'ver' => 1,
            'scripts' => null,
        ]);
    }

    protected function shareCommonDataForCart()
    {
        view()->share([
            'currentUser' => Auth::user(),
            'ProductGroups' => $this->getProductGroups(),
            'title' => 'BrauniArtмаркетплейс',
            'verName' => '2.0',
            'ver' => 1,
            'scripts' => null,
        ]);
    }

    private function getCartData(): array
    {
        // Возвращаем закэшированные данные, если они есть
        if (!empty($this->cartData)) {
            return $this->cartData;
        }

        try {
            $cart = $this->getCartService()->getCart();
            $itemsCount = $cart->items->sum('quantity');
            $totalPrice = $cart->items->reduce(function ($sum, $item) {
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

    private function GetProductGroups($ShopMode = 1)
    {
        return ProductGroup::rootGroups()
            ->with('children')
            ->get();
    }
}

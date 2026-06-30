<?php

namespace App\Http\Controllers;

use App\Models\ShopMode;
use App\Models\UserPvz;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends BaseController
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(request $request)
    {
        $this->shareCommonData($request); // вызываем один раз
        $shopMode = $this->ShopModeGet($request);
        $cartItems = $this->cartService->getItemsWithDetails($shopMode->Model);
        $totalItems = $this->cartService->getTotalItems($shopMode->Model);
        $totalPrice = $this->cartService->getTotalPrice($shopMode->Model);
        $userPvzs = UserPvz::where('user_id', Auth::id())->get();

        return view('index', ['view' => 'pages.cart', 'userPvzs'=>$userPvzs, 'title'=> 'Корзина | Брауни Арт — маркетплейс качественных товаров с доставкой по России', compact('cartItems', 'totalItems', 'totalPrice')]);
    }

    private function ShopModeGet(Request $request){

        if (isset($shopMode)) {
            $CurrentShopMode = $shopMode;
        } elseif ($request->cookie('ShopMode') !== null) {
            $CurrentShopMode = $request->cookie('ShopMode');
        } else {
            $CurrentShopMode = 1;
        }

        return ShopMode::find($CurrentShopMode);
    }

    public function getMiniCart(Request $request)
    {
        $cart = $this->cartService->getCart();
        $items = [];
        $shopMode = $this->ShopModeGet($request);
        if ($cart && $cart->items->isNotEmpty()) {
            foreach ($cart->items as $item) {
                if($item->product_type == $shopMode->Model) {
                    $product = $item->getProduct();
                    switch ($shopMode->id) {
                        case '8':
                            $route = 'books.show';
                        break;
                        default:
                            $route = 'products.show';
                        break;
                    }
                    $items[] = [
                        'id' => $item->id,
                        'name' => $product->getProductName(),
                        'quantity' => $item->quantity,
                        'price_per_unit' => number_format($product->getProductPrice(), 2, ',', ' '),
                        'total_price' => number_format($product->getProductPrice() * $item->quantity, 2, ',', ' '),
                        'image_url' => $item->product_type == 'App\Models\Product' ? 'https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/' : '' . $product->getMainImage(),
                        'product_url' => route($route, $product->id),
                        'remove_url' => route('cart.remove', $item->id)
                    ];
                }
            }
        }

        return response()->json([
            'shopModeModel' => $shopMode->Model,
            'items' => $items,
            'total_items' => $cart ? $this->cartService->getTotalItems($shopMode->Model) : 0,
            'total_price' => $cart ? number_format($this->cartService->getTotalPrice($shopMode->Model), 2, ',', ' ') . ' ' : '0'
        ]);
    }


    public function add(Request $request)
    {
        try {
            $this->cartService->addItem(
                $request->input('product_id'),
                $request->input('product_type', 'App\\Models\\Product'),
                $request->input('seller_id'),
                $request->input('quantity', 1),
                $request->input('options', [])
            );

            return response()->json([
                'success' => true,
                'message' => 'Товар добавлен в корзину',
                'total_items' => $this->cartService->getTotalItems()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function update(Request $request, $itemId)
    {
        try {
//            $itemId = $request->input('item_id');
            $quantity = $request->input('quantity');

            // Валидация входных данных
            if (!$itemId || !is_numeric($itemId) || $quantity < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Некорректные данные запроса'
                ], 400);
            }

            $this->cartService->updateItemQuantity($itemId, $quantity);

            // Получаем обновлённые данные корзины
            $cart = $this->cartService->getCart();
            $updatedItem = $cart->items->find($itemId);

            return response()->json([
                'success' => true,
                'message' => 'Количество обновлено',
                'item' => [
                    'subtotal' => $updatedItem->getProduct()->getProductPrice() * $updatedItem->quantity
                ],
                'total_price' => $this->cartService->getTotalPrice(),
                'cart_items_count' => $this->cartService->getTotalItems()
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function remove($itemId)
    {
        $this->cartService->removeItem($itemId);

        return response()->json([
            'success' => true,
            'message' => 'Товар удалён из корзины'
        ]);
    }

    public function clear()
    {
        $this->cartService->clearCart();

        return redirect()->route('cart.index')
            ->with('success', 'Корзина очищена');
    }
}

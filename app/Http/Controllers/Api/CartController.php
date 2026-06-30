<?php

namespace App\Http\Controllers\Api;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends ApiBaseController
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Получение содержимого корзины
     */
    public function index()
    {
        $cartItems = $this->cartService->getItemsWithDetails();
        $totalItems = $this->cartService->getTotalItems();
        $totalPrice = $this->cartService->getTotalPrice();

        $items = $cartItems->map(function ($item) {
            $product = $item->getProduct();
            $mainImage = $product->images->where('mainImage', true)->first();

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $product->getProductName(),
                'price_per_unit' => $product->getProductPrice(),
                'quantity' => $item->quantity,
                'total_price' => $product->getProductPrice() * $item->quantity,
                'image' => $mainImage ? $this->getImageUrl($mainImage->url) : null,
                'seller_id' => $item->seller_id,
                'seller_name' => $item->seller->name ?? null,
                'in_stock' => $product->getProductQuantity() >= $item->quantity,
                'options' => $item->options
            ];
        });

        return $this->successResponse([
            'items' => $items,
            'total_items' => $totalItems,
            'total_price' => number_format($totalPrice, 2, '.', ''),
            'currency' => 'RUB'
        ]);
    }

    /**
     * Добавление товара в корзину
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'seller_id' => 'required|integer|exists:sellers,id',
            'quantity' => 'integer|min:1|max:99',
            'options' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $this->cartService->addItem(
                $request->product_id,
                'App\\Models\\Product',
                $request->seller_id,
                $request->quantity ?? 1,
                $request->options ?? []
            );

            return $this->successResponse([
                'total_items' => $this->cartService->getTotalItems()
            ], 'Товар добавлен в корзину');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Обновление количества товара
     */
    public function update(Request $request, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $this->cartService->updateItemQuantity($itemId, $request->quantity);

            $cart = $this->cartService->getCart();
            $updatedItem = $cart->items->find($itemId);
            $product = $updatedItem->getProduct();

            return $this->successResponse([
                'item_subtotal' => $product->getProductPrice() * $updatedItem->quantity,
                'total_price' => number_format($this->cartService->getTotalPrice(), 2, '.', ''),
                'total_items' => $this->cartService->getTotalItems()
            ], 'Количество обновлено');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Удаление товара из корзины
     */
    public function remove($itemId)
    {
        try {
            $this->cartService->removeItem($itemId);

            return $this->successResponse([
                'total_items' => $this->cartService->getTotalItems(),
                'total_price' => number_format($this->cartService->getTotalPrice(), 2, '.', '')
            ], 'Товар удалён из корзины');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Очистка корзины
     */
    public function clear()
    {
        $this->cartService->clearCart();

        return $this->successResponse(null, 'Корзина очищена');
    }

    private function getImageUrl(?string $url): ?string
    {
        if (!$url) return null;
        return 'https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/' . $url;
    }
}

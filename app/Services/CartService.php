<?php

namespace App\Services;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductQuantity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected ?Cart $cart = null;

    public function __construct()
    {
        $this->initializeCart();
    }

    protected function initializeCart(): void
    {
        $sessionId = Session::getId();

        // Сначала ищем гостевую корзину по session_id (даже если пользователь авторизован)
        $guestCart = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if (Auth::check()) {
            // Ищем корзину пользователя
            $userCart = Cart::where('user_id', Auth::id())->first();

            if ($userCart) {
                // Если корзина пользователя есть — используем её
                $this->cart = $userCart;
            } else if ($guestCart) {
                // Если корзины пользователя нет, но есть гостевая — присваиваем user_id
                $guestCart->update([
                    'user_id' => Auth::id(),
                    'session_id' => null
                ]);
                $this->cart = $guestCart;
            } else {
                // Если нет ни гостевой, ни пользовательской — создаём новую
                $this->cart = Cart::create([
                    'user_id' => Auth::id(),
                    'session_id' => null
                ]);
            }
        } else {
            // Для гостей — используем гостевую корзину
            if ($guestCart) {
                $this->cart = $guestCart;
            } else {
                $this->cart = Cart::create([
                    'user_id' => null,
                    'session_id' => $sessionId
                ]);
            }
        }
    }



    /**
     * Объединяет гостевую корзину с корзиной пользователя после авторизации
     */
    /**
     * Объединяет гостевую корзину с корзиной пользователя после авторизации.
     * Выполняется в транзакции для обеспечения целостности данных.
     *
     * @param int $userId ID пользователя, для которого выполняется слияние
     */
    public function mergeWithUserCart(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            $currentCart = $this->cart;
            $userCart = Cart::where('user_id', $userId)->first();

            // Логирование для отладки — фиксируем все ключевые параметры
            \Log::info('Cart merge process', [
                'user_id' => $userId,
                'current_cart_id' => $currentCart->id,
                'current_cart_user_id' => $currentCart->user_id,
                'current_cart_session_id' => $currentCart->session_id,
                'user_cart_exists' => $userCart ? 'yes' : 'no',
                'is_current_guest' => is_null($currentCart->user_id) ? 'yes' : 'no'
            ]);

            // Случай 1: текущая корзина — гостевая (не привязана к пользователю)
            if (is_null($currentCart->user_id)) {
                if ($userCart) {
                    // Подслучай 1.1: у пользователя уже есть корзина
                    // Сливаем позиции из гостевой корзины в существующую пользовательскую
                    $this->mergeCartItems($currentCart, $userCart);

                    // Удаляем гостевую корзину после переноса всех позиций
                    $currentCart->delete();

                    // Обновляем текущую корзину сервиса на пользовательскую
                    $this->cart = $userCart;

                    \Log::info('Merged guest cart into existing user cart', [
                        'guest_cart_id' => $currentCart->id,
                        'user_cart_id' => $userCart->id,
                        'items_transferred' => $currentCart->items->count()
                    ]);
                } else {
                    // Подслучай 1.2: у пользователя нет корзины
                    // Присваиваем гостевую корзину пользователю
                    $currentCart->update([
                        'user_id' => $userId,
                        'session_id' => null  // Очищаем session_id, т. к. корзина теперь пользовательская
                    ]);

                    \Log::info('Assigned guest cart as user cart', [
                        'cart_id' => $currentCart->id,
                        'assigned_to_user' => $userId
                    ]);
                }
            }

            // Случай 2: текущая корзина уже принадлежит пользователю (user_id заполнен)
            // Ничего не делаем — используем существующую корзину пользователя
            // Это предотвращает дублирование операций и гарантирует целостность данных
            else {
                \Log::info('Current cart already belongs to user — no merge needed', [
                    'cart_id' => $currentCart->id,
                    'user_id' => $userId
                ]);
            }
        });
    }



    /**
     * Объединяет позиции из одной корзины в другую
     */
    protected function mergeCartItems(Cart $sourceCart, Cart $targetCart): void
    {
        foreach ($sourceCart->items as $sourceItem) {
            $existingItem = $targetCart->items()
                ->where('product_id', $sourceItem->product_id)
                ->where('product_type', $sourceItem->product_type)
                ->where('seller_id', $sourceItem->seller_id)
                ->first();

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $sourceItem->quantity;
                $maxQuantity = $this->getAvailableStock($sourceItem->product_id, $sourceItem->product_type);

                $existingItem->update([
                    'quantity' => min($newQuantity, $maxQuantity)
                ]);
            } else {
                CartItem::create([
                    'cart_id' => $targetCart->id,
                    'product_id' => $sourceItem->product_id,
                    'product_type' => $sourceItem->product_type,
                    'seller_id' => $sourceItem->seller_id,
                    'quantity' => $this->adjustQuantityToStock($sourceItem->product_id, $sourceItem->product_type, $sourceItem->quantity),
                    'options' => $sourceItem->options
                ]);
            }
        }
    }

    public function setCurrentCart(Cart $cart): void
    {
        $this->cart = $cart;
    }

    protected function getAvailableStock($productId, $productType): int
    {
        $stock = ProductQuantity::forProduct($productId, $productType)->first();
        return $stock ? $stock->quantity : 0;
    }

    protected function adjustQuantityToStock($productId, $productType, $requestedQuantity): int
    {
        $available = $this->getAvailableStock($productId, $productType);
        return min($requestedQuantity, $available);
    }















    public function addItem($productId, $productType, $sellerId, $quantity = 1, array $options = [])
    {
        // Проверяем остатки
        if (!$this->checkStock($productId, $productType, $quantity)) {
            throw new \Exception('Недостаточно товара на складе');
        }

        $existingItem = $this->cart->items()
            ->where('product_id', $productId)
            ->where('product_type', $productType)
            ->where('seller_id', $sellerId)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if (!$this->checkStock($productId, $productType, $newQuantity)) {
                throw new \Exception('Превышено количество доступных товаров');
            }
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'cart_id' => $this->cart->id,
                'product_id' => $productId,
                'product_type' => $productType,
                'seller_id' => $sellerId,
                'quantity' => $quantity,
                'options' => $options
            ]);
        }

        return true;
    }

    protected function checkStock($productId, $productType, $requiredQuantity): bool
    {
        $stock = ProductQuantity::forProduct($productId, $productType)->first();
//        return $stock && $stock->quantity >= $requiredQuantity;
        return true;
    }

    public function getCart()
    {
        return $this->cart->load('items');
    }

    public function removeItem($itemId)
    {
        return CartItem::where('id', $itemId)
            ->where('cart_id', $this->cart->id)
            ->delete();
    }

    public function updateItemQuantity($itemId, $quantity)
    {
        $item = CartItem::where('id', $itemId)
            ->where('cart_id', $this->cart->id)
            ->first();

        if (!$item) {
            throw new \Exception('Позиция корзины не найдена');
        }

        // Проверяем остатки при обновлении количества
        if (!$this->checkStock($item->product_id, $item->product_type, $quantity)) {
            throw new \Exception('Недостаточно товара на складе для указанного количества');
        }

        $item->update(['quantity' => $quantity]);

        return $item;
    }

    public function clearCart()
    {
        return $this->cart->items()->delete();
    }

    public function getTotalItems(): int
    {
        return $this->cart->items->sum('quantity');
    }

    public function getTotalPrice(): float
    {
        $total = 0;

        foreach ($this->cart->items as $item) {
            $product = $item->getProduct();
            if ($product) {
                $price = $product->getProductPrice();
                $total += $price * $item->quantity;
            }
        }

        return round($total, 2);
    }

    public function getItemsWithDetails()
    {
        return $this->cart->items->map(function ($item) {
            $product = $item->getProduct();

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $product ? $product->getProductName() : 'Товар удалён',
                'seller_id' => $item->seller_id,
                'quantity' => $item->quantity,
                'price' => $product ? $product->getProductPrice() : 0,
                'total_price' => ($product ? $product->getProductPrice() : 0) * $item->quantity,
                'options' => $item->options,
                'in_stock' => $this->checkStock($item->product_id, $item->product_type, $item->quantity)
            ];
        });
    }

    public function isEmpty(): bool
    {
        return $this->cart->items->isEmpty();
    }

    /**
     * Получает ID текущей корзины (для использования в сессиях/авторизации)
     */
    public function getCartId(): int
    {
        return $this->cart->id;
    }

    /**
     * Получает гостевую корзину по session_id
     */
    public function getGuestCartBySession(string $sessionId): ?Cart
    {
        return Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();
    }

}

@props([
    'productId' => null,
    'sellerId' => null,
    'quantity' => 1,
    'productType' => 'App\\Models\\Product',
    'options' => [],
    'buttonText' => 'В корзину',
    'linkClass' => ''
])

<a href="javascript:void(0)"
   class="add-to-cart-link {{ $linkClass }}"
   data-product-id="{{ $productId }}"
   data-seller-id="{{ $sellerId }}"
   data-quantity="{{ $quantity }}"
   data-product-type="{{ $productType }}"
   title="Добавить в корзину">
    {{ $buttonText }}
</a>

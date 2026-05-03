@props([
    'productId' => null,
    'sellerId' => null,
    'mode' => 'catalog',
    'quantity' => 1,
    'productType' => 'App\\Models\\Product',
    'options' => [],
    'buttonText' => 'В корзину',
    'linkClass' => '',
])

@if($mode == 'catalog')
<a href="javascript:void(0)"
   class="{{ $linkClass }}"
   data-product-id="{{ $productId }}"
   data-seller-id="{{ $sellerId }}"
   data-quantity="{{ $quantity }}"
   data-product-type="{{ $productType }}"
   title="Добавить в корзину">
    {{ $buttonText }}
</a>
@elseif($mode == 'homeQuest')
<a href="/quests/{{$productId}}"
   class="to-cart">
   Подробнее
</a>
@else
<a href="javascript:void(0)"
    class="to-cart {{ $linkClass }}"
    data-product-id="{{ $productId }}"
    data-seller-id="{{ $sellerId }}"
    data-quantity="{{ $quantity }}"
    data-product-type="{{ $productType }}"
    title="Добавить в корзину">
    {{ $buttonText }}
    <i class="fas fa-cart-plus"></i>
</a>
@endif

@props([
    'product' => null,
])

<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 grid-item grid-sizer cat-two cat-three d-flex">
    <div class="product-card w-100">
        {{-- Блок изображения --}}
        <div class="product-card-img">
            <a href="/products/{{ $product->getProductId() }}">
                <img
                    src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{ $product->getMainImage() }}"
                    alt="{{ $product->getProductName() }}"
                >
            </a>

            {{-- Метки --}}
            @if($product->isNew())
                <span class="badge-new">Новинка</span>
            @endif
            <span class="badge-shop">BA Магазин</span>

            {{-- Кнопка корзины --}}
            <div class="cart-btn-wrapper">
                <x-add-to-cart-button
                    :product-id="$product->getProductId()"
                    :seller-id="$product->getProductSellerId()"
                    mode="home"
                    quantity="1"
                    button-text="В корзину"
                    link-class="AdToCartLink"
                    :options="[]"
                />
            </div>
        </div>

        {{-- Информация --}}
        <div class="product-card-info">
            <h5 class="product-title">
                <a href="/products/{{ $product->getProductId() }}">{{ $product->getProductName() }}</a>
            </h5>
            <span class="product-price">{{ $product->getProductPrice() }} ₽</span>
        </div>
    </div>
</div>

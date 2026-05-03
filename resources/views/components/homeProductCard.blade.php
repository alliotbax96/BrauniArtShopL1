@props([
    'product'=> null,
])

<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 grid-item grid-sizer cat-two cat-three">
    <div class="exclusive-item exclusive-item-two mb-40">
        <div class="exclusive-item-thumb">
            <a href="/products/{{$product->getProductId()}}">
                <img src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{$product->getMainImage()}}" alt="" style="object-fit: contain; height: 350px; margin-left: 20px;">
                <img class="overlay-product-thumb" src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{$product->getMainImage()}}" alt="" style="object-fit: contain; height: 350px; margin-left: 20px;">
            </a>
            @if($product->isNew())
                <span class="sd-meta">Новинка</span>
            @endif
            <span class="discount">BA Магазин</span>
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
        <div class="exclusive-item-content">
            <div class="exclusive--content--bottom">
                <h5>
                    <a href="/products/{{$product->getProductId()}}">{{$product->getProductName()}}</a>
                </h5>
                <span>{{$product->getProductPrice()}}р</span>
            </div>
        </div>
    </div>
</div>

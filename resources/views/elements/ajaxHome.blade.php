@if(count($products['products']) < 1)
    0
@else
    @foreach($products['products'] as $product)
        <div class="col-xl-3 col-lg-6 col-md-4 col-sm-6 {if ($i++)<=8}cat-one{/if}">
            <div class="exclusive-item exclusive-item-three text-center mb-50 card-item">
                <div class="exclusive-item-thumb">
                    <a href="/products/{{$product->getProductId()}}">
                        <img src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{$product->getMainImage()}}" alt="" style="object-fit: contain; height: 300px;">
                    </a>
                </div>
                <h5 class="exclusive-item-content"><a href="/products/{{$product->getProductId()}}">{{$product->getProductName()}}</a></h5>
                <p></p>
                <div class="exclusive--item--price"><span class="new-price">
                                        {{$product->getProductPrice()}} руб.</span></div>
                <div class="rating">
                    @for($i = 0; $i < 5; $i++)
                        <i class="fas fa-star"></i>
                    @endfor
                </div>
                <div class="cart_buttons_block"><a href="javascript:void(0)" class="btn-primary AddToCartHome">В корзину</a></div>
            </div>
        </div>
        {{--                            @break--}}
    @endforeach
@endif

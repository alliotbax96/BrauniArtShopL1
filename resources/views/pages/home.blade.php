<!-- main-area -->
    <main>

        <!-- slider-area -->
{{--        <section>--}}
{{--            <div class="container-fluid">--}}
{{--                <div class="row align-items-start justify-content-between">--}}
{{--                    <div class="col mini_banner d-none d-lg-flex"><img src="/assets/img/images/right_banner.png" alt=""></div>--}}
{{--                    <div class="col mini_banner-center"><img src="/assets/img/images/center_banner.png" alt=""></div>--}}
{{--                    <div class="col mini_banner d-none d-lg-flex"><img src="/assets/img/images/left_banner.png" alt=""></div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </section>--}}
        <!-- slider-area-end -->

        <!-- exclusive-collection-area -->
        <section class="exclusive-collection pt-10 pb-10">
            <div class="custom-container-two">
                <div class="row justify-content-center">
                </div>
                <div id="showmore-list">
                    <div>
                        <div class="row prod-list">
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
                                        <div class="exclusive--item--price"><span class="new-price">{{$product->getProductPrice()}} руб.</span></div>
                                        <div class="rating">
                                            @for($i = 0; $i < 5; $i++)
                                                <i class="fas fa-star"></i>
                                            @endfor
                                        </div>
                                        <div class="cart_buttons_block">
                                            {{-- <a href="javascript:void(0)" class="btn-primary AddToCartHome">В корзину</a>--}}
                                            <x-add-to-cart-button
                                                :product-id="$product->getProductId()"
                                                :seller-id="$product->getProductSellerId()"
                                                quantity="1"
                                                button-text="В корзину"
                                                link-class="btn-primary AddToCartHome"
                                                :options="[]"
                                            />
                                        </div>
                                    </div>
                                </div>
                                {{--                            @break--}}
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div id="showmore-triger" data-page="1" data-max="{$amt}">
                <img src="https://snipp.ru/demo/693/ajax-loader.gif" alt="">
            </div>
        </section>
        <!-- exclusive-collection-area-end -->
    </main>
    <!-- main-area-end -->

@push('scripts')
    @vite('resources/js/Pages/home.js')
@endpush

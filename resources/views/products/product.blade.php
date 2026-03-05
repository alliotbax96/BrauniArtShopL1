<main>
    <!-- breadcrumb-area -->
    <section class="breadcrumb-area breadcrumb-bg" data-background="/assets/img/bg/breadcrumb_bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-content text-center">
                        <h2>{{$product['product']->getProductName()}}</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/products">Каталог</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{$product['product']->getProductName()}}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb-area-end -->
    <!-- shop-details-area -->
    <section class="shop-details-area pt-100 pb-100">
        <div class="container">
            <div class="row mb-95">
                <div class="col-xl-7 col-lg-6">
                    <div class="shop-details-nav-wrap">
                        <div class="shop-details-nav">
                            @foreach($product['product']->getProductImages() as $image)
                                <div class="shop-nav-item">
                                    <img src="https://seller.brauniart.shop/uploads/{{$image->url}}" alt="" style="max-height: 103px; object-fit: contain">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="shop-details-img-wrap">
                        <div class="shop-details-active">
                            @foreach($product['product']->getProductImages() as $image)
                                <div class="shop-details-img">
                                    <a href="https://seller.brauniart.shop/uploads/{{$image->url}}"
                                       class="popup-image">
                                        <img src="https://seller.brauniart.shop/uploads/{{$image->url}}" alt="" style="max-height: 499px; object-fit: contain"></a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6">
                    <div class="shop-details-content">
                        <span class="stock-info">Доступен/В наличии/Нет в наличии</span>
                        <h2>{{$product['product']->getProductName()}}</h2>
                        <div class="shop-details-price">
                            <h2>{{$product['product']->getProductPrice()}} руб.</h2>
                        </div>
                        <p>Продавец: {{$product['product']->getSeller()->name}}</p>

                        <p><span class="stock-info">Цифровой товар/Склад/Услуга</span><p>
                        <div class="perched-info">
                            <div class="cart-plus">
                                <form id="adtocart">
                                    <div class="p_product-plus-minus">
                                        <input class="p_product-input" name="count" type="number" min="1" max="1" value="1">
                                    </div>
                                </form>
                            </div>

                            <input form="adtocart" type="hidden" name="product_id" value="{{$product['product']->getProductId()}}">
                            <a href="#" class="btn btn-primary ad_to_cart">В корзину</a>
                            <form id="adtocart"></form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="product-desc-wrap mb-100">
                        <ul class="nav nav-tabs mb-25" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details"
                                   role="tab" aria-controls="details" aria-selected="true">Описание</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="player-tab" data-toggle="tab" href="#playerTab" role="tab"
                                   aria-controls="player" aria-selected="false">Слушать онлайн</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="details" role="tabpanel"
                                 aria-labelledby="details-tab">
                                <div class="product-desc-content">
                                    <h4 class="title">Описание</h4>
                                    <div class="">
                                        {!!$product['product']->getProductDescription()!!}
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="playerTab" role="tabpanel" aria-labelledby="player-tab">
                                <div class="product-desc-content">
                                    <h4 class="title">Слушать онлайн</h4>
                                    <div>
                                        <div class="container tab-files">
                                            <div class="row item r" data-id="item-{$key}"
                                                 data-src="{$file['url']}">
                                                <div class="col-1 item-media primary">
                                                    <a href="javascript:void()" class="item-media-content"
                                                       style="background-image: url('https://id.brauniart.shop/uploads/{$images->getProductMainImage()->url}');"></a>
                                                    <div class="item-overlay center">
                                                        <button class="btn-playpause">Play</button>
                                                    </div>
                                                </div>
                                                <div class="col-11 item-info">
                                                    <div class="item-title text-ellipsis">
                                                        <a href="javascript:void()"></a>
                                                    </div>
                                                    <div class="item-author text-sm text-ellipsis">
                                                        <a href="javascript:void()" class="text-muted"></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <h4>/Прослушивание будет доступно после покупки книги.</h4>
                                        </div><!-- ./col-md-offset-4 -->
                                    </div><!-- ./row -->
                                </div><!-- ./container -->
                            </div>
                        </div>
                    </div>

                    <div class="product-reviews-wrap">
                        <div class="deal-day-top">
                            <div class="deal-day-title">
                                <h4 class="title">Отзывы</h4>
                            </div>
                        </div>
                        <div class="reviews-count-title">
                            <h5 class="title"></h5>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="product-review-list blog-comment">
                                    <ul>

                                        <li>
                                            <div class="single-comment">
                                                <div class="comment-text">
                                                    <div class="comment-avatar-info">
                                                        <h5> <span
                                                                class="comment-date"> - </span></h5>
                                                        <div class="rating">
                                                            @for($i = 0; $i < 5; $i++)
                                                                <i class="fas fa-star"></i>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <p></p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="product-review-form">
                                    <div id="form_add_review_result"></div>
                                    <div class="rising-star mb-40">
                                        <h5>Оценка</h5>
                                        <div class="rising-rating"></div>
                                    </div>
                                    <form id="form_review" onsubmit="return: false;">
                                        <input type="hidden" id="form_estimation" name="estimation" value="">
                                        <input type="hidden" name="product_id" value="{$product.id}">
                                        <div class="form-grp">
                                            <label for="message">Отзыв *</label>
                                            <textarea class="" name="message" id="message"
                                                      minlength="50" maxlength="1000"></textarea>
                                        </div>
                                        <button class="btn">Отправить</button>
                                    </form>
                                    /
                                    <h4>Авторизуйтесь, что бы оставлять отзывы</h4>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- shop-details-area-end -->
</main>
<!-- main-area-end -->

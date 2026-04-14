<main>

    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/products">Продукты</a></li>
                <li class="breadcrumb-item"><a href="/products?category={{$product['productWithConditions']->getProductGroup()[0]->parent->id}}">{{$product['productWithConditions']->getProductGroup()[0]->parent->name}}</a></li>
                <li class="breadcrumb-item"><a href="/products?category={{$product['productWithConditions']->getProductGroup()[0]->id}}">{{$product['productWithConditions']->getProductGroup()[0]->name}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$product['productWithConditions']->getProductName()}}</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">{{$product['productWithConditions']->getProductName()}}</h1>
    </div>
    <!-- breadcrumb-area-end -->

    <!-- shop-details-area -->
    <section class="shop-details-area pb-100">
        <div class="custom-container-two">
            <div class="row mb-95">
                <div class="col-xl-7 col-lg-6">
                    <div class="shop-details-nav-wrap">
                        <div class="shop-details-nav">
                            @foreach($product['productWithConditions']->getProductImages() as $image)
                                <div class="shop-nav-item">
                                    <img src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{$image->url}}" alt="" style="max-height: 103px; object-fit: contain">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="shop-details-img-wrap">
                        <div class="shop-details-active">
                            @foreach($product['productWithConditions']->getProductImages() as $image)
                                <div class="shop-details-img">
                                    <a href="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{$image->url}}"
                                       class="popup-image">
                                        <img src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{$image->url}}" alt="" style="max-height: 499px; object-fit: contain"></a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6">
                    <div class="shop-details-content">
                        <span class="stock-info">
                            @if($product['productWithConditions']->getProductQuantity() > 0)
                            В наличии
                            @else
                            Нет в наличии
                            @endif
                        </span>
                        <h2>{{$product['productWithConditions']->getProductName()}}</h2>
                        <div class="shop-details-review">
                            <div class="rating">
                                @php
                                    $rating = $product['productWithConditions']->getProductEstimation(); // Получаем оценку товара
                                    $fullStars = floor($rating); // Целые звёзды
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5; // Есть ли половинка?
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $fullStars)
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif($hasHalfStar && $i == $fullStars + 1)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="fas fa-star text-muted"></i>
                                    @endif
                                @endfor
                            </div>
                            <span>
                              @php
                                  $count = count($product['productWithConditions']->getProductReviews());
                                  $word = match (true) {
                                      $count % 10 == 1 && $count % 100 != 11 => 'отзыв',
                                      in_array($count % 10, [2, 3, 4]) && !in_array($count % 100, [12, 13, 14]) => 'отзыва',
                                      default => 'отзывов'
                                  };
                              @endphp
                                - {{ $count }} {{ $word }}
                            </span>
                        </div>
                        <div class="shop-details-price">
                            <h2>{{$product['productWithConditions']->getProductPrice()}} руб.</h2>
                        </div>
                        <p></p>

                        <div class="product-details-size mb-40">
                            <span>Помол : </span>
                            <a href="#">Инструкция</a>
                            <ul>
                                <li><a href="#">Турка</a></li>
                                <li><a href="#">Эспрессо</a></li>
                                <li><a href="#">Гейзер</a></li>
                                <li><a href="#">Френч-пресс</a></li>
                            </ul>
                        </div>

                        <p>Продавец: {{$product['productWithConditions']->getSeller()->name}}</p>
                        <div class="perched-info">
                            <div class="cart-plus">
                                <form id="adtocart">
                                    <div class="p_product-plus-minus">
                                        <input class="p_product-input" name="count" type="number" min="1" max="{{$product['productWithConditions']->getProductQuantity()}}" value="1">
                                    </div>
                                </form>
                            </div>
                            <input form="adtocart" type="hidden" name="product_id" value="{{$product['productWithConditions']->getProductId()}}">
                            <x-add-to-cart-button
                                :product-id="$product['productWithConditions']->getProductId()"
                                :seller-id="$product['productWithConditions']->getProductSellerId()"
                                quantity="1"
                                button-text="В корзину"
                                link-class="btn-primary"
                                :options="[]"
                            />
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
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="details" role="tabpanel"
                                 aria-labelledby="details-tab">
                                <div class="product-desc-content">
                                    <h4 class="title">Описание</h4>
                                    <div class="">
                                        {!!$product['productWithConditions']->getProductDescription()!!}
                                    </div>
                                </div>
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
                                    @foreach($product['productWithConditions']->getProductReviews() as $review)
                                        <li>
                                            <div class="single-comment">
                                                <div class="comment-text">
                                                    <div class="comment-avatar-info">
                                                        <h5>{{$review->user->name}}<span class="comment-date"> {{$review->created_at}}</span></h5>
                                                        <div class="rating">
                                                            @php
                                                                $rating = $review->estimation; // Получаем оценку товара
                                                                $fullStars = floor($rating); // Целые звёзды
                                                                $hasHalfStar = ($rating - $fullStars) >= 0.5; // Есть ли половинка?
                                                            @endphp
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= $fullStars)
                                                                    <i class="fas fa-star text-warning"></i>
                                                                @elseif($hasHalfStar && $i == $fullStars + 1)
                                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                                @else
                                                                    <i class="fas fa-star text-muted"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <p>
                                                        {{$review->comment}}
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="product-review-form">
                                    @if(isset($currentUser->id))
                                    <div class="rising-star mb-40">
                                        <h5>Оценка</h5>
                                        <div class="rising-rating"></div>
                                    </div>

                                    <form id="form_review" method="POST" action="/products/{{$product['productWithConditions']->id}}/reviews">
                                        <input type="hidden" id="form_estimation" name="estimation" value="">
                                        @csrf
                                        <div class="form-grp">
                                            <label for="message">Отзыв *</label>
                                            <textarea class="" name="comment" id="message"
                                                      minlength="50" maxlength="1000"></textarea>
                                        </div>
                                        <button class="btn">Отправить</button>
                                    </form>
                                    @else
                                    <h4>Авторизуйтесь, что бы оставлять отзывы</h4>
                                    @endif
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
@push('scripts')
    @vite('resources/js/Pages/product.js')
@endpush

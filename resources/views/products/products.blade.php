<!-- main-area -->
<main>
    <!-- breadcrumb-area -->
    <section class="breadcrumb-area breadcrumb-bg" data-background="/assets/img/bg/breadcrumb_bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-content text-center">
                        <h2>Каталог</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Каталог</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb-area-end -->

    <!-- shop-area -->
    <div class="shop-area gray-bg pt-50 pb-100">
        <div class="custom-container-two">
            <div class="shop-top-meta mb-40">
                <p class="show-result"></p>
                <div class="shop-meta-right">
                    <form id="filters" action="/products" method="get">
                        <select class="custom-select perPageSelect" name="perPage">
                            <option value="8" @if($products['perPage'] == 8) selected @endif>8</option>
                            <option value="12" @if($products['perPage'] == 12) selected @endif>12</option>
                            <option value="16" @if($products['perPage'] == 16) selected @endif>16</option>
                            <option value="20" @if($products['perPage'] == 20) selected @endif>20</option>
                            <option value="24" @if($products['perPage'] == 24) selected @endif>24</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8 order-2 order-lg-0">
                    <aside class="shop-sidebar">
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Категории</h6>
                            </div>

                            <div class="shop-cat-list">
                                <ul class="treeview">
                                    @foreach($ProductGroups as $item)
                                        @if($item->parent_id == 0)
                                            <li class="has-children">
                                                <label class="category-toggle">
                                                    <input form="filters" type="radio" name="category" value="{{ $item->id }}" @if($item->id == $products['filters']['category']) checked @endif>
                                                    <span class="category-name">{{ $item->name }}</span>
                                                    <span class="toggle-icon">@if ($item->children->isNotEmpty())▶@endif</span>
                                                </label>
                                                <!-- Вложенные категории -->
                                                @if ($item->children->isNotEmpty())
                                                    <ul class="children">
                                                        @foreach($item->children as $child)
                                                            <li>
                                                                <label>
                                                                    <input form="filters" type="radio" name="category" value="{{ $child->id }}" @if($child->id == $products['filters']['category']) checked @endif>
                                                                    <span class="category-name">{{ $child->name }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>


                        </div>
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Цена</h6>
                            </div>
                            <div class="price_filter">
                                <div id="slider-range"></div>
                                <div class="price_slider_amount">
                                    <span>Цена :</span>
                                    <input type="text" id="amount" name="price" placeholder="Add Your Price" />
                                    <input form="filters" type="hidden" name="min_price" value="{{$products['filters']['min_price']}}">
                                    <input form="filters" type="hidden" name="max_price" value="{{$products['filters']['max_price']}}">
                                </div>
                            </div>
                            <br>
                            <input form="filters" type="submit" class="btn" value="Показать">
                        </div>
                    </aside>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="row list">
                        @foreach($products['products'] as $product)
                            <div class="col-xl-3 col-lg-6 col-md-4 col-sm-6">
                                <div class="exclusive-item exclusive-item-three text-center mb-50 card-item">
                                    <div class="exclusive-item-thumb">
                                        <a href="/products/{{$product->id}}"><img src="https://seller.brauniart.shop/uploads/{{$product->getMainImage()}}" alt="" style="object-fit: contain; height: 300px;"></a>
                                    </div>
                                    <div class="exclusive-item-content">
                                        <h5 style="font-size: 12px;"><a href="/products/{{$product->id}}">{{$product->getProductName()}}</a></h5>
                                        <p></p>
                                        <div class="exclusive--item--price">
                                            <span class="new-price">{{$product->getProductPrice()}} руб.</span>
                                        </div>
                                        <div class="rating">
                                            @for($i = 0; $i < 5; $i++)
                                                <i class="fas fa-star"></i>
                                            @endfor
                                        </div>
                                        <div class="cart_buttons_block">
{{--                                            <a href="#" class="btn-primary adtocart">В корзину</a>--}}
                                            <x-add-to-cart-button
                                                :product-id="$product->getProductId()"
                                                :seller-id="$product->getProductSellerId()"
                                                quantity="1"
                                                button-text="В корзину"
                                                link-class="btn-primary adtocart"
                                                :options="[]"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pagination-wrap">{{ $products['products']->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- shop-area-end -->
</main>
<!-- main-area-end -->

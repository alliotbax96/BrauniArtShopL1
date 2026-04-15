<!-- main-area -->
<main>

    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Корзина</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Корзина</h1>
    </div>
    <!-- breadcrumb-area-end -->

    <!-- shop-cart-area -->
    <section class="shop-cart-area wishlist-area pb-100">
        <div class="custom-container-two" style="margin-bottom: 25px;">
            <div class="collapse" id="collapseExample">
                <div id="pvz_map">Проблема при загрузке карты!</div>
            </div>
        </div>
        <form action="{{route('checkout.index')}}" method="post" id="CartForm">@csrf</form>
        @if($cartItemsCount <= 0)
            <div class="container my-5">
                <div class="position-relative p-5 text-center text-muted bg-body border border-dashed rounded-5">
                    <h1 class="text-body-emphasis">Ой! Кажется корзина пуста!</h1>
                    <p class="col-lg-6 mx-auto mb-4">
                        Добавьте в корзину товары из каталога и возвращайтесь.
                    </p>
                    <a class="btn btn-primary px-5 mb-5" href="/products">
                        В каталог
                    </a>
                </div>
            </div>
        @else
            <div class="custom-container-two">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="table-responsive-xl">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" name="all" checked></th>
                                    <th class="product-thumbnail"></th>
                                    <th class="product-name">Наименование</th>
                                    <th class="product-price">Цена</th>
                                    <th class="product-quantity">Количество</th>
                                    <th class="product-subtotal">Сумма</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cart->items as $key => $item)
                                    <tr data-item-id="{{ $item->id }}">
                                        <td>
                                            <input form="CartForm" type="checkbox" name="cart_items[]"
                                                   value="{{ $item->id }}" checked>
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show', $item->getProduct()->id) }}">
                                                <img
                                                    src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{ $item->getProduct()->getMainImage() }}"
                                                    alt="{{ $item->getProduct()->getProductName() }}"
                                                    style="object-fit: contain; height: 129px; width: 103px;">
                                            </a>
                                        </td>
                                        <td class="product-name">
                                            <h4>
                                                <a href="{{ route('products.show', $item->getProduct()->id) }}">{{ $item->getProduct()->getProductName() }}</a>
                                            </h4>
                                            <p></p>
                                        </td>
                                        <td class="product-price">
                                            <span
                                                id="price{{ $item->id }}">{{ number_format($item->getProduct()->getProductPrice(), 2, ',', ' ') }}</span>
                                            руб.
                                        </td>
                                        <td class="product-quantity">
                                            <div class="cart-plus">
                                                <div class="cart-plus-minus">
                                                    <input type="number"
                                                           form="CartForm"
                                                           class="item_count"
                                                           data-id="{{ $item->id }}"
                                                           name="quantities[{{ $item->id }}]"
                                                           value="{{ $item->quantity }}"
                                                           min="1"
                                                           max="{{ $item->getProduct()->stock }}"
                                                           onchange="updateCartItem({{ $item->id }}, this.value)">
                                                </div>
                                                {{--                                                    @endif--}}
                                            </div>
                                        </td>
                                        <td class="product-subtotal">
                                            <span
                                                id="sum{{ $item->id }}">{{ number_format($item->getProduct()->getProductPrice() * $item->quantity, 2, ',', ' ') }}</span>
                                            руб.
                                        </td>
                                        <td class="product-thumbnail">
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                                                  style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button class="wishlist-remove cart_item_del skip-ajax" type="submit"
                                                        style="background: none; border: none; cursor: pointer;">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="shop-cart-bottom mt-20">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="cart-coupon">
                                        {{--                                            <form action="{{ route('cart.applyCoupon') }}" method="POST">--}}
                                        {{--                                                @csrf--}}
                                        {{--                                                <input type="text" name="coupon_code" placeholder="Enter Coupon Code...">--}}
                                        {{--                                                <button class="btn">Apply Coupon</button>--}}
                                        {{--                                            </form>--}}
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="continue-shopping">
                                        <a href="/products" class="btn">Продолжить покупки</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-8">
                        <aside class="shop-cart-sidebar">
                            <div class="shop-cart-widget">
                                <h6 class="title">Итого:</h6>
                                <div id="alert"></div>
                                <form>
                                    <ul>
                                        <li>
                                            <span>Подытог: </span>
                                            <div class="shop-check-wrap">
                                                <div class="custom-control">
                                                    <span
                                                        id="pitog">{{ number_format($cartTotalPrice, 2, ',', ' ') }}</span>
                                                    руб.
                                                </div>
                                            </div>
                                        </li>

                                        @auth
                                            <li>
                                                <select form="CartForm" name="pvz" id="pvz" class="form-control point-select">
                                                    <option disabled>Выберите пункт выдачи</option>
                                                    @foreach($userPvzs as $pvz)
                                                        <option value="{{$pvz['pvz']}}"
                                                                @if($pvz['last']) selected @endif>{{$pvz['pvz_name']}}</option>
                                                    @endforeach
                                                </select>
                                            </li>
                                            <li><a style="font-size: 12px;" href="javascript:$('.collapse').show();">Добавить
                                                    новый пункт выдачи</a></li>
                                            <li>
                                                <span>Доставка:</span>
                                                <div class="shop-check-wrap">
                                                    <div class="custom-control">
                                                        <span>Стоимость доставки будет рассчитана на следующем этапе.</span>
                                                    </div>
                                                </div>
                                            </li>
                                        @else
                                            <li class="cart-total-amount">Для оформления заказа нужно авторизоваться!
                                            </li>
                                        @endauth
                                    </ul>
                                    <br>
                                    <div id="checkout_buttons">
                                        @auth
                                            <button form="CartForm" type="submit" id="checkout_button" class="btn">К
                                                оформлению
                                            </button>
                                        @else
                                            <a href="/auth" class="btn">Авторизоваться</a>
                                        @endauth
                                    </div>
                                </form>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
            {{--            </form>--}}
        @endif
    </section>
    <!-- shop-cart-area-end -->
</main>
<!-- main-area-end -->


@push('scripts')
    @vite('resources/js/Pages/cart.js')
@endpush

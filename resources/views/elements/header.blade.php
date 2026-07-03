<!-- menu-area -->
<div id="sticky-header" class="main-header menu-area container-header pt-3">
    <div class="custom-container-two">
        <div class="row">
            <div class="col-12">
                <div class="mobile-nav-toggler"><i class="fas fa-bars"></i></div>
                <div class="menu-wrap">
                    <nav class="menu-nav show">
                        <div class="logo">
                            <a href="/"><img src="/assets/img/logo/logo.png" alt="Logo"></a>
                        </div>
                        <div class="header-category d-none d-lg-flex">
                            <x-miniCatalog
                                :ProductGroups="$ProductGroups"
                                :shopMode="Cookie::get('ShopMode')"
                            />
                        </div>
                        <div class="col-md-auto mx-1 header-search-wrap d-none d-lg-flex">
                            <x-search
                                :shopMode="Cookie::get('ShopMode')"
                            />
                        </div>
                        <div class="header-action d-none d-md-block">
                            <ul>
                                <li class="dropdown tooltip-wrapper">
                                    <a href="@if(!$currentUser) /auth @else javascript:void(0) @endif"
                                    @if($currentUser) data-bs-toggle="dropdown" aria-expanded="false" @endif>
                                        <i data-feather="user"></i>
                                        <span class="tooltip-text">
                                            @if($currentUser)
                                                Учетная запись
                                            @else
                                                Авторизация
                                            @endif
                                        </span>
                                    </a>
                                    @if($currentUser)
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('sso.initiate') }}"
                                                   target="_blank">Учетная запись</a>
                                            </li>
                                            <li style="margin-left: 0;">
                                                <a class="dropdown-item" href="/logout">Выход</a>
                                            </li>
                                        </ul>
                                    @endif
                                </li>
                                @switch($CurrentShopMode)
                                    @case(1)
                                        <li class="tooltip-wrapper">
                                            <a href="@if($currentUser) /orders @else /auth @endif">
                                                <i class="flaticon-shopping-bag"></i>
                                                <span class="tooltip-text">Ваши заказы</span>
                                            </a>
                                        </li>
                                        <li class="header-shop-cart tooltip-wrapper">
                                            <a href="/cart/">
                                                <i class="flaticon-shopping-bags"></i>
                                                <span class="cart-count">{{$cartItemsCount}}</span>
                                                <span class="tooltip-text">Корзина</span>
                                            </a>
                                            <span class="cart-total-price">{{$cartTotalPrice}}</span>
                                            @include('elements.miniCart')
                                        </li>
                                    @break

                                    @case(8)
                                        <li class="tooltip-wrapper">
                                            <a href="@if($currentUser) /bookOrders @else /auth @endif">
                                                <i data-feather="book"></i>
                                                <span class="tooltip-text">Мои книги</span>
                                            </a>
                                        </li>
                                        <li class="header-shop-cart tooltip-wrapper">
                                            <a href="/cart/" class="d-flex align-items-center gap-2 text-decoration-none">
                                                <div class="position-relative d-inline-flex align-items-center">
                                                    <i data-feather="shopping-bag" width="22" height="22"></i>
                                                    <span class="position-absolute translate-middle badge rounded-pill bg-primary p-1"
                                                          style="top: -2px; right: -12px; min-width: 18px; font-size: 0.65rem;">
                                                        {{$cartItemsCount}}
                                                    </span>
                                                </div>
                                                <span class="cart-total-price small">{{$cartTotalPrice}}</span>
                                            </a>
                                            <span class="tooltip-text">Корзина</span>
                                            @include('elements.miniCart')
                                        </li>
                                    @break

                                    @case(4)
                                        <li class="tooltip-wrapper">
                                            <a href="@if($currentUser) /bookings @else /auth @endif">
                                                <i class="flaticon-calendar"></i>
                                                <span class="tooltip-text">Ваши бронирования</span>
                                            </a>
                                        </li>
                                    @break
                                @endswitch
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="menu-warp main-menu-warp pb-3">
                    <nav class="menu-nav show">
                        <div class="navbar-wrap main-menu d-none d-lg-flex">
                            <ul class="navigation">
                                @switch($CurrentShopMode)
                                    @case(4)
                                        <li class="mobile_m">
                                            <a href="/quests">
                                                Все квесты
                                            </a>
                                        </li>
                                    @break
                                    @default
                                        <li class="dropdown mobile_m">
                                            <a href="#">
                                                {{ Cookie::get('ShopMode') == 1 ? 'Каталог' : 'Жанры' }}
                                            </a>
                                            <ul class="submenu">
                                                @foreach($ProductGroups->where('parent_id', 0)->where('ShopMode', Cookie::get('ShopMode')) as $item)
                                                    @if ($item->children->isNotEmpty())
                                                        <li class="dropdown">
                                                            <a href="/products">
                                                                {{$item->name}}
                                                            </a>
                                                            <ul class="submenu">
                                                                @foreach($item->children as $children)
                                                                    <li>
                                                                        <a href="/products?category={{$children->id}}">
                                                                            {{$children->name}}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="/products?category={{$item->id}}">
                                                                {{$item->name}}
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </li>
                                    @break
                                @endswitch
                                @foreach($ShopModes as $ShopMode)
                                <li class="@if($CurrentShopMode == $ShopMode['id']) active @endif"><a
                                            href="/shopMode/{{$ShopMode['id']}}">{{$ShopMode['ShopModeName']}}</a></li>
                                @endforeach
                                <li class="@if(Route::currentRouteName() == 'aboutUs') active @endif">
                                    <a href="/aboutUs">О нас</a>
                                </li>
                                <li class="@if(Route::currentRouteName() == 'PayAndDelivery') active @endif">
                                    <a href="/PayAndDelivery">Доставка и оплата</a>
                                </li>
                                <li class="@if(Route::currentRouteName() == 'contacts') active @endif">
                                    <a href="/contacts">Контакты</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
                <!-- Mobile Menu  -->
                <div class="mobile-menu">
                    <div class="menu-backdrop"></div>
                    <div class="close-btn"><i class="fas fa-times"></i></div>

                    <nav class="menu-box">
                        <div class="nav-logo"><a href="/"><img
                                    src="/assets/img/logo/logo.png" alt="" title=""></a>
                        </div>
                        <div class="header-action">
                            <ul>
                                <li class="dropdown tooltip-wrapper">
                                    <a href="@if(!$currentUser) /auth @else javascript:void(0) @endif"
                                       @if($currentUser) data-bs-toggle="dropdown" aria-expanded="false" @endif>
                                        <i data-feather="user"></i>
                                        <span class="tooltip-text">
                                            @if($currentUser)
                                                Учетная запись
                                            @else
                                                Авторизация
                                            @endif
                                        </span>
                                    </a>
                                    @if($currentUser)
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('sso.initiate') }}"
                                                   target="_blank">Учетная запись</a>
                                            </li>
                                            <li style="margin-left: 0;">
                                                <a class="dropdown-item" href="/logout">Выход</a>
                                            </li>
                                        </ul>
                                    @endif
                                </li>
                                @switch($CurrentShopMode)
                                    @case(1)
                                        <li>
                                            <a href="@if($currentUser) /orders @else /auth @endif">
                                                <i class="flaticon-shopping-bag"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/auth">
                                                <i class="flaticon-heart"></i>
                                            </a>
                                        </li>
                                        <li class="header-shop-cart">
                                            <a href="/cart/">
                                                <i class="flaticon-shopping-bags"></i>
                                                <span class="cart-count">0</span>
                                            </a>
                                            <span class="cart-total-price">0</span>
                                        </li>
                                    @break
                                    @case(8)
                                        <li class="tooltip-wrapper">
                                            <a href="@if($currentUser) /bookOrders @else /auth @endif">
                                                <i data-feather="book"></i>
                                                <span class="tooltip-text">Мои книги</span>
                                            </a>
                                        </li>
                                        <li class="header-shop-cart tooltip-wrapper">
                                            <a href="/cart/" class="d-flex align-items-center gap-2 text-decoration-none">
                                                <div class="position-relative d-inline-flex align-items-center">
                                                    <i data-feather="shopping-bag" width="22" height="22"></i>
                                                    <span class="position-absolute translate-middle badge rounded-pill bg-primary p-1"
                                                          style="top: -2px; right: -12px; min-width: 18px; font-size: 0.65rem;">
                                                        {{$cartItemsCount}}
                                                    </span>
                                                </div>
                                                <span class="cart-total-price small">{{$cartTotalPrice}}</span>
                                            </a>
                                            <span class="tooltip-text">Корзина</span>
                                            @include('elements.miniCart')
                                        </li>
                                    @break
                                    @case(4)
                                        <li>
                                            <a href="/bookings">
                                                <i class="flaticon-calendar"></i>
                                            </a>
                                        </li>
                                        @break
                                @endswitch
                            </ul>
                        </div>
                        <br>
                        <div class="menu-outer">
                            <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
                        </div>
                        <div class="social-links">
                            <ul class="clearfix">
                                <li><a href="https://vk.com/brauniartshop"><i class="fab fa-vk"></i></a></li>
                                <li><a href="https://t.me/brauniartshop"><i class="fab fa-telegram"></i></a></li>
                                <li><a href="https://www.youtube.com/@ShilovMax"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </nav>
                </div>
                <!-- End Mobile Menu -->
            </div>
            <div id="autocomplete-results" class="autocomplete-dropdown"></div>
        </div>
    </div>
</div>
<!-- menu-area-end -->

<!-- header-search-area -->
<div class="header-search-area mobile_m">
    <form id="mobile-search_form" action="/products" method="get"></form>
    <div class="container mobile-search">
        <div class="header-search-wrap">
            <div>
                <input form="mobile-search_form" id="search-mobile-input" list="search" name="search" type="text"
                       placeholder="Введите ваш поисковый запрос.....">
                <button form="mobile-search_form" type="submit"><i class="flaticon-magnifying-glass-1"></i></button>
            </div>
        </div>
    </div>
</div>
<div id="autocomplete-results-mobile" class="autocomplete-dropdown mobile_m"></div>
<!-- header-search-area-end -->

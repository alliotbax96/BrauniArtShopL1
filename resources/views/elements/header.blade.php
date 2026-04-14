<!-- header-top -->
<div class="header-top-area">
    <div class="custom-container-two">
        <div class="row">
            <div class="col-md-8 col-sm-7">
                <div class="header-top-left">
                    <ul>
                        <li>
                            <div class="heder-top-guide">
                                <div class="dropdown">
                                    <button class="dropdown-toggle" type="button" id="dropdownMenuButton2"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Помощь
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                        <a class="dropdown-item" href="https://support.brauniart.shop/">База знаний</a>
                                        <a class="dropdown-item"
                                           href="https://t.me/BrauniArtShop">Поддержка</a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="heder-top-guide">
                                <div class="dropdown">
                                    <button class="dropdown-toggle" type="button" id="dropdownMenuButton3"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Продавайте с нами
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                        <a class="dropdown-item" href="//id.brauniart.shop">Кабинет
                                            продавца</a>
                                    </div>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>

            <div class="col-md-4 col-sm-5">
                <div class="header-top-right">
                    <ul>
                        <li>
                            @if($currentUser)
                                <input type="hidden" id="authuserid" value="{$auth_user.id}">
                                <a href="//id.brauniart.shop" target="_blank"><i
                                        class="flaticon-user"></i>{{$currentUser->name}}</a>
                                |
                                <a href="/cart" target="_blank"><i class="flaticon-shopping-bags"></i>Корзина</a>
                                |
                                <a href="/logout">Выйти</a>
                            @else
                                <a href="/auth/"><i class="flaticon-user"></i>Войти через BaID</a>
                            @endif
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- header-top-end -->

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
                            <a href="#" class="btn-catalog cat-toggle">КАТАЛОГ</a>
                            @include('elements.miniCatalog')
                        </div>
                        <div class="col-md-auto mx-1 header-search-wrap d-none d-lg-flex">
                            <div>
                                <form id="search_form" action="/products" method="get"></form>
                                <input form="search_form" id="search-input" list="search" name="search" type="text"
                                       placeholder="Введите ваш поисковый запрос.....">
                                <button form="search_form" type="submit"><i class="flaticon-magnifying-glass-1"></i>
                                </button>
                            </div>
                        </div>
                        <div class="header-action d-none d-md-block">
                            <ul>
                                <li><a href="@if($currentUser) /orders @else /auth @endif"><i
                                            class="flaticon-shopping-bag"></i></a></li>
                                <li><a href="@if($currentUser) /dev @else /auth @endif"><i
                                            class="flaticon-heart"></i></a></li>
                                <li class="header-shop-cart">
                                    <a href="/cart/">
                                        <i class="flaticon-shopping-bags"></i>
                                        <span class="cart-count">{{$cartItemsCount}}</span>
                                    </a>
                                    <span class="cart-total-price">{{$cartTotalPrice}}</span>
                                     @include('elements.miniCart')
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="menu-warp main-menu-warp pb-3">
                    <nav class="menu-nav show">
                        <div class="navbar-wrap main-menu d-none d-lg-flex">
                            <ul class="navigation">
                                @foreach($ShopModes as $ShopMode)
                                    <li class="active"><a href="/selectMode/{{$ShopMode['id']}}">{{$ShopMode['ShopModeName']}}</a></li>
                                @endforeach
                                <li class="dropdown mobile_m"><a href="#">Каталог</a>
                                    <ul class="submenu">
                                        @foreach($ProductGroups as $item)
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
                                <li class="{if $url1 == 'aboutUs'}active{/if}"><a href="/aboutUs">О нас</a></li>
                                <li class="{if $url1 == 'PayAndDelivery'}active{/if}"><a
                                        href="/PayAndDelivery">Доставка и оплата</a></li>
                                <li class="{if $url1 == 'contacts'}active{/if}"><a href="/contacts">Контакты</a>
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
                                <li><a href="@if($currentUser) /orders @else /auth @endif"><i
                                            class="flaticon-shopping-bag"></i></a></li>
                                <li><a href="/auth"><i class="flaticon-heart"></i></a></li>
                                <li class="header-shop-cart"><a href="/cart/"><i class="flaticon-shopping-bags"></i>
                                        <span class="cart-count">0</span></a>
                                    <span class="cart-total-price">0</span>
                                </li>
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

<html class="no-js" lang="ru">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{$title}}</title>
    <meta name="description" content="{{$meta_description ? $meta_description : ''}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Фавиконы (оставляем как есть) -->
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/icons/favicon-16x16.png">
    <link rel="manifest" href="/assets/img/icons/site.webmanifest">
    <link rel="mask-icon" href="/assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/assets/img/icons/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="/assets/img/icons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <!-- Vite CSS -->
    @vite(['resources/css/app.css'])
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto:wght@300;400;500&display=swap"
        rel="stylesheet">

    <!-- Внешние скрипты (оставляем как есть, но исправляем протокол) -->
    <script src="https://api-maps.yandex.ru/2.1/?apikey=ddf6f3c5-7470-4e2c-b2a9-359a8c35e699&lang=ru_RU"></script>
    <script async src="https://ndd-widget.landpro.site/widget.js"></script>
</head>

@php
    if (isset($shopMode)) {
     $CurrentShopMode = $shopMode;
    } elseif (Cookie::get('ShopMode') !== null) {
     $CurrentShopMode = Cookie::get('ShopMode');
    } else {
     $CurrentShopMode = 1;
    }
@endphp

<body class=" @if($CurrentShopMode == 4) dark-theme @endif ">

<!-- preloader  -->
<div id="preloader">
    <div id="ctn-preloader" class="ctn-preloader">
        <div class="animation-preloader">
            <div class="spinner"></div>
            <div class="txt-loading">
                <span data-text-preloader="B" class="letters-loading">
                        B
                    </span>
                <span data-text-preloader="R" class="letters-loading">
                        R
                    </span>
                <span data-text-preloader="A" class="letters-loading">
                        A
                    </span>
                <span data-text-preloader="U" class="letters-loading">
                        U
                    </span>
                <span data-text-preloader="N" class="letters-loading">
                        N
                    </span>
                <span data-text-preloader="I" class="letters-loading">
                        I
                    </span>
                <span data-text-preloader="A" class="letters-loading">
                        A
                    </span>
                <span data-text-preloader="R" class="letters-loading">
                        R
                    </span>
                <span data-text-preloader="T" class="letters-loading">
                        T
                    </span>
                <span data-text-preloader="&nbsp;" class="letters-loading">
                        &nbsp;
                    </span>
                <span data-text-preloader="S" class="letters-loading">
                        S
                    </span>
                <span data-text-preloader="H" class="letters-loading">
                        H
                    </span>
                <span data-text-preloader="O" class="letters-loading">
                        O
                    </span>
                <span data-text-preloader="P" class="letters-loading">
                        P
                    </span>
            </div>
        </div>
        <div class="loader">
            <div class="row">
                <div class="col-3 loader-section section-left">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-left">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-right">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-right">
                    <div class="bg"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- preloader end -->


<!-- Scroll-top -->
<button class="scroll-top scroll-to-target" data-target="html">
    <i class="fas fa-angle-up"></i>
</button>
<!-- Scroll-top-end-->

<!-- header-area -->
<header class="header-style-two">
    @include('elements.header')
</header>
<!-- header-area-en@end -->
<div class="main_alert"></div>
@if(isset($view))
    @include($view)
@endif

{{--modal--}}
@if(isset($modal))
    @include($modal)
@endif
{{--end-modal--}}

<!-- footer-area -->
<footer class="footer-area">
    <div class="footer-top pt-65 pb-25">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget mb-50">
                        <div class="footer-logo mb-30">
                            <a href="/"><img src="/assets/img/logo/logo.png" alt=""></a>
                        </div>
                        <div class="footer-text mb-35">
                            <p>Маркетплейс от Брауни Арт!</p>
                        </div>
                        <div class="footer-social">
                            <ul>
                                <li><a href="https://vk.com/brauniartshop"><i class="fab fa-vk"></i></a></li>
                                <li><a href="https://t.me/brauniartshop"><i class="fab fa-telegram"></i></a></li>
                                <li><a href="https://www.youtube.com/@ShilovMax"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-sm-6">
                    <div class="footer-widget mb-50">
                        <div class="fw-title mb-35">
                            <h5>Сервис</h5>
                        </div>
                        <div class="fw-link">
                            <ul>
                                {{--                                <li><a href="https://t.me/brauniartshop" target="_blank">Поддержка</a></li>--}}
                                <li><a href="/refunds">Возвраты</a></li>
                                <li><a href="/assets/docs/conditions.pdf" target="_blank">Условия
                                        покупки</a></li>
                                <li><a href="/assets/docs/recurrent.pdf" target="_blank">Рекуриентные
                                        платежи</a></li>
                                <li><a href="/assets/docs/confidential.pdf" target="_blank">Политика
                                        конфиденциальности</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-sm-6">
                    <div class="footer-widget mb-50">
                        <div class="fw-title mb-35">
                            <h5>Информация</h5>
                        </div>
                        <div class="fw-link">
                            <ul>
                                <li><a href="/orders">Заказы</a></li>
                                <li><a href="/PayAndDelivery">Оплата и доставка</a></li>
                                {{--                                <li><a href="/dev">Избранное</a></li>--}}
                                <li><a href="//id.brauniart.shop" target="_blank">Кабинет продавца</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="footer-widget mb-50">
                        <div class="fw-title mb-35">
                            <h5>О компании</h5>
                        </div>
                        <div class="footer-contact">
                            <ul>
                                <li><i class="fa fa-building"></i>
                                    ООО "БРАУНИ АРТ МАГАЗИН"
                                    <br>ИНН: 7751222627/690001001
                                    <br>ОРГН: 1227700273052
                                </li>
                                <li><i class="fas fa-envelope-open"></i>support@brauniart.shop</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-wrap copyright-style-two">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <div class="copyright-text">
                        <p><a href="/">БРАУНИАРТ МАГАЗИН</a> VER {{$verName}} | Все права защищены {{date('Y')}} |
                            Разработано <a
                                href="//albax-s.ru">ALBAX Studio</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- footer-area-end -->

{{--<div class="app-footer app-player grey bg">--}}
{{--    <div class="playlist" style="width:100%"></div>--}}
{{--</div>--}}

<!-- В конце body -->
@vite('resources/js/app.js')

<!-- Внешние скрипты (оставляем как есть) -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@cdek-it/widget@3" charset="utf-8"></script>

@stack('scripts')
@if(session('error'))
    <div class="notification"
         style="position: fixed; top: 20px; right: 20px; padding: 15px 20px; background-color: #F44336; color: white; border-radius: 4px; z-index: 10000; box-shadow: rgba(0, 0, 0, 0.2) 0px 2px 10px;">{{session('error')}}</div>
    <script type="module">
        $(document).ready(function () {
            setTimeout(() => {
                $(".notification").remove();
            }, 5000);
        });
    </script>
@endif
@if(!empty($productSchema))
    <script type="application/ld+json">
        {!! $productSchema !!}
    </script>
@endif

</body>
</html>

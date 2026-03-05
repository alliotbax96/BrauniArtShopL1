 <html class="no-js" lang="ru">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>{{$title}}</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/icons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/icons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/icons/favicon-16x16.png">
        <link rel="manifest" href="/assets/img/icons/site.webmanifest">
        <link rel="mask-icon" href="/assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="shortcut icon" href="/assets/img/icons/favicon.ico">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="msapplication-config" content="/assets/img/icons/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">
        <!-- Place favicon.ico in the root directory -->

        <!-- CSS here -->
        <link rel="stylesheet" href="/assets/css/bootstrap.min.css?{$ver}">
        {{--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">--}}
        <link rel="stylesheet" href="/assets/css/animate.min.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/magnific-popup.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/fontawesome-all.min.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/owl.carousel.min.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/jquery-ui.min.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/flaticon.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/odometer.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/aos.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/slick.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/default.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/style.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/tech.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/responsive.css?{$ver}">
        <link rel="stylesheet" href="/assets/css/font-awesome/css/font-awesome.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/mediaelement/build/mediaelementplayer.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/mediaelement/build/mep.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/jquery.dataTables.css">
        <script src="https://api-maps.yandex.ru/2.1/?apikey=ddf6f3c5-7470-4e2c-b2a9-359a8c35e699&lang=ru_RU"></script>
        <script async src="https://ndd-widget.landpro.site/widget.js"></script>
        <script src="https://unpkg.com/feather-icons"></script>
        @vite(['resources/js/app.js'])
    </head>

    <body>

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
                                    <li><a href="https://t.me/brauniartshop" target="_blank">Поддержка</a></li>
                                    <li><a href="/vozvrat">Возвраты</a></li>
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
                                    <li><a href="{if $auth_user.id}/orders/{else}/auth/{/if}">Заказы</a></li>
                                    <li><a href="/PayAndDelivery">Оплата и доставка</a></li>
                                    <li><a href="/dev">Избранное</a></li>
                                    <li><a href="//seller.brauniart.shop" target="_blank">Кабинет продавца</a></li>
                                    <li><a href="//id.brauniart.shop" target="_blank">BrauniArt ID</a></li>
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
                            <p><a href="/">БРАУНИАРТ МАГАЗИН</a> VER {{$verName}} | Все права защищены {{date('Y')}} | Разработано <a
                                    href="//albax-s.ru">ALBAX Studio</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer-area-end -->

    <div class="app-footer app-player grey bg">
        <div class="playlist" style="width:100%"></div>
    </div>

    <!-- JS here -->
    <script src="/assets/js/vendor/jquery-3.5.0.min.js?{$ver}"></script>
    <script src="/assets/js/vendor/jquery.textfill.js?{$ver}"></script>
    <script src="/assets/js/popper.min.js?{$ver}"></script>
    <script src="/assets/js/bootstrap.min.js? {$ver}"></script>
    <script src="/assets/js/isotope.pkgd.min.js?{$ver}"></script>
    <script src="/assets/js/imagesloaded.pkgd.min.js?{$ver}"></script>
    <script src="/assets/js/jquery.magnific-popup.min.js?{$ver}"></script>
    <script src="/assets/js/owl.carousel.min.js?{$ver}"></script>
    <script src="/assets/js/jquery.odometer.min.js?{$ver}"></script>
    <script src="/assets/js/jquery.countdown.min.js?{$ver}"></script>
    <script src="/assets/js/jquery.appear.js?{$ver}"></script>
    <script src="/assets/js/slick.min.js?{$ver}"></script>
    <script src="/assets/js/ajax-form.js?{$ver}"></script>
    <script src="/assets/js/wow.min.js?{$ver}"></script>
    <script src="/assets/js/aos.js?{$ver}"></script>
    <script src="/assets/js/plugins.js?{$ver}"></script>
    <script src="/assets/js/vendor/jquery.maskedinput.min.js?{$ver}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@cdek-it/widget@3" charset="utf-8"></script>
    <script src="https://bootstraptema.ru/snippets/audio/2017/jplayer/jquery.jplayer.min.js"></script>
    <script src="https://bootstraptema.ru/snippets/audio/2017/jplayer/jplayer.playlist.min.js"></script>
    <script src="/assets/js/vendor/table.check-vox.plugin.js"></script>
    <script src="/assets/js/vendor/jquery.dataTables.min.js"></script>
    <script src="/assets/mediaelement/build/mediaelement-and-player.min.js"></script>
    <script src="/assets/mediaelement/build/mep.js"></script>
    <script src="/assets/js/main.js?{$ver}"></script>
    <script src="{{ mix('/resources/js/cart-handler.js') }}"></script>
    <script>
        feather.replace();
    </script>
    @if($scripts)
        @foreach($scripts as $script)
            <script src="{{$script}}"></script>
        @endforeach
    @endif
    @stack('scripts')
    </body>
    </html>

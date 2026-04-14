<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="theme_ocean" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="seller_id" content="{{$currentUser->getSellerId()}}">
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title>{{$title}}</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/dashboard/images/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/dashboard/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/dashboard/images/icons/favicon-16x16.png">
    <link rel="manifest" href="/assets/dashboard/images/icons/site.webmanifest">
    <!--! END: Favicon-->
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@22.6.0/dist/css/suggestions.min.css" rel="stylesheet" />
    @vite('resources/css/dashboard/app.css')
</head>

<body>
<!--! ================================================================ !-->
<!--! [Start] Navigation Manu !-->
<!--! ================================================================ !-->
<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="/assets/dashboard/images/logo-full.png" style="width: 180px;" alt=""
                     class="logo logo-lg" />
                <img src="/assets/dashboard/images/logo-abbr.png" alt="" class="logo logo-sm" />
            </a>
        </div>
        @include('dashboard.elements.menu')
    </div>
</nav>
<!--! ================================================================ !-->
<!--! [End]  Navigation Manu !-->
<!--! ================================================================ !-->
<!--! ================================================================ !-->
<!--! [Start] Header !-->
<!--! ================================================================ !-->
@include('dashboard.elements.header')
<!--! ================================================================ !-->
<!--! [End] Header !-->
<!--! ================================================================ !-->
<!--! ================================================================ !-->
<!--! [Start] Main Content !-->
<!--! ================================================================ !-->
@if(!isset($mainView))
<main class="nxl-container">
{{--Content--}}
    <div class="main_body">

        @include('dashboard.page')
        <!-- [ Footer ] start -->
        <footer class="footer">
            <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                <span>
                    Копирайт © BAID | Единый личный кабинет сервисов BrauniArt | Работает на ALBAX ENGINE разработано
                    <a href="https://albax-s.ru">ALBAX STUDIO</a>
                </span>
                <script>document.write(new Date().getFullYear());</script>
            </p>
            <div class="d-flex align-items-center gap-4">
                <a href="https://t.me/BrauniArtSellers" target="_blank" class="fs-11 fw-semibold text-uppercase">Поддержка</a>
            </div>
        </footer>
        <!-- [ Footer ] end -->
    </div>
</main>
@else
    @include($mainView)
@endif
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    @if(isset($Errors))
    <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <svg aria-hidden="true" class="bd-placeholder-img rounded me-2" height="20" preserveAspectRatio="xMidYMid slice" width="20" xmlns="http://www.w3.org/2000/svg">
                <rect width="100%" height="100%" fill="#007aff"></rect>
            </svg>
            <img src="/assets/dashboard/images/logo-abbr.png" class="bd-placeholder-img rounded me-2" height="20" alt="">
            <strong class="me-auto">
                <ya-tr-span data-index="181-0" data-translated="true" data-source-lang="en" data-target-lang="ru" data-value="Bootstrap" data-translation="Bootstrap" data-ch="0" data-type="trSpan" style="visibility: inherit !important;">
                    BAID
                </ya-tr-span>
            </strong>
            <small class="text-body-secondary">
                <ya-tr-span data-index="181-0" data-translated="true" data-source-lang="en" data-target-lang="ru" data-value="2 seconds ago" data-translation="2 секунды назад" data-ch="0" data-type="trSpan" style="visibility: inherit !important;">
                    Только что
                </ya-tr-span>
            </small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
        </div>
        <div class="toast-body">
            <ya-tr-span data-index="182-0" data-translated="true" data-source-lang="en" data-target-lang="ru" data-value=" Heads up, toasts will stack automatically " data-translation=" Внимание, тосты будут складываться стопкой автоматически " data-ch="0" data-type="trSpan" style="visibility: inherit !important;">
                {$error}
            </ya-tr-span>
        </div>
    </div>
    @endif
</div>
<!--! ================================================================ !-->
<!--! [End] Main Content !-->
<!--! ================================================================ !-->
<!--! ================================================================ !-->
<!--! [Start] Add New task Modal !-->
<!--! ================================================================ !-->
@if(isset($modalContent))
@include('dashboard.elements.modal.'.$modalContent)
@endif
<!--! ================================================================ !-->
<!--! [End] Add New task Modal !-->
<!--! ================================================================ !-->
<!--! ================================================================ !-->
<!--! Footer Script !-->
<!--! ================================================================ !-->
<!--! BEGIN: Vendors JS !-->
<script async src="https://ndd-widget.landpro.site/widget.js"></script>
@vite('resources/js/dashboard/app.js')
<script type="module">
    $('.mask-phone').mask('+7 (999) 999-99-99');
</script>
@stack('scripts')
<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        window.pusherAppKey = '{{ env('VITE_PUSHER_APP_KEY') }}';
        window.pusherCluster = '{{ env('VITE_PUSHER_APP_CLUSTER') }}';

        if (typeof Pusher === 'undefined') {
            console.error('❌ Pusher не загружен');
            return;
        }

        window.currentUserId = {{auth()->id()}};
        if (!window.currentUserId) {
            console.warn('⚠️ ID пользователя не найден — уведомления не будут работать');
            return;
        }

        window.notificationsApp = new NotificationsApp(window.currentUserId);
    });

    // Глобальная функция для подписки на новый чат (если нужно вызвать извне)
    window.subscribeToNewChat = function(chatId) {
        if (window.notificationsApp) {
            window.notificationsApp.subscribeToNewChat(chatId);
        }
    };
</script>
</body>
</html>

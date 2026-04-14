<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="theme_ocean">
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title>BrauniArt ID | Авторизация</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="/assets/dashboard/images/favicon.ico">
    <!--! END: Favicon-->
    @vite('resources/css/dashboard/app.css')
</head>

<body>
<!--! ================================================================ !-->
<!--! [Start] Main Content !-->
<!--! ================================================================ !-->
<main class="auth-cover-wrapper">
    <div class="auth-cover-content-inner">
        <div class="auth-cover-content-wrapper">
            <div class="auth-img">
                <img src="/assets/dashboard/images/auth/auth-cover-login-bg.svg" alt="" class="img-fluid">
            </div>
        </div>
    </div>
    <div class="auth-cover-sidebar-inner">
        <div class="auth-cover-card-wrapper">
            <div class="auth-cover-card p-sm-5">
                <div class="wd-50 mb-5">
                    <img src="/assets/dashboard/images/logo-abbr.png" alt="" class="img-fluid">
                </div>
                <h2 class="fs-20 fw-bolder mb-4">Авторизация</h2>
                <h4 class="fs-13 fw-bold mb-2">Войдите в свою учетную запись</h4>
{{--                <p class="fs-12 fw-medium text-muted">Благодарим вас за выбор <strong>ALBAX ENGINE</strong>.</p>--}}
                <form id="login" onsubmit="return false;" class="w-100 mt-4 pt-2">
                    @csrf
                    <div id="alert">
                        @if(session('error'))
                            <div class="alert alert-danger">{{session('error')}}</div>
                        @endif
                    </div>
                    <div class="mb-4">
                        <input type="text" class="form-control mask_phone" name="phone" placeholder="Номер телефона"
                               value="" required>
                    </div>
                    <div id="kode" class="mb-3 hidden">
                        <input type="text" name="code" class="form-control code" placeholder="Код из смс" value="" required>
                        <span id="get_sms" class="fs-11 text-primary"></span>
                    </div>
{{--                    <div class="d-flex align-items-center justify-content-between">--}}
{{--                            --}}
{{--                    </div>--}}
                    <div class="mt-5">
                        <button type="submit" class="btn btn-lg btn-primary w-100">Войти</button>
                    </div>
                </form>
                <div class="w-100 mt-5 text-center mx-auto">
                    <div class="mb-4 border-bottom position-relative"><span
                            class="small py-1 px-3 text-uppercase text-muted bg-white position-absolute translate-middle">или</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <a href="/auth/yandex" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip"
                           data-bs-trigger="hover" title="Войти через Яндекс">
                            <i class="fa-brands fa-yandex"></i>
                        </a>
                    </div>
                </div>
                <div class="mt-5 text-muted">
                    <span> Нет учетной записи?</span>
                    <a href="/signup" class="fw-bold">Зарегистрируйтесь</a>
                </div>
            </div>
        </div>
    </div>
</main>
<!--! ================================================================ !-->
<!--! [End] Main Content !-->
<!--! ================================================================ !-->

<!--! ================================================================ !-->
<!--! Footer Script !-->
<!--! ================================================================ !-->
<script src="https://use.fontawesome.com/b6bb56a290.js"></script>
@vite('resources/js/dashboard/Pages/login.js')
</body>
</html>

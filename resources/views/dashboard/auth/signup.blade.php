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
    <title>BrauniArt ID | Регистрация</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/dashboard/images/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/dashboard/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/dashboard/images/icons/favicon-16x16.png">
    <link rel="manifest" href="/assets/dashboard/images/icons/site.webmanifest">
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
                <img src="/assets/dashboard/images/auth/auth-cover-register-bg.svg" alt="" class="img-fluid">
            </div>
        </div>
    </div>
    <div class="auth-cover-sidebar-inner">
        <div class="auth-cover-card-wrapper">
            <div class="auth-cover-card p-sm-5">
                <div class="wd-50 mb-5">
                    <img src="/assets/dashboard/images/logo-abbr.png" alt="" class="img-fluid">
                </div>
                <h2 class="fs-20 fw-bolder mb-4">Регистрация</h2>
                <h4 class="fs-13 fw-bold mb-2">Один аккаунт для всех систем</h4>
{{--                <p class="fs-12 fw-medium text-muted">Благодарим вас за выбор <strong>ALBAX ENGINE</strong>.</p>--}}
                <form class="w-100 mt-4 pt-2" id="signup">
                    @csrf
                    <div id="alert"></div>
                    <div class="mb-4">
                        <input type="text" name="name" class="form-control" placeholder="Имя">
                    </div>
                    <div class="mb-4">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                    </div>
                    <div class="mb-4">
                        <input type="tel" name="phone" class="form-control mask-phone phone" placeholder="Телефон">
                    </div>
                    <div id="kode" class="mb-3 hidden">
                        <input type="text" name="code" class="form-control code" placeholder="Код из смс" value="">
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div></div>
                        <div>
                            <span id="get_sms" class="fs-11 text-primary"></span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" name="sand_ads" id="receiveMial">
                            <label class="custom-control-label c-pointer text-muted" for="receiveMial"
                                   style="font-weight: 400 !important">Соглашаюсь на получение информационных
                                сообщений</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="termsCondition" id="termsCondition">
                            <label class="custom-control-label c-pointer text-muted" for="termsCondition"
                                   style="font-weight: 400 !important">Соглашаюсь с <a href="https://brauniart.shop/assets/docs/confidential.pdf" target="_blank">условиями</a>
                                сервиса.</label>
                        </div>
                    </div>
                    <div class="mt-5">
                        <button type="submit" class="btn btn-lg btn-primary w-100">Зарегистрироваться</button>
                    </div>
                </form>
                <div class="mt-5 text-muted">
                    <span>Уже есть аккаунт?</span>
                    <a href="/" class="fw-bold">Войти</a>
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
@vite('resources/js/dashboard/Pages/signup.js')
</body>
</html>

<!-- main-area -->
<main>

    <!-- my-account-area -->
    <section class="my-account-area pattern-bg pt-100 pb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="login-page-title">
                        <h2 class="title"><a href="{{route('auth.index')}}">Авторизация</a> / <span>Регистрация</span></h2>
                    </div>
                    <div class="my-account-bg">
                        <div class="my-account-content">
                            {{--                            <div class="direct-login">--}}
                            {{--                                <a href="#"><i class="fab fa-facebook-f"></i>Login with facebook</a>--}}
                            {{--                                <a href="#" class="xing"><i class="fab fa-xing"></i>Login with xing</a>--}}
                            {{--                            </div>--}}
                            {{--                            <span class="or">- OR -</span>--}}
                            <div id="alert"></div>
                            <form id="signup" class="login-form" onsubmit="return false;">
                                @csrf
                                <div class="form-grp">
                                    <label for="name">Имя <span>*</span></label>
                                    <input name="name" type="text" id="name">
                                </div>
                                <div class="form-grp">
                                    <label for="email">Адрес электронной почты <span>*</span></label>
                                    <input name="email" type="email" id="email">
                                </div>
                                <div class="form-grp">
                                    <label for="phone">Номер телефона <span>*</span></label>
                                    <input name="phone" class="mask_phone" type="text" id="phone">
                                </div>
                                <div class="form-grp hidden">
                                    <label for="code">Код подтверждения <span>*</span></label>
                                    <input name="code" class="code" type="text" id="code">
                                    <span id="get_sms" class="fs-11 text-primary"></span>
                                </div>
                                <div class="form-grp-btn">
                                    <input type="submit" class="btn" value="Зарегистрироваться">
                                    <a href="{{route('auth.index')}}" class="btn">Войти</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- my-account-area-end -->


</main>

@push('scripts')
    @vite('resources/js/Pages/signup.js')
@endpush

<!-- main-area -->
<main>

    <!-- my-account-area -->
    <section class="my-account-area pattern-bg pt-100 pb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="login-page-title">
                        <h2 class="title"><span>Авторизация</span> / Регистрация</h2>
                    </div>
                    <div class="my-account-bg">
                        <div class="my-account-content">
                            <p>Пожалуйста войдите в вашу <span>Учетную запись</span></p>
                            {{--                            <div class="direct-login">--}}
                            {{--                                <a href="#"><i class="fab fa-facebook-f"></i>Login with facebook</a>--}}
                            {{--                                <a href="#" class="xing"><i class="fab fa-xing"></i>Login with xing</a>--}}
                            {{--                            </div>--}}
                            {{--                            <span class="or">- OR -</span>--}}
                            <div id="alert"></div>
                            <form id="login" class="login-form" onsubmit="return false;">
                                @csrf
                                <div class="form-grp">
                                    <label for="phone">Номер телефона <span>*</span></label>
                                    <input name="phone" class="mask_phone" type="text" id="phone">
                                </div>
                                <div class="form-grp hidden">
                                    <label for="code">Код подтверждения <span>*</span></label>
                                    <input name="code" class="code" type="text" id="code">
                                </div>
                                <div class="form-grp-btn">
                                    <input type="hidden" name="password" value="Password">
                                    <input type="submit" class="btn" value="Войти">
                                    <a href="/signup" class="btn">Зарегистрироваться</a>
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
<!-- main-area-end -->

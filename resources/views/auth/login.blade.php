<!-- main-area -->
<main>

    <!-- my-account-area -->
    <section class="my-account-area pattern-bg pt-100 pb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="login-page-title">
                        <h2 class="title"><span>Авторизация</span> / <a href="{{route('signup.index')}}">Регистрация</a></h2>
                    </div>
                    <div class="my-account-bg">
                        <div class="my-account-content">
                            <p>Пожалуйста войдите в вашу <span>Учетную запись</span></p>
                              <div class="direct-login">
                                  <a href="{{route('auth.login.yandex', ['store'=>true])}}"><i class="fab fa-yandex"></i>Войти с Яндекс</a>
{{--                                  <a href="#" class="xing"><i class="fab fa-vk"></i>Войти с VK</a>--}}
                              </div>
                              <span class="or">- ИЛИ -</span>
                            <div id="alert">

                            </div>
                            <form id="login" class="login-form" onsubmit="return false;">
                                @csrf
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
                                    <input type="hidden" name="password" value="Password">
                                    <input type="submit" class="btn" value="Войти">
                                    <a href="{{route('signup.index')}}" class="btn">Зарегистрироваться</a>
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
@push('scripts')
    @vite('resources/js/Pages/login.js')
@endpush

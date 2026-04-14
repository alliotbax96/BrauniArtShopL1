<!-- main-area -->
<main>

    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Контакты</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Контакты</h1>
    </div>
    <!-- breadcrumb-area-end -->


    <!-- contact-area -->
    <section class="contact-area primary-bg pt-100 pb-70">
        <div class="custom-container-two">
            <div class="contact-wrap-padding">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 col-sm-8">
                        <div class="contact-info-box text-center mb-30">
                            <div class="contact-box-icon">
                                <i class="flaticon-project"></i>
                            </div>
                            <div class="contact-info-content">
                                <h5>ООО "БРАУНИ АРТ МАГАЗИН"</h5>
                                <p>ИНН: 7751222627/690001001</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-8">
                        <div class="contact-info-box text-center mb-30">
                            <div class="contact-box-icon">
                                <i class="flaticon-mail"></i>
                            </div>
                            <div class="contact-info-content">
                                <h5>E-mail</h5>
                                <p>Поддержка: support@brauniart.shop</p>
                                <p>info@brauniart.shop</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-8">
                        <div class="contact-info-box text-center mb-30">
                            <div class="contact-box-icon">
                                <i class="flaticon-placeholder"></i>
                            </div>
                            <div class="contact-info-content">
                                <h5>Удобный пункт выдачи</h5>
                                <p>Получайте заказы у наших партнеров</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact-area-end -->

    <!-- contact-area -->
    <section class="contact-area pt-100 pb-100">
        <div class="custom-container-two">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-lg-7 col-md-9">
                    <div class="contact-title text-center mb-60">
                        <div class="section-title text-center">
                            <span class="sub-title">Связаться с нами</span>
                            <h2 class="title">Отправьте сообщение</h2>
                        </div>
                        <p>Мы всегда рады пообщаться с вами. Обязательно пишите нам, если у вас возникнут какие-либо
                            вопросы или вам понадобится помощь и поддержка.</p>
                    </div>
                </div>
            </div>
            <div class="contact-wrap-padding">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="contact-form">
                            <form action="#">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-grp">
                                            <input type="text" placeholder="Ваше имя*" disabled>
                                            <i class="far fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-grp">
                                            <input type="text" placeholder="Фамилия*" disabled>
                                            <i class="far fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-grp">
                                            <input type="email" required placeholder="E-mail*" disabled>
                                            <i class="far fa-envelope"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-grp">
                                            <input type="text" placeholder="Телефон*" disabled>
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                    </div>
                                </div>
                                <textarea name="message" id="message" placeholder="Сообщение" disabled></textarea>
                                <button type="submit" class="btn" disabled>отправить</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                     {{--510 × 440--}}
                        @foreach($wareHouses as $wareHouse)
                        <input class="point" type="text" value="{{$wareHouse->id}}" data-name="{{$wareHouse->name}}" data-description="">
                        @endforeach
                        <div id="map" class="contact-map" style="width: 600px; height: 440px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact-area-end -->
</main>
<!-- main-area-end -->
@push('scripts')
    @vite('resources/js/Pages/contacts.js')
@endpush

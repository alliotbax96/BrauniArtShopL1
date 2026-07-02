<!-- [ Main Content ] start -->
<div class="main-content">
    <div class="row">
        <!-- [Invoices Awaiting Payment] start -->
        <div class="col-xxl-12 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between ">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="avatar-text avatar-lg bg-gray-200">
                                <i class="feather-code"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark"><span class="counter">Версия системы {{$verName}}</span></div>
                                <h3 class="fs-13 fw-semibold text-truncate-1-line">Что нового:</h3>
                                <ul>
                                    <li>
                                        <ul>
                                            <li>Исправление ошибок в работе сервисов!</li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [Invoices Awaiting Payment] end -->
        {{--        <hr class="border-top-dashed mt-4 mb-5 mx-3">--}}
        <!-- [Mini Card] start -->
        <div class="col-xxl-3">
            <div class="card no-border">
                <button class="btn btn-primary" onclick='window.location.href="https://brauniart.shop";'>
                    <i class="feather-shopping-bag"></i>
                    Магазин
                </button>
            </div>
        </div>
        <div class="col-xxl-3">
            <div class="card no-border">
                <button class="btn btn-primary" onclick='window.location.href="{{route('profile.delivery.index')}}";'>
                    <i class="feather-shopping-bag"></i>
                    Доставка
                </button>
            </div>
        </div>
        <div class="col-xxl-3">
            <div class="card no-border">
                <button class="btn btn-primary" onclick="window.location.href='{{route('profile.pay.index')}}'">
                    <i class="feather-dollar-sign"></i>
                    Оплата
                </button>
            </div>
        </div>
        <div class="col-xxl-3">
            <div class="card no-border">
                <button class="btn btn-primary" onclick="window.location.href='{{route('profile.index')}}';">
                    <i class="feather-user"></i>
                    Учетная запись
                </button>
            </div>
        </div>
        <!-- [Mini Card] end -->
        {{--        <hr class="border-top-dashed mt-4 mb-5 mx-3">--}}
    </div>
</div>
<!-- [ Main Content ] end -->

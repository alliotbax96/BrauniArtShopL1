<!-- [ Main Content ] start -->
<div class="main-content">
    <div class="row g-4">
        <!-- Блок с версией системы и списком изменений -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="margin-bottom: 0 !important;">
                <div class="card-body p-4 p-xl-5">
                    <div class="d-flex flex-column flex-md-row gap-4 align-items-md-start">
                        <!-- Иконка версии -->
                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="feather-code fs-3 text-white"></i>
                        </div>
                        <!-- Информация -->
                        <div class="flex-grow-1">
                            <h2 class="fs-4 fw-bold mb-3">Версия системы {{ $verName }}</h2>

                            <div class="mb-4">
                                <h3 class="fs-6 fw-semibold mb-2 text-success">
                                    <i class="feather-plus-circle me-1"></i> Что нового
                                </h3>
                                <ul class="list-unstyled ps-3 mb-0">
                                    <li class="mb-1 d-flex align-items-start">
                                        <i class="feather-check-circle text-success me-2 mt-1 flex-shrink-0"></i>
                                        Полностью открыт функционал самиздата для электронных и аудиокниг
                                    </li>
                                    <li class="mb-1 d-flex align-items-start">
                                        <i class="feather-check-circle text-success me-2 mt-1 flex-shrink-0"></i>
                                        Обновлён сервис поддержки. Доступен чат для покупателей и продавцов.
                                    </li>
                                    <li class="mb-1 d-flex align-items-start">
                                        <i class="feather-check-circle text-success me-2 mt-1 flex-shrink-0"></i>
                                        Обновлён внешний вид части разделов в кабинете продавца и на витрине.
                                    </li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <h3 class="fs-6 fw-semibold mb-2 text-warning">
                                    <i class="feather-tool me-1"></i> Что исправлено
                                </h3>
                                <ul class="list-unstyled ps-3 mb-0">
                                    <li class="mb-1 d-flex align-items-start">
                                        <i class="feather-alert-triangle text-warning me-2 mt-1 flex-shrink-0"></i>
                                        Исправлены ошибки клиентской части
                                    </li>
                                    <li class="mb-1 d-flex align-items-start">
                                        <i class="feather-alert-triangle text-warning me-2 mt-1 flex-shrink-0"></i>
                                        Улучшена система авторизации
                                    </li>
                                </ul>
                            </div>

                            <p class="text-muted mb-0">
                                <i class="feather-info me-1"></i>
                                Произведены работы по подготовке к переходу к Realise версии
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Быстрые действия: 4 кнопки в ряд на больших экранах -->
        <div class="col-12 mb-2">
            <div class="row g-3">
                <div class="col-sm-6 col-xxl-3">
                    <a href="https://brauniart.shop" class="card border-0 shadow-sm rounded-4 text-decoration-none action-card h-100">
                        <div class="card-body d-flex flex-column align-items-center text-center p-4">
                            <div class="icon-circle bg-primary bg-opacity-10 mb-3">
                                <i class="feather-shopping-bag text-white fs-4"></i>
                            </div>
                            <h5 class="fw-semibold mb-1 text-dark">Магазин</h5>
                            <p class="text-muted small mb-0">Перейти к покупкам</p>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-xxl-3">
                    <a href="{{ route('profile.delivery.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none action-card h-100">
                        <div class="card-body d-flex flex-column align-items-center text-center p-4">
                            <div class="icon-circle bg-primary bg-opacity-10 mb-3">
                                <i class="feather-truck text-white fs-4"></i>
                            </div>
                            <h5 class="fw-semibold mb-1 text-dark">Доставка</h5>
                            <p class="text-muted small mb-0">Управление доставкой</p>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-xxl-3">
                    <a href="{{ route('profile.pay.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none action-card h-100">
                        <div class="card-body d-flex flex-column align-items-center text-center p-4">
                            <div class="icon-circle bg-primary bg-opacity-10 mb-3">
                                <i class="feather-dollar-sign text-white fs-4"></i>
                            </div>
                            <h5 class="fw-semibold mb-1 text-dark">Оплата</h5>
                            <p class="text-muted small mb-0">Способы оплаты</p>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-xxl-3">
                    <a href="{{ route('profile.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none action-card h-100">
                        <div class="card-body d-flex flex-column align-items-center text-center p-4">
                            <div class="icon-circle bg-primary bg-opacity-10 mb-3">
                                <i class="feather-user text-white fs-4"></i>
                            </div>
                            <h5 class="fw-semibold mb-1 text-dark">Учётная запись</h5>
                            <p class="text-muted small mb-0">Профиль и настройки</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@push('scripts')
    @vite('resources/js/dashboard/scripts/home.js')
@endpush

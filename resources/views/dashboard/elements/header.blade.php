<header class="nxl-header">
    <div class="header-wrapper">
        <!--! [Start] Header Left !-->
        <div class="header-left d-flex align-items-center gap-4">
            <!--! [Start] nxl-head-mobile-toggler !-->
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <!--! [Start] nxl-head-mobile-toggler !-->

            <!--! [Start] nxl-navigation-toggle !-->
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            <!--! [End] nxl-navigation-toggle !-->

            <!--! [Start] nxl-lavel-mega-menu-toggle !-->
            <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open">
                    <i class="feather-align-left"></i>
                </a>
            </div>
            <!--! [End] nxl-lavel-mega-menu-toggle !-->

            <!--! [Start] nxl-lavel-mega-menu !-->
            <div class="nxl-drp-link nxl-lavel-mega-menu">
                <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                    <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Назад</span>
                    </a>
                </div>
                <!--! [Start] nxl-lavel-mega-menu-wrapper !-->
                <div class="nxl-lavel-mega-menu-wrapper d-flex gap-3">
                    <!--! [Start] nxl-lavel-menu !-->
                    <div class="dropdown nxl-h-item nxl-lavel-menu">
                        <a href="javascript:void(0);" class="avatar-text avatar-md bg-primary text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="feather-plus"></i>
                        </a>
                        <div class="dropdown-menu nxl-h-dropdown">
                        @if($currentUser->getSellerId())
                            @if($currentUser->groupInfo()->hasPermission('create_products'))
                            <div class="dropdown nxl-level-menu">
                                <a href="/seller/products/create" class="dropdown-item">
                                <span class="hstack">
                                    <i class="feather-package"></i>
                                    <span>Товар</span>
                                </span>
                                </a>
                            </div>
                            @endif
                            @if($currentUser->groupInfo()->hasPermission('create_users'))
                            <div class="dropdown nxl-level-menu">
                                <a href="/seller/users/create" class="dropdown-item">
                                <span class="hstack">
                                    <i class="feather-users"></i>
                                    <span>Сотрудник</span>
                                </span>
                                </a>
                            </div>
                            @endif
                        @endif
                        <div class="dropdown nxl-level-menu">
                            <a href="/profile/pay" class="dropdown-item">
                                <span class="hstack">
                                    <i class="feather-dollar-sign"></i>
                                    <span>Реквизиты</span>
                                </span>
                            </a>
                        </div>
                        </div>
                    </div>
                    <!--! [End] nxl-lavel-menu !-->

                    <!--! [Start] nxl-h-item nxl-mega-menu !-->
                    <div class="dropdown nxl-h-item nxl-mega-menu"></div>
                    <!--! [End] nxl-h-item nxl-mega-menu !-->
                </div>
                <!--! [End] nxl-lavel-mega-menu-wrapper !-->
            </div>
            <!--! [End] nxl-lavel-mega-menu !-->
        </div>
        <!--! [End] Header Left !-->
        <!--! [Start] Header Right !-->
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                <div class="dropdown nxl-h-item nxl-header-search"></div>
{{--                 <div class="dropdown nxl-h-item nxl-header-language d-none d-sm-flex">--}}
{{--                    <a href="javascript:void(0);" class="nxl-head-link me-0 nxl-language-link" data-bs-toggle="dropdown" data-bs-auto-close="outside">--}}
{{--                        <img src="/assets/dashboard/vendors/img/flags/1x1/ru.svg" alt="" class="img-fluid wd-20" />--}}
{{--                    </a>--}}
{{--                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-language-dropdown">--}}
{{--                        <div class="dropdown-divider mt-0"></div>--}}
{{--                        <div class="language-items-wrapper">--}}
{{--                            <div class="select-language px-4 py-2 hstack justify-content-between gap-4">--}}
{{--                                <div class="lh-lg">--}}
{{--                                    <h6 class="mb-0">Выбор языка</h6>--}}
{{--                                    <p class="fs-11 text-muted mb-0">Доступно (count) языков!</p>--}}
{{--                                </div>--}}
{{--                                <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="tooltip" title="Добавить язык">--}}
{{--                                    <i class="feather-plus"></i>--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                            <div class="dropdown-divider"></div>--}}
{{--                            <div class="row px-4 pt-3">--}}

{{--                                <div class="col-sm-4 col-6 language_select active">--}}
{{--                                    <a href="javascript:void(0);" class="d-flex align-items-center gap-2">--}}
{{--                                        <div class="avatar-image avatar-sm"><img src="/assets/dashboard/vendors/img/flags/1x1/ru.svg" alt="" class="img-fluid" /></div>--}}
{{--                                        <span>Русский</span>--}}
{{--                                    </a>--}}
{{--                                </div>--}}

{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>
                <div class="dropdown nxl-h-item"></div>

{{--                <div class="dropdown nxl-h-item">--}}
{{--                    <a class="nxl-head-link me-3" data-bs-toggle="dropdown" href="#" role="button" data-bs-auto-close="outside">--}}
{{--                        <i class="feather-bell"></i>--}}
{{--                        <span class="badge bg-danger nxl-h-badge">3</span>--}}
{{--                    </a>--}}
{{--                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">--}}
{{--                        <div class="d-flex justify-content-between align-items-center notifications-head">--}}
{{--                            <h6 class="fw-bold text-dark mb-0">Уведомления</h6>--}}
{{--                            <a href="javascript:void(0);" class="fs-11 text-success text-end ms-auto" data-bs-toggle="tooltip" title="Отметить все прочитанными">--}}
{{--                                <i class="feather-check"></i>--}}
{{--                                <span>Отметить все прочитанными</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        <div class="text-center notifications-footer">--}}
{{--                            <a href="javascript:void(0);" class="fs-13 fw-semibold text-dark">Все уведомления</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="/assets/dashboard/images/avatar/1.png" alt="user-image" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="/assets/dashboard/images/avatar/1.png" alt="user-image" class="img-fluid user-avtar" />
                                <div>
                                    <h6 class="text-dark mb-0">{{$currentUser->name}}
                                        @if($currentUser->isAdmin())<span class="badge bg-soft-success text-success ms-1">ADMIN</span>@endif
                                    </h6>
                                    <span class="fs-12 fw-medium text-muted">{{$currentUser->email}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="/profile" class="dropdown-item">
                                <i class="feather-user"></i>
                                <span>Учетная запись</span>
                            </a>
                            <a href="/profile/pay" class="dropdown-item">
                                <i class="feather-dollar-sign"></i>
                                <span>Оплата</span>
                            </a>
                            <a href="/profile/delivery" class="dropdown-item">
                                <i class="feather-dollar-sign"></i>
                                <span>Доставка</span>
                            </a>
                            <a href="/logout" class="dropdown-item">
                                <i class="feather-log-out"></i>
                                <span>Выйти</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!--! [End] Header Right !-->
        </div>
    </div>
</header>

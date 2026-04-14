<div class="navbar-content">
    <ul class="nxl-navbar">
        <li class="nxl-item nxl-caption">
            <label>Навигация</label>
        </li>
        <li class="nxl-item">
            <a href="/" class="nxl-link">
                <span class="nxl-micon"><i class="feather-home"></i></span>
                <span class="nxl-mtext">Главная</span>
            </a>
        </li>
        <li class="nxl-item nxl-hasmenu">
            <a href="javascript:void(0);" class="nxl-link">
                <span class="nxl-micon"><i class="feather-user"></i></span>
                <span class="nxl-mtext">Профиль</span>
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item"><a class="nxl-link"  href="/profile">Учетная запись</a></li>
                <li class="nxl-item"><a class="nxl-link"  href="/profile/pay">Оплата</a></li>
                <li class="nxl-item"><a class="nxl-link"  href="/profile/delivery">Доставка</a></li>
            </ul>
        </li>
        <li class="nxl-item nxl-hasmenu">
            <a href="javascript:void(0);" class="nxl-link">
                <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                <span class="nxl-mtext">Партнерская программа</span>
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item"><a class="nxl-link"  href="/partner/settings">Настройки</a></li>
                <li class="nxl-item"><a class="nxl-link"  href="/partner/payments">Выплаты</a></li>
            </ul>
        </li>

        <li class="nxl-item nxl-caption">
            <label>
                Кабинет продавца
            </label>
        </li>
        @if($currentUser->isSeller())
            @if($currentUser->groupInfo()->hasPermission('view_products') || $currentUser->isAdmin())
            <li class="nxl-item nxl-hasmenu">
                <a href="javascript:void(0);" class="nxl-link">
                    <span class="nxl-micon"><i class="feather-package"></i></span>
                    <span class="nxl-mtext">
                        Ассортимент
                        @if($currentUser->isAdmin())<span class="badge bg-soft-success text-success ms-1">ADMIN</span>@endif
                    </span>
                    <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                </a>
                <ul class="nxl-submenu">
                    <li class="nxl-item"><a class="nxl-link"  href="{{route('seller.products.index')}}">Товары</a></li>
{{--                    <li class="nxl-item"><a class="nxl-link"  href="#">Товары экспресс</a></li>--}}
                    <li class="nxl-item"><a class="nxl-link"  href="#">Остатки</a></li>
{{--                    <li class="nxl-item"><a class="nxl-link"  href="#">Цифровые товары</a></li>--}}
{{--                    <li class="nxl-item"><a class="nxl-link"  href="#">Услуги</a></li>--}}
{{--                    <li class="nxl-item"><a class="nxl-link"  href="#">Бронирование</a></li>--}}
{{--                    <li class="nxl-item"><a class="nxl-link"  href="#">Квесты</a></li>--}}
                </ul>
            </li>
            @endif
            @if($currentUser->groupInfo()->hasPermission('view_orders') || $currentUser->isAdmin())
            <li class="nxl-item nxl-hasmenu">
                <a href="javascript:void(0);" class="nxl-link">
                    <span class="nxl-micon"><i class="feather-shopping-bag"></i></span>
                    <span class="nxl-mtext">
                        Заказы
                        @if($currentUser->isAdmin())<span class="badge bg-soft-success text-success ms-1">ADMIN</span>@endif
                    </span>
                    <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                </a>
                <ul class="nxl-submenu">
                    <li class="nxl-item"><a class="nxl-link"  href="/seller/orders">Товары</a></li>
{{--                    <li class="nxl-item"><a class="nxl-link"  href="#">Товары экспресс</a></li>--}}
{{--                    <li class="nxl-item"><a class="nxl-link"  href="#">Цифровые товары</a></li>--}}
{{--                    <li class="nxl-item"><a class="nxl-link"  href="#">Услуги</a></li>--}}
{{--                    <li class="nxl-item"><a class="nxl-link"  href="#">Бронирование</a></li>--}}
{{--                    <li class="nxl-item"><a class="nxl-link"  href="#">Квесты</a></li>--}}
                </ul>
            </li>
            @endif
            @if($currentUser->groupInfo()->hasPermission('view_finance') || $currentUser->isAdmin())
            <li class="nxl-item nxl-hasmenu">
                <a href="javascript:void(0);" class="nxl-link">
                    <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                    <span class="nxl-mtext">Финансы</span>
                    <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                </a>
                <ul class="nxl-submenu">
                    <li class="nxl-item">
                        <a class="nxl-link"  href="/seller/finance/payments">
                            Выплаты
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            @if($currentUser->groupInfo()->hasPermission('view_users') || $currentUser->isAdmin())
            <li class="nxl-item">
                <a href="/seller/users" class="nxl-link">
                    <span class="nxl-micon"><i class="feather-user"></i></span>
                    <span class="nxl-mtext">Сотрудники</span>
                </a>
            </li>
            @endif
            @if($currentUser->groupInfo()->hasPermission('view_settings') || $currentUser->isAdmin())
            <li class="nxl-item nxl-hasmenu">
                <a href="javascript:void(0);" class="nxl-link">
                    <span class="nxl-micon"><i class="feather-settings"></i></span>
                    <span class="nxl-mtext">Настройки</span>
                    <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                </a>
                <ul class="nxl-submenu">
                    <li class="nxl-item"><a class="nxl-link"  href="/seller/settings">Основные</a></li>
                    <li class="nxl-item"><a class="nxl-link"  href="/seller/settings/logistic">Логистика</a></li>
                    <li class="nxl-item"><a class="nxl-link"  href="/seller/settings/contract">Договор</a></li>
                </ul>
            </li>
            @endif
            <li class="nxl-item">
                <a href="{{route('seller.chat.index')}}" class="nxl-link">
                    <span class="nxl-micon"><i class="feather-help-circle"></i></span>
                    <span class="nxl-mtext">Поддержка</span>
                </a>
            </li>
        @else
        <li class="nxl-item">
            <a href="/BecomeASeller" class="nxl-link">
                <span class="nxl-micon"><i class="feather-check-square"></i></span>
                <span class="nxl-mtext">Стать продавцом</span>
            </a>
        </li>
        @endif
{{--        @if($currentUser->isAdmin())--}}
{{--            <li class="nxl-item nxl-caption">--}}
{{--                <label>Администрирование</label>--}}
{{--            </li>--}}
{{--        @endif--}}
    </ul>

</div>

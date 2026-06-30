<div class="main-content">
    <div class="row">
        <div class="col-g-12">
            <div class="card border-top-0">
                <div class="card-header p-0">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab"
                        role="tablist">
                        <li class="nav-item flex-fill border-top " role="presentation">
                            <a href="javascript:void(0);"
                               class="nav-link @if(Route::currentRouteName() == 'seller.settings.index') active @endif"
                               data-bs-toggle="tab" data-bs-target="#profileTab" role="tab">Основные</a>
                        </li>
                        @if($Seller->getSalesStatus() == 'company')
                            <li class="nav-item flex-fill border-top " role="presentation">
                                <a href="javascript:void(0);"
                                   class="nav-link @if(Route::currentRouteName() == 'seller.settings.logistic') active @endif"
                                   data-bs-toggle="tab"
                                   data-bs-target="#billingTab" role="tab">Логистика</a>
                            </li>
                        @endif
                        <li class="nav-item flex-fill border-top " role="presentation">
                            <a href="javascript:void(0);"
                               class="nav-link @if(Route::currentRouteName() == 'seller.settings.contract') active @endif"
                               data-bs-toggle="tab" data-bs-target="#contractTab" role="tab">Договор</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div
                        class="tab-pane fade @if(Route::currentRouteName() == 'seller.settings.index') show active @endif"
                        id="profileTab" role="tabpanel">
                        <div class="card-body personal-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Основные настройки:</span>
                                </h5>
                                <form id="seller_general" onsubmit="return false;">
                                    @csrf
                                    <input form="seller_general" type="submit" class="btn btn-sm btn-light-brand"
                                           value="Сохранить">
                                </form>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div id="alert_seller_general">
                                    @if(session()->has('message'))
                                        <div class="alert alert-success">{{session('message')}}</div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label for="StoreName" class="fw-semibold">Название
                                        кабинета(Магазина):</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" form="seller_general"
                                               name="StoreName" id="StoreName"
                                               placeholder="Название кабинета(Магазина)"
                                               value="{{$Seller->name}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="companyPhone" class="fw-semibold">Номер телефона:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control mask-phone" form="seller_general"
                                               name="companyPhone" id="companyPhone"
                                               placeholder="Номер телефона" value="{{$Seller->phone}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="companyEntityDetails" class="fw-semibold">Реквизиты:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <select form="seller_general" name="companyEntityDetails"
                                                id="companyEntityDetails" class="form-control">
                                            @if(!isset($userLegalEnityDetail))
                                                <option value="0" disabled selected>Реквизиты не найдены, создайте в
                                                    настройках пользователя!
                                                </option>
                                            @else
                                                <option value="0" disabled selected>--Выберете реквизиты--</option>
                                                @foreach($userLegalEnityDetail as $userLegalEnityDetailItem)
                                                    <option value="{{$userLegalEnityDetailItem->id}}"
                                                            @if($Seller->getSalesStatus() == 'company' && $SellerLegalDetail->id == $userLegalEnityDetailItem->id) @php $isSellerLegal = true; @endphp selected @endif>{{$userLegalEnityDetailItem->legal_name}}</option>
                                                @endforeach
                                            @endif
                                            @if($Seller->getSalesStatus() == 'company')
                                                <option value="{{$SellerLegalDetail->id}}"
                                                        selected>{{$SellerLegalDetail->legal_name}}</option>
                                            @endif
                                            @if(isset($selfEmployed))
                                                <option value="selfEmployed"
                                                        @if($Seller->getSalesStatus() == 'self_employed') selected @endif>
                                                    Самозанятый({{$selfEmployed->account_holder_name}})
                                                </option>
                                            @endif
                                            @if($Seller->getSalesStatus() == 'no_sales_rights')
                                                <option value="selfpub" selected disabled>Самиздат(Без коммерческого
                                                    статуса)
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    @if($Seller->getSalesStatus() == 'company')
                        <div
                            class="tab-pane fade @if(Route::currentRouteName() == 'seller.settings.logistic') show active @endif"
                            id="billingTab"
                            role="tabpanel">
                            <div class="card-body pass-info">
                                <div class="mb-4 d-flex align-items-center justify-content-between">
                                    <div id="alert_profile"></div>
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Адрес отгрузки:</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                       Адрес отгрузки — адрес, по которому необходимо доставить товар для дальнейшей отправки партнёром.
                                    </span>
                                    </h5>
                                    <form id="delivery_form" onsubmit="return false;"></form>
                                </div>
                            </div>
                            <hr class="my-0">
                            <div class="card-body pass-security" id="user_address">
                                <div class="collapse" id="point_map_collapse">
                                    <div class="card card-body" id="point_map">
                                        Некоторый заполнитель для компонента сворачивания. Эта панель по умолчанию
                                        скрыта, но открывается, когда пользователь активирует соответствующий триггер.
                                    </div>
                                </div>
                                <div class="row mb-4 align-items-center">
                                    <div id="alert_profile"></div>
                                    <div class="col-lg-4">
                                        <label for="pvzInput" class="fw-semibold">Пункт сдачи заказов до 200
                                            килограмм:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" form="delivery_form" name="pvz"
                                                   id="pvzInput" placeholder="Пункт сдачи заказов до 200 килограмм:"
                                                   value=" @if(isset($Seller->SellerPvz)) {{$Seller->SellerPvz->pvz_name}} @endif"
                                                   disabled="true">
                                            <div class="input-group-text border-start bg-gray-2 c-pointer">
                                                <a href="#" id="AddPvz">
                                                    <i class="feather feather-map"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div
                        class="tab-pane fade @if(Route::currentRouteName() == 'seller.settings.contract') show active @endif"
                        id="contractTab"
                        role="tabpanel">
                        <div class="card-body pass-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Договор:</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                      В данном разделе представлен текст договора, инструкции по его подписанию, а также информация о статусе вашего личного кабинета.
                                    </span>
                                </h5>
                                <form id="delivery_form" onsubmit="return false;"></form>
                            </div>
                        </div>
                        <hr class="my-0">
                        <div class="card-body pass-security" id="user_address">
                            <div class="row mb-4 align-items-center">
                                <div id="alert_contract"></div>
                            </div>
                            <div
                                class="hstack justify-content-between p-4 mb-3 border border-dashed border-gray-3 rounded-1">
                                <div class="hstack me-4">
                                    <div class="avatar-text">
                                        <i class="fa fa-file-text"></i>
                                    </div>
                                    <div class="ms-4">
                                        <span class="fw-bold mb-1 text-truncate-1-line">
                                            @if(!isset($contract))
                                                Договор не найден!
                                            @else
                                                <a href="/seller/settings/contract" target="_blank">
                                                Договор № {{$contract->id}}
                                            </a>
                                            @endif
                                        </span>
                                        <div class="fs-12 text-muted text-truncate-1-line">
                                            @if(isset($contract) && $contract->status && $contract->signed_status)
                                                Договор подписан и активирован! Товары отображаются!
                                            @endif
                                            @if(isset($contract) && !$contract->status && $contract->signed_status)
                                                Договор подписан но не активирован! Товары не отображаются!
                                            @endif
                                            @if(isset($contract) && !$contract->status && !$contract->signed_status)
                                                Договор не подписан и не активирован! Товары не отображаются в каталоге!
                                            @endif
                                            @if(!isset($contract))
                                                Заполните реквизиты компании и вернитесь на эту страницу!
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-check form-switch form-switch-sm">
                                    <label class="form-check-label fw-500 text-dark c-pointer"
                                           for="formSwitch2FA"></label>
                                    <input class="form-check-input c-pointer" type="checkbox" id="formSwitch2FA"
                                           disabled
                                           @if(isset($contract) && $contract->status && $contract->signed_status) checked @endif>
                                </div>
                            </div>
                        </div>
                        @if(isset($contract) && (!$contract->status || !$contract->signed_status))
                            <hr class="my-0">
                            <div class="card-body">
                                <div class="alert alert-info">
                                    @if(isset($contract) && !$contract->signed_status)
                                        Для подписания договора отправьте нам запрос через ЭДО:
                                        <br>
                                        ООО "Брауни Арт" (компания с НДС).
                                        <br>
                                        Оператор ЭДО: НТЦ СТЭК (СТЭК-ТРАСТ)
                                        <br>
                                        ID: 2BA708231.
                                        Для подписания договора отправьте нам запрос через ЭДО:
                                        <br>
                                        ООО "Брауни Арт магазин".
                                        <br>
                                        Оператор ЭДО: НТЦ СТЭК (СТЭК-ТРАСТ)
                                        <br>
                                        ID: 2BA708230.
                                    @endif
                                    @if(isset($contract) && !$contract->status && $contract->signed_status)
                                        Рекомендуем обратиться в <a href="https://t.me/BrauniArtSellers"
                                                                    target="_blank">поддержку</a> для решения проблемы!
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@push('scripts')
    @vite('resources/js/dashboard/Pages/settings.js')
@endpush

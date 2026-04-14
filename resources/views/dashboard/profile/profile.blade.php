<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-top-0">

                <div class="card-header p-0">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab"
                        role="tablist">
                        <li class="nav-item flex-fill border-top" role="presentation">
                            <a href="javascript:void(0);"
                               class="nav-link @if(Route::currentRouteName() == 'profile.index') active @endif "
                               data-bs-toggle="tab" data-bs-target="#profileTab" role="tab">Профиль</a>
                        </li>
                        <li class="nav-item flex-fill border-top" role="presentation">
                            <a href="javascript:void(0);" class="nav-link @if(Route::currentRouteName() == 'profile.delivery.index') active @endif "
                               data-bs-toggle="tab" data-bs-target="#billingTab" role="tab">Доставка</a>
                        </li>
                        <li class="nav-item flex-fill border-top" role="presentation">
                            <a href="javascript:void(0);" class="nav-link @if(Route::currentRouteName() == 'profile.pay.index') active @endif "
                               data-bs-toggle="tab" data-bs-target="#subscriptionTab" role="tab">Оплата</a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade @if(Route::currentRouteName() == 'profile.index') show active @endif"
                         id="profileTab" role="tabpanel">
                        <div class="card-body personal-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Персональная информация:</span>
                                </h5>
                                <form id="user_data" onsubmit="return false;">
                                    @csrf
                                    <input form="user_data" type="submit" class="btn btn-sm btn-light-brand"
                                           value="Сохранить">
                                </form>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div id="alert_profile">
                                    @if(session('error'))
                                        <div class="alert alert-danger">{{session('error')}}</div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label for="fullnameInput" class="fw-semibold">ФИО: </label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-user"></i></div>
                                        <input type="text" class="form-control" form="user_data" name="name"
                                               id="fullnameInput" placeholder="ФИО" value="{{$currentUser->name}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="mailInput" class="fw-semibold">Email: </label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-mail"></i></div>
                                        <input type="text" form="user_data" name="email" class="form-control"
                                               id="mailInput" placeholder="Email" value="{{$currentUser->email}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="phoneInput" class="fw-semibold">Телефон: </label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-phone"></i></div>
                                        <input type="text" class="form-control mask-phone" id="phoneInput"
                                               placeholder="Телефон" value="{{$currentUser->phone}}" disabled>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Поменять телефон пока не получится!
                                            Мы уже работаем над этой функцией и скоро добавим ее!
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-0">
                        <div class="card-body pass-security">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Социальные сети и сервисы:</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Настройте авторизацию через социальную сеть или сервис.
                                    </span>
                                </h5>
                            </div>
                            @if(!isset($SocialAccounts['yandex'][0]))
                            <a style="text-decoration: none;" href="{{route('profile.attach.yandex')}}">
                            @endif
                                <div
                                    class="hstack justify-content-between p-4 mb-3 border border-dashed border-gray-3 rounded-1">
                                    <div class="hstack me-4">
                                        <div class="avatar-text">
                                            <i class="fa-brands fa-yandex"></i>
                                        </div>
                                        <div class="ms-4">
                                            <span class="fw-bold mb-1 text-truncate-1-line">
                                                Яндекс
                                            </span>
                                            <div class="fs-12 text-muted text-truncate-1-line">
                                                Авторизация в один клик с аккаунтом Яндекс.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch form-switch-sm">
                                        <label class="form-check-label fw-500 text-dark c-pointer"
                                               for="formSwitch2FA"></label>
                                        <input class="form-check-input c-pointer @if(isset($SocialAccounts['yandex'][0])) openid_off @endif" data-service="yandex" type="checkbox"
                                               id="formSwitch2FA" @if(isset($SocialAccounts['yandex'][0])) checked @endif @if(!isset($SocialAccounts['yandex'][0])) disabled @endif>
                                    </div>
                                </div>
                            @if(!isset($SocialAccounts['yandex'][0]))
                            </a>
                            @endif

                        </div>
                    </div>
                    <div class="tab-pane fade @if(Route::currentRouteName() == 'profile.delivery.index') show active @endif" id="billingTab"
                         role="tabpanel">
                        <div class="card-body pass-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <div id="alert_profile"></div>
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Адрес доставки:</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                    Адрес доставки - это адрес, по которому должен быть доставлен приобретенный товар или услуга.
                                </span>
                                </h5>
                                <form id="delivery_form" onsubmit="return false;">
                                    {{-- <input type="submit" class="btn btn-sm btn-light-brand" value="Сохранить"> --}}
                                </form>
                            </div>
                            <div class="collapse" id="point_map_collapse">
                                <div class="card card-body" id="point_map">
                                    Некоторый заполнитель для компонента сворачивания. Эта панель по умолчанию
                                    скрыта, но открывается, когда пользователь активирует соответствующий триггер.
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div id="alert_delivery"></div>
                                <div class="col-lg-4">
                                    <label for="pvzInput" class="fw-semibold">Пункт выдачи заказов до 200
                                        килограмм:</label>
                                </div>
                                <div class="col-lg-8 align-items-right">
                                    <a href="javascript:void()" class="btn btn-primary" id="AddPvz">Добавить</a>
                                </div>
                            </div>
                        </div>
                        <hr class="my-0">
                        <div class="card-body pass-security" id="user_address">
                            @foreach($userPvzs as $item)
                                <div
                                    class="hstack justify-content-between p-4 mb-3 border border-dashed border-gray-3 rounded-1 block-div">
                                    <div class="hstack me-4">
                                        <div class="avatar-text">
                                            <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                                        </div>
                                        <div class="ms-4">
                                            <span class="fw-bold mb-1 text-truncate-1-line">Пункт выдачи Яндекс</span>
                                            <div class="fs-12 text-muted text-truncate-1-line">{{$item->pvz_name}}</div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch form-switch-sm">
                                        <label class="form-check-label fw-500 text-dark c-pointer"
                                               for="formSwitch2FA"></label>
                                        <a href="#" class="del_address" data-addressid="{{$item->id}}"
                                           style="text-decoration: none; color: black;">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                            @if(count($userPvzs)<1)
                                    <div class="hstack justify-content-between p-4 mb-3 border border-dashed border-gray-3 rounded-1 block-null">
                                        <div class="hstack me-4">
                                            <div class="avatar-text">
                                                <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                                            </div>
                                            <div class="ms-4">
                                                <span class="fw-bold mb-1 text-truncate-1-line">У вас еще нет пунктов получения заказов</span>
                                                <div class="fs-12 text-muted text-truncate-1-line"></div>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch form-switch-sm">
                                        </div>
                                    </div>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane fade @if(Route::currentRouteName() == 'profile.pay.index') show active @endif" id="subscriptionTab"
                         role="tabpanel">
                        <div class="card-body payment-methord">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0">Привязанные карты:</h5>
                                <a href="{{route('profile.pay.AddCard')}}" class="btn btn-sm btn-light-brand">Добавить новую карту</a>
                            </div>
                            <div class="row">
                                <div id="CardAlert" class="col-lg-12"></div>
                                @foreach($Cards as $Card)
                                @if($Card['Status'] == 'A')
                                @php $HasCards = true; @endphp
                                <div class="col-lg-6 bankCard">
                                    <div class="px-4 py-2 mb-4 d-sm-flex justify-content-between border border-dashed border-gray-3 rounded-1 position-relative">
                                        <div class="d-sm-flex align-items-center">
                                            <div class="ms-0 ms-sm-3">
                                                <img src="#" alt="">
                                                <div class="mb-0 text-truncate-1-line pan">
                                                 {{$Card['Pan']}}
                                                </div>
                                                <small class="fs-10 fw-medium text-uppercase text-truncate-1-line">
                                                    Действует до {{substr_replace($Card['ExpDate'], '/', 2, 0)}}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="hstack gap-3 mt-3 mt-sm-0">
                                            <a href="javascript:void(0);" data-id="{{$Card['CardId']}}" class="text-light del_card">
                                                Отвязать
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                                @if(!isset($HasCards))
                                <div class="hstack p-4 mb-3">
                                    <div class="hstack me-4">
                                        <div class="avatar-text">
                                            <i class="feather-alert-circle"></i>
                                        </div>
                                        <div class="ms-4">
                                            <span class="fw-bold mb-1 text-truncate-1-line">Карты не найдены</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        {{--                <hr class="my-0">--}}
                        <div class="card-body pass-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Реквизиты организации:</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">Укажите реквизиты
                                            вашей организации если планируете покупать как юридическое лицо.</span>
                                </h5>
                                <form id="requisites" onsubmit="return false;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{$currentUser->id}}">
                                    <input type="hidden" name="id">
                                </form>
                                <button form="requisites" type="submit" class="btn btn-sm btn-light-brand">Сохранить
                                </button>
                            </div>
                            <div id="requisitesAlert" class="mb-4 d-flex align-items-center"></div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="company_name" class="fw-semibold">Наименование организации: </label>
                                </div>
                                <div class="col-lg-8">
                                    <input form="requisites" class="form-control" id="company_name" name="legal_name"
                                           placeholder="Наименование организации или ИНН"
                                           value="">
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold" for="ipinput">Индивидуальный предприниматель:</label>
                                </div>
                                <div class="col-lg-8">
                                    <input form="requisites" class="form-check-input" type="checkbox" name="ip"
                                           id="ipinput">
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold" for="NDSInput">НДС:</label>
                                </div>
                                <div class="col-lg-8">
                                    <input form="requisites" class="form-check-input" type="checkbox" name="is_vat_payer"
                                           id="NDSInput" >
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="company_inn" class="fw-semibold">ИНН: </label>
                                </div>
                                <div class="col-lg-8">
                                    <input form="requisites" class="form-control" id="company_inn" name="inn"
                                           placeholder="ИНН" maxlength="10" value="">
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center ul" id="company_kpp_block">
                                <div class="col-lg-4">
                                    <label for="company_kpp" class="fw-semibold">КПП: </label>
                                </div>
                                <div class="col-lg-8">
                                    <input form="requisites" class="form-control" id="company_kpp" name="kpp"
                                           placeholder="КПП" maxlength="9" value="">
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="company_ogrn" class="fw-semibold">ОГРН: </label>
                                </div>
                                <div class="col-lg-8">
                                    <input form="requisites" class="form-control" id="company_ogrn" name="ogrn"
                                           placeholder="ОГРН" maxlength="13" value="">
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center ul">
                                <div class="col-lg-4">
                                    <label for="company_manager" class="fw-semibold">Генеральный директор: </label>
                                </div>
                                <div class="col-lg-8">
                                    <input form="requisites" class="form-control" id="company_manager"
                                           name="director_name"
                                           placeholder="Генеральный директор" value="">
                                </div>
                            </div>
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Банковские реквизиты:</span>
                                </h5>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="bank_account" class="fw-semibold">Расчетный счет: </label>
                                </div>
                                <div class="col-lg-8">
                                    <input class="form-control" id="bank_account" name="account_number"
                                           placeholder="Расчетный счет" maxlength="20" form="requisites">
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="bank_name" class="fw-semibold">Введите БИК или название
                                        банка: </label>
                                </div>
                                <div class="col-lg-8">
                                    <input class="form-control" id="bank_name" name="bank_name"
                                           placeholder="Введите БИК или название банка" form="requisites">
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="bank_bik" class="fw-semibold">БИК: </label>
                                </div>
                                <div class="col-lg-8">
                                    <input class="form-control" id="bank_bik" name="bik" placeholder="БИК"
                                           maxlength="9" form="requisites">
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="bank_cor_account" class="fw-semibold">Корр. счет: </label>
                                </div>
                                <div class="col-lg-8">
                                    <input class="form-control" id="bank_cor_account" name="correspondent_account"
                                           placeholder="Корр. счет" maxlength="20" form="requisites">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pass-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Сохраненные ранее реквизиты организации:</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line"></span>
                                </h5>
                            </div>
                            <div class="card-body pass-security" id="user_legals">
                                @if(count($userLegalEnityDetail)<1)
                                    <div class="hstack justify-content-between p-4 mb-3 border border-dashed border-gray-3 rounded-1 legal-block-null">
                                        <div class="hstack me-4">
                                            <div class="avatar-text">
                                                <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                                            </div>
                                            <div class="ms-4">
                                                <span class="fw-bold mb-1 text-truncate-1-line">У вас еще нет сохраненных реквизитов</span>
                                                <div class="fs-12 text-muted text-truncate-1-line"></div>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch form-switch-sm">
                                        </div>
                                    </div>
                                @endif
                                @foreach($userLegalEnityDetail as $detail)
                                    <div class="hstack justify-content-between p-4 mb-3 border border-dashed border-gray-3 rounded-1 block-div">
                                        <div class="hstack me-4">
                                            <div class="avatar-text">
                                                <i class="fa fa-building" aria-hidden="true"></i>
                                            </div>
                                            <div class="ms-4 legal_data" data-id="{{$detail->id}}">
                                                <span class="fw-bold mb-1 text-truncate-1-line">{{$detail->legal_name}}</span>
                                                <div class="fs-12 text-muted text-truncate-1-line">ИНН: {{$detail->inn}}, ОГРН: {{$detail->ogrn}}, @if(!$detail->isIndividualEntrepreneur()) КПП: {{$detail->kpp}}, Генеральный директор: {{$detail->director_name}}. @endif @if($detail->isVatPayer()) Плательщик НДС @endif</div>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch form-switch-sm">
                                            <label class="form-check-label fw-500 text-dark c-pointer"
                                                   for="formSwitch2FA"></label>
                                            <a href="#" class="edit_legal" data-id="{{$detail->id}}" style="text-decoration: none; color: black;">
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                            </a>

                                            <label class="form-check-label fw-500 text-dark c-pointer"
                                                   for="formSwitch2FA"></label>
                                            <a href="#" class="del_legal" data-id="{{$detail->id}}" style="text-decoration: none; color: black;">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </a>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@push('scripts')
    @vite('resources/js/dashboard/Pages/profile.js')
@endpush

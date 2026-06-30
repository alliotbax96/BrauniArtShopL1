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
                               class="nav-link active"
                               data-bs-toggle="tab" data-bs-target="#profileTab" role="tab">Регистрация продавца</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active"
                         id="profileTab" role="tabpanel">
                        <div class="card-body personal-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Основные настройки:</span>
                                </h5>
                                <form id="BecomeASeller" method="post" action="/BecomeASeller">
                                    @csrf
                                    <input form="BecomeASeller" type="submit" class="btn btn-sm btn-light-brand"
                                           value="Сохранить">
                                </form>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div id="alert_BecomeASeller">
                                    @if(session()->has('message'))
                                        <div class="alert alert-danger">{{session('message')}}</div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label for="StoreName" class="fw-semibold">Название
                                        кабинета(Магазина):</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" form="BecomeASeller"
                                               name="StoreName" id="StoreName"
                                               placeholder="Название кабинета(Магазина)"
                                               @if(session()->has('data')) value="{{session('data.StoreName')}}" @endif >
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="companyPhone" class="fw-semibold">Номер телефона:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control mask-phone" form="BecomeASeller"
                                               name="companyPhone" id="companyPhone"
                                               placeholder="Номер телефона"
                                               @if(session()->has('data')) value="{{session('data.companyPhone')}}" @endif >
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="companyEntityDetails" class="fw-semibold">Реквизиты:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <select form="BecomeASeller" name="companyEntityDetails"
                                                id="companyEntityDetails" class="form-control">
                                            @if(!isset($userLegalEnityDetail))
                                                <option value="0" disabled selected>Реквизиты не найдены, создайте в
                                                    настройках пользователя!
                                                </option>
                                            @else
                                                <option value="0" disabled selected>--Выберете реквизиты--</option>
                                                @foreach($userLegalEnityDetail as $userLegalEnityDetailItem)
                                                    <option value="{{$userLegalEnityDetailItem->id}}"
                                                            @if(session()->has('data') && session('data.companyEntityDetails')) selected @endif >{{$userLegalEnityDetailItem->legal_name}}</option>
                                                @endforeach
                                                <option value="selfpub">Самиздат(Без коммерческого статуса)</option>
                                                @if(isset($selfEmployed))
                                                    <option value="selfEmployed">
                                                        Самозанятый({{$selfEmployed->account_holder_name}})
                                                    </option>
                                                @endif
                                            @endif
                                        </select>
                                    </div>
                                </div>
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
    {{--    @vite('resources/js/dashboard/Pages/settings.js')--}}
@endpush

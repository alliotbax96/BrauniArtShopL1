<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@if(isset($InPageName)) {{$InPageName}} @else {{$PageName}} @endif</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                <a href="{{ url(Str::beforeLast(Request::url(), '/')) }}">{{$PageName}}</a>
                </li>
                @if(isset($InPageName))
                <li class="breadcrumb-item">{{$InPageName}}</li>
                @endif
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Назад</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    @if(isset($SelectSellerList))
                        <select class="form-control" name="sellerId" id="SellerSelect">
                         @foreach($SelectSellerList as $seller)
                                <option value="{{$seller->id}}" @if($seller->id == $_COOKIE['selectedSellerId']) selected @endif>{{$seller->name}}({{$seller->id}})</option>
                         @endforeach
                        </select>
                    @endif
                    @if(isset($CreateObject))
                    <a href="{{$CreateObject}}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Создать</span>
                    </a>
                    @endif
                    @if(isset($AddFuncModal))
                    <a href="javascript:void(0);" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="feather-plus me-2"></i>
                        <span>Создать</span>
                    </a>
                    @endif

                    @if(isset($InvitationFuncModal))
                    <a href="javascript:void(0);" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="feather-plus me-2"></i>
                        <span>Пригласить</span>
                    </a>
                    @endif
                    @if(isset($SaveMode))
                    <a href="javascript:void(0)" id="save" class="btn btn-primary">
                        <span>Сохранить</span>
                    </a>
                    @endif
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->
    @include($View)
</div>
@push('scripts')
    <script type="module">
       $("#SellerSelect").select2({
           theme: 'bootstrap-5'
       })
    </script>
@endpush

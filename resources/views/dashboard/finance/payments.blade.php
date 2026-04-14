<!-- [ Main Content ] start -->
<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover" id="proposalList">
                            <thead>

                            <tr>
                                <th class="wd-30">
                                    <div class="btn-group mb-1">
                                        <div class="custom-control custom-checkbox ms-1">
                                            <input type="checkbox" class="custom-control-input"
                                                   id="checkAllProposal">
                                            <label class="custom-control-label" for="checkAllProposal"></label>
                                        </div>
                                    </div>
                                </th>
                                <th>Номер</th>
                                <th>Дата</th>
                                <th>Заказ</th>
                                <th>Сумма</th>
                                <th>Статус</th>
                            </tr>
                            </thead>
                            <tbody style="overflow: scroll !important;">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@push('scripts')
 @vite('resources/js/dashboard/Pages/payments.js')
@endpush

<!-- [ Main Content ] start -->
<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="proposalList">
                            <thead>
                            <tr>
                                <th class="wd-30 ml-0">
                                    <div class="btn-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                   id="checkAllProposal">
                                            <label class="custom-control-label" for="checkAllProposal"></label>
                                        </div>
                                    </div>
                                </th>
                                <th>Номер</th>
                                <th>Позиции</th>
                                <th>Сумма</th>
                                <th>Дата</th>
                                <th>Статус</th>
                                <th class="text-end">Действия</th>
                            </tr>
                            </thead>
                            <tbody>
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
    @vite('resources/js/dashboard/Pages/orders.js')
@endpush

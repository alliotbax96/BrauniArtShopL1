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
                                            <input type="checkbox" class="custom-control-input" id="checkAllProject">
                                            <label class="custom-control-label" for="checkAllProject"></label>
                                        </div>
                                    </div>
                                </th>
                                <th>Название в системе</th>
                                <th>Номер договора</th>
                                <th>Статус договора</th>
                                <th>Статус</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Данные подгружаются через AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
@vite('resources/js/dashboard/scripts/admin/sellers.js')
@endpush

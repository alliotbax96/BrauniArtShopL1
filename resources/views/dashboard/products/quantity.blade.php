<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover" id="proposalList">
                            <thead>
                            <tr>
                                <th>Наименование</th>
                                <th>Артикул</th>
                                <th>Остаток на складе</th>
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
@push('scripts')
    @vite('resources/js/dashboard/Pages/ProductQuantity.js')
@endpush

<!-- [ Main Content ] start -->
<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="bookOrdersTable" class="table table-hover align-middle">
                            <thead>
                            <tr>
                                <th>Книга</th>
                                <th>Сумма</th>
                                <th>Статус</th>
                                <th>Дата заказа</th>
                                <th>Дата оплаты</th>
                                <th>Покупатель (email)</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@push('scripts')
    @vite('resources/js/dashboard/Pages/bookOrders.js')
@endpush

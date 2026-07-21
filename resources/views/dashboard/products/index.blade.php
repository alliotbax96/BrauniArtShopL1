<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-modern" id="proposalList">
                            <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <div class="custom-checkbox-modern">
                                        <input type="checkbox" id="checkAllProject" class="custom-checkbox-input">
                                        <label for="checkAllProject" class="custom-checkbox-label"></label>
                                    </div>
                                </th>
                                <th>Наименование</th>
                                <th>Артикул</th>
                                <th>Категория</th>
                                <th>Цена</th>
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
@push('scripts')
    @vite('resources/js/dashboard/Pages/products.js')
@endpush

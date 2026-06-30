<div class="main-content">
    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Тип</label>
                            <select class="form-select filter-input" id="typeFilter">
                                <option value="">Все типы</option>
                                <option value="ebook">Электронная книга</option>
                                <option value="audiobook">Аудиокнига</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Автор</label>
                            <input type="text" class="form-control filter-input" id="authorFilter"
                                   placeholder="Введите автора">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Статус</label>
                            <select class="form-select filter-input" id="statusFilter">
                                <option value="">Все статусы</option>
                                <option value="draft">Черновик</option>
                                <option value="moderation">На модерации</option>
                                <option value="approved">Одобрено</option>
                                <option value="rejected">Отклонено</option>
                                <option value="published">Опубликовано</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Цена (мин-макс)</label>
                            <div class="input-group">
                                <input type="number" class="form-control filter-input" id="minPriceFilter"
                                       placeholder="Мин">
                                <input type="number" class="form-control filter-input" id="maxPriceFilter"
                                       placeholder="Макс">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                                   id="checkAllProject">
                                            <label class="custom-control-label" for="checkAllProject"></label>
                                        </div>
                                    </div>
                                </th>
                                <th>Название</th>
                                <th>Тип</th>
                                <th>Автор</th>
                                <th>Статус</th>
                                <th>Цена</th>
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
    @vite('resources/js/dashboard/Pages/books.js')
@endpush


<main class="nxl-container apps-container apps-tasks">
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            <!-- [ Content Sidebar ] start -->
            <div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
                <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
                    <h4 class="fw-bolder mb-0">Бюджет проекта</h4>
                    <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex">
                        <i class="feather-x"></i>
                    </a>
                </div>

                <div class="content-sidebar-header">
                    <a href="javascript:void(0);" class="btn btn-success w-100" data-bs-toggle="modal"
                       data-bs-target="#addIncomeModal">
                        <i class="feather-plus-circle me-2"></i>
                        <span>Пополнить бюджет</span>
                    </a>
                    <a href="javascript:void(0);" class="btn btn-danger w-100" data-bs-toggle="modal"
                       data-bs-target="#addExpenseModal">
                        <i class="feather-minus-circle me-2"></i>
                        <span>Списать средства</span>
                    </a>
                </div>

                <div class="content-sidebar-body">
                    <div class="px-4 my-3">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-3">Состояние бюджета</h6>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Общий бюджет:</small>
                                    <div
                                        class="fs-5 fw-bold total-budget">{{ number_format($budget->total_budget, 2, '.', ' ') }}
                                        ₽
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Текущий баланс:</small>
                                    <div
                                        class="fs-5 fw-bold current-balance {{ $budget->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($budget->current_balance, 2, '.', ' ') }} ₽
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <small class="text-muted d-block">Баланс руководителя:</small>
                                    <div class="fs-5 fw-bold text-primary manager-balance">
                                        {{ number_format($budget->manager_balance, 2, '.', ' ') }} ₽
                                    </div>
                                    @if(auth()->id() === 1)
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary mt-2"
                                           data-bs-toggle="modal" data-bs-target="#updateManagerBalanceModal">
                                            <i class="feather-edit me-1"></i>Изменить
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 my-3">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <div class="mb-0">
                                    <form action="/admin/budget/invoice" method="POST">
                                        @error('fns')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                        @csrf
                                        <div class="col mb-2">
                                            <label for="amount" class="fw-semibold">Сумма платежа: </label>
                                        </div>
                                        <div class="col">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="amount"
                                                       id="amount" placeholder="Сумма платежа">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="feather-edit me-1"></i>Получить счет
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="nav flex-column nxl-content-sidebar-item">
                        <li class="px-4 my-2 fs-10 fw-bold text-uppercase text-muted text-spacing-1">
                            <span>Категории расходов</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active filter-link" href="javascript:void(0);" data-filter="all">
                                <i class="feather-list"></i>
                                <span>Все операции</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filter-link" href="javascript:void(0);" data-filter="income">
                                <span class="wd-7 ht-7 bg-success rounded-circle"></span>
                                <span>Поступления</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filter-link" href="javascript:void(0);" data-filter="development">
                                <span class="wd-7 ht-7 bg-primary rounded-circle"></span>
                                <span>Разработка</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filter-link" href="javascript:void(0);" data-filter="testing">
                                <span class="wd-7 ht-7 bg-success rounded-circle"></span>
                                <span>Тестирование</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filter-link" href="javascript:void(0);" data-filter="layout">
                                <span class="wd-7 ht-7 bg-info rounded-circle"></span>
                                <span>Верстка</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filter-link" href="javascript:void(0);" data-filter="design">
                                <span class="wd-7 ht-7 bg-warning rounded-circle"></span>
                                <span>Дизайн</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filter-link" href="javascript:void(0);" data-filter="manager">
                                <span class="wd-7 ht-7 bg-danger rounded-circle"></span>
                                <span>Руководитель</span>
                            </a>
                        </li>
                    </ul>

                    @if($statistics->count() > 0)
                        <ul class="nav flex-column nxl-content-sidebar-item mt-3">
                            <li class="px-4 my-2 fs-10 fw-bold text-uppercase text-muted text-spacing-1">
                                <span>Статистика расходов</span>
                            </li>
                            @foreach($statistics as $stat)
                                @if($stat->category)
                                    <li class="nav-item">
                                        <div class="px-4 py-2 d-flex justify-content-between align-items-center">
                                          <span class="fs-12">{{
                                              match($stat->category) {
                                                  'development' => 'Разработка',
                                                  'testing' => 'Тестирование',
                                                  'layout' => 'Верстка',
                                                  'design' => 'Дизайн',
                                                  'manager' => 'Руководитель',
                                                  default => $stat->category
                                              }
                                          }}</span>
                                          <span class="fs-12 fw-bold">{{ number_format($stat->total_amount, 2, '.', ' ') }} ₽</span>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>
            <!-- [ Content Sidebar ] end -->

            <!-- [ Main Area ] start -->
            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header sticky-top bg-white">
                    <div class="page-header-left d-flex align-items-center gap-2">
                        <a href="javascript:void(0);" class="app-sidebar-open-trigger me-2">
                            <i class="feather-align-left fs-20"></i>
                        </a>
                        <h5 class="mb-0">История операций</h5>
                    </div>
                    <div class="page-header-right ms-auto">
                        <div class="hstack gap-2">
                            <div class="hstack">
                                <a href="javascript:void(0)" class="search-form-open-toggle">
                                    <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                         title="Поиск">
                                        <i class="feather-search"></i>
                                    </div>
                                </a>
                                <form class="search-form" style="display: none">
                                    <div class="search-form-inner">
                                        <a href="javascript:void(0)" class="search-form-close-toggle">
                                            <div class="avatar-text avatar-md" data-bs-toggle="tooltip"
                                                 data-bs-trigger="hover" title="Закрыть">
                                                <i class="feather-arrow-left"></i>
                                            </div>
                                        </a>
                                        <input type="search" class="py-3 px-0 border-0 w-100" id="transactionsSearch"
                                               placeholder="Поиск по описанию...">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="transactionsTable" style="width:100%">
                                    <thead class="bg-light">
                                    <tr>
                                        <th class="px-4">Дата</th>
                                        <th>Тип</th>
                                        <th>Категория</th>
                                        <th>Сумма</th>
                                        <th>Описание</th>
                                        <th class="d-none d-md-table-cell">Баланс</th>
                                        <th class="d-none d-lg-table-cell">Пользователь</th>
                                        <th class="text-end px-4">Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="footer">
                    <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                        <span>Copyright ©</span>
                        <script>document.write(new Date().getFullYear());</script>
                    </p>
                </footer>
            </div>
            <!-- [ Content Area ] end -->
        </div>
    </div>
</main>

<!-- ================================================================ -->
<!-- [Start] Модальное окно пополнения бюджета -->
<!-- ================================================================ -->
<div class="modal fade" id="addIncomeModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addIncomeForm" action="{{ route('admin.budget.income') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="feather-plus-circle me-2"></i>Пополнение бюджета
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="incomeAmount" class="form-label">Сумма пополнения (₽) *</label>
                        <div class="input-group">
                            <span class="input-group-text">₽</span>
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-lg"
                                   id="incomeAmount" name="amount" placeholder="0.00" required>
                        </div>
                        <small class="text-muted">Минимальная сумма: 0.01 ₽</small>
                    </div>

                    <div class="mb-4">
                        <label for="incomeDescription" class="form-label">Описание *</label>
                        <input type="text" class="form-control" id="incomeDescription"
                               name="description" placeholder="Например: Оплата от клиента, инвестиции" required
                               maxlength="255">
                        <small class="text-muted">Краткое описание поступления</small>
                    </div>

                    <div class="mb-0">
                        <label for="incomeNotes" class="form-label">Примечания</label>
                        <textarea class="form-control" id="incomeNotes" name="notes" rows="3"
                                  placeholder="Дополнительная информация (необязательно)" maxlength="500"></textarea>
                        <small class="text-muted">Максимум 500 символов</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-success">
                        <i class="feather-check me-1"></i>Пополнить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- [End] Модальное окно пополнения бюджета -->

<!-- ================================================================ -->
<!-- [Start] Модальное окно списания средств -->
<!-- ================================================================ -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addExpenseForm" action="{{ route('admin.budget.expense') }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="feather-minus-circle me-2"></i>Списание средств
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Категория расхода *</label>
                        <div class="row g-2">
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="category" id="cat-development"
                                       value="development" required>
                                <label class="btn btn-outline-primary w-100" for="cat-development">
                                    <i class="feather-code d-block mb-1"></i>
                                    Разработка
                                </label>
                            </div>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="category" id="cat-testing" value="testing">
                                <label class="btn btn-outline-success w-100" for="cat-testing">
                                    <i class="feather-check-circle d-block mb-1"></i>
                                    Тестирование
                                </label>
                            </div>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="category" id="cat-layout" value="layout">
                                <label class="btn btn-outline-info w-100" for="cat-layout">
                                    <i class="feather-layout d-block mb-1"></i>
                                    Верстка
                                </label>
                            </div>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="category" id="cat-design" value="design">
                                <label class="btn btn-outline-warning w-100" for="cat-design">
                                    <i class="feather-pen-tool d-block mb-1"></i>
                                    Дизайн
                                </label>
                            </div>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="category" id="cat-manager" value="manager">
                                <label class="btn btn-outline-danger w-100" for="cat-manager">
                                    <i class="feather-user-check d-block mb-1"></i>
                                    Руководитель
                                </label>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="feather-info me-1"></i>
                            При выборе "Руководитель" сумма поступит на баланс руководителя проекта
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="expenseAmount" class="form-label">Сумма списания (₽) *</label>
                        <div class="input-group">
                            <span class="input-group-text">₽</span>
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-lg"
                                   id="expenseAmount" name="amount" placeholder="0.00" required>
                        </div>
                        <small class="text-muted">
                            Доступно: <span class="fw-bold text-success" id="availableBalance">{{ number_format($budget->current_balance, 2, '.', ' ') }} ₽</span>
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="expenseDescription" class="form-label">Описание *</label>
                        <input type="text" class="form-control" id="expenseDescription"
                               name="description" placeholder="Например: Оплата хостинга, зарплата разработчика"
                               required maxlength="255">
                        <small class="text-muted">Краткое описание расхода</small>
                    </div>

                    <div class="mb-0">
                        <label for="expenseNotes" class="form-label">Примечания</label>
                        <textarea class="form-control" id="expenseNotes" name="notes" rows="3"
                                  placeholder="Дополнительная информация (необязательно)" maxlength="500"></textarea>
                        <small class="text-muted">Максимум 500 символов</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="feather-check me-1"></i>Списать
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- [End] Модальное окно списания средств -->

<!-- ================================================================ -->
<!-- [Start] Модальное окно изменения баланса руководителя -->
<!-- ================================================================ -->
@if(auth()->id() === 1)
    <div class="modal fade" id="updateManagerBalanceModal" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="updateManagerBalanceForm" action="{{ route('admin.budget.manager-balance') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="feather-edit me-2"></i>Баланс руководителя
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="feather-info me-2"></i>
                            Текущий баланс: <strong>{{ number_format($budget->manager_balance, 2, '.', ' ') }}
                                ₽</strong>
                        </div>

                        <div class="mb-4">
                            <label for="newManagerBalance" class="form-label">Новый баланс (₽) *</label>
                            <div class="input-group">
                                <span class="input-group-text">₽</span>
                                <input type="number" step="0.01" min="0" class="form-control form-control-lg"
                                       id="newManagerBalance" name="new_balance" value="{{ $budget->manager_balance }}"
                                       required>
                            </div>
                            <small class="text-muted">
                                Разница будет автоматически рассчитана и записана в историю
                            </small>
                        </div>

                        <div class="alert alert-warning mb-0">
                            <i class="feather-alert-triangle me-2"></i>
                            <strong>Внимание!</strong> Это действие создаст запись в истории транзакций.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-1"></i>Сохранить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
<!-- [End] Модальное окно изменения баланса руководителя -->

<!-- ================================================================ -->
<!-- [Start] Модальное окно просмотра транзакции -->
<!-- ================================================================ -->
<div class="modal fade" id="viewTransactionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Детали операции</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transactionDetails">
                <!-- Заполняется через JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<!-- [End] Модальное окно просмотра транзакции -->

@push('scripts')
    @vite(['resources/js/dashboard/scripts/admin/budget.js'])
@endpush

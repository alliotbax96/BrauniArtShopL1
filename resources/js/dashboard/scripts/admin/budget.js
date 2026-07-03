import 'datatables.net';
import 'datatables.net-bs5';
// resources/js/budget.js

$(document).ready(function() {
    // ============================================
    // Переменная для активного фильтра (объявляем ДО DataTable)
    // ============================================
    let activeFilter = 'all';

    // ============================================
    // Инициализация DataTable
    // ============================================
    const table = $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/budget/transactions',
            type: 'GET',
            data: function(d) {
                d.search = $('#transactionsSearch').val();
                d.type = activeFilter;
                d.per_page = d.length;
                d.page = (d.start / d.length) + 1;
            },
            dataSrc: function(response) {
                return response.data;
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', error, thrown);
                showAlert('danger', 'Ошибка загрузки данных. Попробуйте обновить страницу.');
            }
        },
        columns: [
            {
                data: null,
                render: function(data) {
                    return `
                        <div class="fs-13 fw-bold">${data.date}</div>
                        <div class="fs-12 text-muted">${data.time}</div>
                    `;
                },
                orderable: true,
                searchable: false
            },
            {
                data: 'type_label',
                render: function(data, type, row) {
                    const icon = row.type === 'income' ? 'arrow-down-left' : 'arrow-up-right';
                    return `
                        <span class="badge bg-soft-${row.type_badge} text-${row.type_badge}">
                            <i class="feather-${icon} me-1"></i>${data}
                        </span>
                    `;
                },
                orderable: true,
                searchable: false
            },
            {
                data: 'category_name',
                render: function(data, type, row) {
                    if (!data) return '<span class="text-muted">—</span>';
                    return `
                        <span class="badge bg-soft-${row.category_badge} text-${row.category_badge}">
                            <i class="feather-${row.category_icon} me-1"></i>${data}
                        </span>
                    `;
                },
                orderable: true,
                searchable: false
            },
            {
                data: 'amount_formatted',
                render: function(data, type, row) {
                    const sign = row.type === 'income' ? '+' : '-';
                    const colorClass = row.type === 'income' ? 'text-success' : 'text-danger';
                    return `<span class="fw-bold ${colorClass}">${sign}${data} ₽</span>`;
                },
                orderable: true,
                searchable: false
            },
            {
                data: 'description',
                render: function(data, type, row) {
                    let html = `<div class="fs-13 fw-bold text-truncate-1-line" style="max-width: 200px;">${data}</div>`;
                    if (row.notes) {
                        html += `<div class="fs-12 text-muted text-truncate-1-line" style="max-width: 200px;">${row.notes}</div>`;
                    }
                    return html;
                },
                orderable: true
            },
            {
                data: null,
                render: function(data, type, row) {
                    let html = '';
                    if (row.type === 'income') {
                        html += `
                            <div class="fs-12">
                                <span class="text-muted">Было:</span> ${row.balance_before_formatted} ₽
                            </div>
                            <div class="fs-12">
                                <span class="text-muted">Стало:</span>
                                <span class="text-success">${row.balance_after_formatted} ₽</span>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="fs-12">
                                <span class="text-muted">Было:</span> ${row.balance_before_formatted} ₽
                            </div>
                            <div class="fs-12">
                                <span class="text-muted">Стало:</span>
                                <span class="text-danger">${row.balance_after_formatted} ₽</span>
                            </div>
                        `;
                        if (row.manager_balance_after_formatted) {
                            html += `
                                <div class="fs-12 mt-1">
                                    <span class="text-muted">Баланс рук.:</span>
                                    <span class="text-primary">${row.manager_balance_after_formatted} ₽</span>
                                </div>
                            `;
                        }
                    }
                    return html;
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return `
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-image avatar-sm">
                                <img src="${data.user_avatar}" alt="" class="img-fluid rounded-circle">
                            </div>
                            <span class="fs-12">${data.user_name}</span>
                        </div>
                    `;
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    let html = `
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                <i class="feather-more-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="javascript:void(0);" onclick="viewTransactionDetails(${data.id})">
                                    <i class="feather-eye me-2"></i>Просмотр
                                </a>
                    `;

                    if (data.can_delete) {
                        html += `
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteTransaction(${data.id})">
                                    <i class="feather-trash-2 me-2"></i>Удалить
                                </a>
                        `;
                    }

                    html += `
                            </div>
                        </div>
                    `;
                    return html;
                },
                orderable: false,
                searchable: false
            }
        ],
        order: [[0, 'desc']],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Загрузка...</span></div>',
            search: '',
            searchPlaceholder: 'Поиск...',
            lengthMenu: 'Показать _MENU_ записей',
            info: 'Показано с _START_ по _END_ из _TOTAL_ записей',
            infoEmpty: 'Показано 0 из 0 записей',
            infoFiltered: '(отфильтровано из _MAX_ записей)',
            zeroRecords: 'Записи не найдены',
            emptyTable: 'Нет доступных записей',
            paginate: {
                first: '<i class="feather-chevrons-left"></i>',
                last: '<i class="feather-chevrons-right"></i>',
                previous: '<i class="feather-chevron-left"></i>',
                next: '<i class="feather-chevron-right"></i>'
            }
        },
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
        responsive: true,
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // ============================================
    // Фильтрация по категориям
    // ============================================
    $('.filter-link').on('click', function(e) {
        e.preventDefault();

        $('.filter-link').removeClass('active');
        $(this).addClass('active');

        activeFilter = $(this).data('filter');
        table.ajax.reload();
    });

    // ============================================
    // Поиск по таблице с задержкой
    // ============================================
    let searchTimeout;
    $('#transactionsSearch').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            table.search($('#transactionsSearch').val()).draw();
        }, 500);
    });

    // ============================================
    // Форма пополнения бюджета
    // ============================================
    $('#addIncomeForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Сохранение...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    updateBudgetInfo(response.budget);
                    table.ajax.reload();
                    $('#addIncomeModal').modal('hide');
                    showAlert('success', response.message);
                    form[0].reset();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Произошла ошибка при пополнении';
                showAlert('danger', message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="feather-check me-1"></i>Пополнить');
            }
        });
    });

    // ============================================
    // Форма списания средств
    // ============================================
    $('#addExpenseForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Сохранение...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    updateBudgetInfo(response.budget);
                    table.ajax.reload();
                    $('#addExpenseModal').modal('hide');
                    showAlert('success', response.message);
                    form[0].reset();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Произошла ошибка при списании';
                showAlert('danger', message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="feather-check me-1"></i>Списать');
            }
        });
    });

    // ============================================
    // Форма обновления баланса руководителя
    // ============================================
    $('#updateManagerBalanceForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Сохранение...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    updateBudgetInfo(response.budget);
                    table.ajax.reload();
                    $('#updateManagerBalanceModal').modal('hide');
                    showAlert('success', response.message);
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Произошла ошибка';
                showAlert('danger', message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="feather-save me-1"></i>Сохранить');
            }
        });
    });

    // ============================================
    // Поиск - открыть/закрыть
    // ============================================
    $('.search-form-open-toggle').on('click', function() {
        $(this).hide();
        $('.search-form').show();
        $('#transactionsSearch').focus();
    });

    $('.search-form-close-toggle').on('click', function() {
        $('.search-form').hide();
        $('.search-form-open-toggle').show();
        $('#transactionsSearch').val('').trigger('keyup');
    });

    // ============================================
    // Закрытие модальных окон - сброс форм
    // ============================================
    $('#addIncomeModal, #addExpenseModal, #updateManagerBalanceModal').on('hidden.bs.modal', function() {
        const form = $(this).find('form');
        if (form.length) {
            form[0].reset();
            // Сбрасываем radio кнопки
            form.find('input[type="radio"]').prop('checked', false);
        }
    });
});

// ============================================
// Глобальные функции (вне document.ready)
// ============================================

// Обновление информации о бюджете в сайдбаре
function updateBudgetInfo(budget) {
    if (!budget) return;

    $('.total-budget').text(formatCurrency(budget.total_budget) + ' ₽');
    $('.current-balance').text(formatCurrency(budget.current_balance) + ' ₽');
    $('.manager-balance').text(formatCurrency(budget.manager_balance) + ' ₽');
    $('#availableBalance').text(formatCurrency(budget.current_balance) + ' ₽');

    // Обновляем цвет текущего баланса
    const balanceElement = $('.current-balance');
    if (balanceElement.length) {
        if (parseFloat(budget.current_balance) >= 0) {
            balanceElement.removeClass('text-danger').addClass('text-success');
        } else {
            balanceElement.removeClass('text-success').addClass('text-danger');
        }
    }
}

// Форматирование числа в валюту
function formatCurrency(amount) {
    if (amount === null || amount === undefined) return '0.00';

    return new Intl.NumberFormat('ru-RU', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(parseFloat(amount));
}

// Просмотр деталей транзакции
window.viewTransactionDetails = function(id) {
    if (!id) return;

    // Показываем индикатор загрузки
    $('#transactionDetails').html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>');
    $('#viewTransactionModal').modal('show');

    $.ajax({
        url: `/budget/transaction/${id}`,
        method: 'GET',
        success: function(response) {
            if (!response || !response.transaction) {
                $('#transactionDetails').html('<div class="alert alert-danger">Ошибка загрузки данных</div>');
                return;
            }

            const t = response.transaction;

            let html = `
                <div class="mb-3">
                    <strong>Дата и время:</strong> ${t.date || 'Не указано'}
                </div>
                <div class="mb-3">
                    <strong>Тип операции:</strong>
                    <span class="badge bg-${t.type === 'income' ? 'success' : 'danger'}">${t.type_label || 'Не указан'}</span>
                </div>
            `;

            if (t.category_name) {
                html += `
                    <div class="mb-3">
                        <strong>Категория:</strong>
                        <span class="badge bg-${t.category_badge || 'secondary'}">${t.category_name}</span>
                    </div>
                `;
            }

            html += `
                <div class="mb-3">
                    <strong>Сумма:</strong>
                    <span class="${t.type === 'income' ? 'text-success' : 'text-danger'} fw-bold">
                        ${t.type === 'income' ? '+' : '-'}${t.amount_formatted || '0.00'} ₽
                    </span>
                </div>
                <div class="mb-3">
                    <strong>Описание:</strong><br>
                    ${t.description || 'Нет описания'}
                </div>
            `;

            if (t.notes) {
                html += `
                    <div class="mb-3">
                        <strong>Примечания:</strong><br>
                        ${t.notes}
                    </div>
                `;
            }

            html += `
                <hr>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Баланс до операции:</small><br>
                        <strong>${t.balance_before_formatted || '0.00'} ₽</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Баланс после операции:</small><br>
                        <strong>${t.balance_after_formatted || '0.00'} ₽</strong>
                    </div>
                </div>
            `;

            if (t.manager_balance_after_formatted) {
                html += `
                    <div class="row mt-3">
                        <div class="col-6">
                            <small class="text-muted">Баланс руководителя до:</small><br>
                            <strong>${t.manager_balance_before_formatted || '0.00'} ₽</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Баланс руководителя после:</small><br>
                            <strong>${t.manager_balance_after_formatted} ₽</strong>
                        </div>
                    </div>
                `;
            }

            html += `
                <hr>
                <div class="text-muted">
                    <small>Пользователь: ${t.user_name || 'Система'}</small>
                </div>
            `;

            $('#transactionDetails').html(html);
        },
        error: function(xhr, status, error) {
            console.error('Error loading transaction details:', error);
            $('#transactionDetails').html('<div class="alert alert-danger">Ошибка загрузки данных. Попробуйте позже.</div>');
        }
    });
};

// Удаление транзакции
window.deleteTransaction = function(id) {
    if (!id) return;

    if (!confirm('Вы уверены, что хотите удалить эту транзакцию?\nЭто действие нельзя отменить.')) {
        return;
    }

    $.ajax({
        url: `/budget/transaction/${id}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                if (response.budget) {
                    updateBudgetInfo(response.budget);
                }
                $('#transactionsTable').DataTable().ajax.reload();
                showAlert('success', response.message || 'Транзакция успешно удалена');
            }
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Ошибка при удалении транзакции';
            showAlert('danger', message);
        }
    });
};

// Показать уведомление
function showAlert(type, message) {
    if (!type || !message) return;

    const icon = type === 'success' ? 'check-circle' : 'alert-circle';

    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="feather-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    const contentArea = $('.content-area-body');
    if (contentArea.length) {
        contentArea.prepend(alertHtml);

        // Автоматически скрываем через 5 секунд
        setTimeout(function() {
            contentArea.find('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    } else {
        console.warn('Content area not found for alert:', message);
    }
}


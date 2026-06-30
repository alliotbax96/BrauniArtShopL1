import 'datatables.net';
import 'datatables.net-bs5';

$(document).ready(function() {
    var dataTable = $("#proposalList").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href + "/ajax",
            "type": "GET",
            "data": function(d) {
                // фильтры при необходимости
            },
            "error": function(xhr, error, code) {
                console.error('DataTable AJAX error:', { xhr, error, code, response: xhr.responseText });
                alert('Ошибка загрузки данных: ' + (xhr.responseJSON?.error || 'Неизвестная ошибка'));
            }
        },
        "pageLength": 10,
        "lengthMenu": [10, 20, 50, 100, 200, 500],
        "columns": [
            { "data": null, "orderable": false, "searchable": false }, // Чекбокс
            { "data": "name", "name": "name", "orderable": true, "searchable": true },
            { "data": "contract_id", "name": "contract_id", "orderable": true, "searchable": true },
            { "data": "contract_status", "name": "contract_status", "orderable": true, "searchable": true }, // переименовал для ясности
            { "data": "status", "name": "status", "orderable": true, "searchable": false },
        ],
        "columnDefs": [
            {
                "targets": 0,
                "render": function(data, type, row, meta) {
                    return `
                        <div class="item-checkbox ms-1">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input checkbox"
                                       id="checkBox_${row.id}" value="${row.id}">
                                <label class="custom-control-label" for="checkBox_${row.id}"></label>
                            </div>
                        </div>`;
                }
            },
            // Колонка 3: signed_status (статус подписания договора)
            {
                "targets": 3,
                "render": function(data, type, row, meta) {
                    const value = parseInt(data) || 0;
                    const contractId = row.contract_id;
                    const isDisabled = (contractId === null || contractId === '-' || contractId === '—' || contractId === undefined);

                    return `
                        <select class="contract_status form-control"
                                data-select2-selector="status"
                                data-id="${contractId}"
                                data-field="signed_status"
                                ${isDisabled ? 'disabled' : ''}>
                            <option value="0" data-bg="bg-danger" ${value === 0 ? 'selected' : ''}>Не подписан</option>
                            <option value="1" data-bg="bg-success" ${value === 1 ? 'selected' : ''}>Подписан</option>
                        </select>`;
                }
            },
            // Колонка 4: status (статус активации)
            {
                "targets": 4,
                "render": function(data, type, row, meta) {
                    const value = parseInt(data) || 0;
                    const contractId = row.contract_id;
                    const isDisabled = (contractId === null || contractId === '-' || contractId === '—' || contractId === undefined);

                    return `
                        <select class="contract_status form-control"
                                data-select2-selector="status"
                                data-id="${contractId}"
                                data-field="status"
                                ${isDisabled ? 'disabled' : ''}>
                            <option value="1" data-bg="bg-success" ${value === 1 ? 'selected' : ''}>Активирован</option>
                            <option value="0" data-bg="bg-danger" ${value === 0 ? 'selected' : ''}>Не активирован</option>
                        </select>`;
                }
            }
        ],
        "language": {
            "processing": "Загрузка...",
            "lengthMenu": "Показать _MENU_ записей",
            "zeroRecords": "Ничего не найдено",
            "info": "Записи с _START_ по _END_ из _TOTAL_",
            "infoEmpty": "Записей нет",
            "infoFiltered": "(отфильтровано из _MAX_ записей)",
            "search": "Поиск:",
            "paginate": {
                "first": "Первая",
                "last": "Последняя",
                "next": "Следующая",
                "previous": "Предыдущая"
            }
        },
        "order": [[1, 'asc']]
    });

    // Логика чекбоксов
    $(document).on('change', '#checkAllProject', function() {
        var isChecked = $(this).is(':checked');
        $('.checkbox').each(function() {
            $(this).prop('checked', isChecked);
            $(this).closest('tr').toggleClass('selected', isChecked);
        });
    });

    $(document).on('change', '.checkbox', function() {
        var $checkbox = $(this);
        var $row = $checkbox.closest('tr');
        var isChecked = $checkbox.is(':checked');

        $row.toggleClass('selected', isChecked);

        var allChecked = true;
        var anyChecked = false;

        $('.checkbox').each(function() {
            if ($(this).is(':checked')) {
                anyChecked = true;
            } else {
                allChecked = false;
            }
        });

        $('#checkAllProject').prop('checked', allChecked);
        $('#checkAllProject').prop('indeterminate', !allChecked && anyChecked);
    });

    // Обработчик изменения статусов
    $(document).on('change', '.contract_status', function () {
        const $select = $(this);
        const contractId = $select.data('id');
        const field = $select.data('field'); // 'signed_status' или 'status'
        const newStatus = $select.val();

        if ($select.prop('disabled') || !contractId || contractId === '-' || contractId === '—') {
            console.warn('Пропущено: селект неактивен или нет contract_id');
            return;
        }

        const originalValue = $select.data('original-value') ?? newStatus;
        $select.data('original-value', newStatus);

        $.ajax({
            url: `/admin/sellers/contracts/${contractId}/status`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                [field]: newStatus, // динамическое имя поля: signed_status или status
            },
            beforeSend: function () {
                const $spinner = $('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                $select.append($spinner);
                $select.prop('disabled', true);
            },
            success: function (response) {
                if (response.success) {
                    console.log('Статус обновлён:', response);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Статус обновлён',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }
                } else {
                    alert('Ошибка: ' + (response.message || 'Не удалось сохранить статус'));
                    $select.val(originalValue).prop('disabled', false).find('.spinner-border').remove();
                }
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr);
                let msg = 'Произошла ошибка при сохранении статуса';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors;
                    msg = errors ? Object.values(errors).flat().join(', ') : msg;
                }
                alert(msg);
                $select.val(originalValue).prop('disabled', false).find('.spinner-border').remove();
            },
            complete: function () {
                $select.find('.spinner-border').remove();
            },
        });
    });
});

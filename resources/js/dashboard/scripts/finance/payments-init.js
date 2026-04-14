$(document).ready(function() {
    var dataTable = $("#proposalList").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href + "/ajaxPayments",
            "type": "GET",
            "data": function(d) {
                d.search = $('#searchFilter').val();
                d.paid_filter = $('#statusFilter').val();
            },
            "error": function(xhr, error, code) {
                console.error('DataTable AJAX error:', {
                    xhr: xhr,
                    error: error,
                    code: code,
                    response: xhr.responseText
                });
                alert('Ошибка загрузки данных: ' + (xhr.responseJSON?.error || 'Неизвестная ошибка'));
            }
        },
        "pageLength": 10,
        "lengthMenu": [10, 20, 50, 100, 200, 500],
        "columns": [
            {
                "data": null,
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row, meta) {
                    return `
                <div class="item-checkbox ms-1">
                    <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input checkbox"
                       id="checkBox_${row.id}">
                <label class="custom-control-label" for="checkBox_${row.id}"></label>
            </div>
        </div>`;
                }
            },
            {
                "data": "id",
                "name": "Номер",
                "render": function(data) {
                    return `<a href="javascript:void(0);" class="fw-bold">#${data}</a>`;
                }
            },
            {
                "data": "created_at",
                "name": "Дата",
                "orderable": true
            },
            {
                "data": "order_link",
                "name": "Заказ",
                "orderable": false
            },
            {
                "data": "amount",
                "name": "Сумма",
                "render": function(data) {
                    return `${data} руб.`;
                }
            },
            {
                "data": "status_badge",
                "name": "Статус",
                "orderable": false,
                "searchable": false
            }
        ],
        "language": {
            "processing": "Загрузка...",
            "lengthMenu": "Показать _MENU_ записей",
            "zeroRecords": "Ничего не найдено",
            "info": "Записи с _START_ по _END_ из _TOTAL_",
            "infoEmpty": "Записей нет",
            "paginate": {
                "first": "Первая",
                "last": "Последняя",
                "next": "Следующая",
                "previous": "Предыдущая"
            }
        }
    });

    function showNotification(type, message) {
        if (type === 'success') {
            alert('Успех: ' + message);
        } else {
            alert('Ошибка: ' + message);
        }
    }

    // Обновляем таблицу при изменении фильтров
    $('#searchFilter, #statusFilter').on('change', function() {
        dataTable.draw();
    });
});

$(document).ready(function() {

    var cookieHelper = {
        set: function(name, value, days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                var expires = "; expires=" + date.toUTCString();
            } else {
                var expires = "";
            }
            document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
        },
        get: function(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
            return null;
        }
    };

    var dataTable = $("#proposalList").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href + "/ajax",
            "type": "GET",
            "data": function(d) {
                d.category = $('#categoryFilter').val();
                d.price_type = $('#priceTypeFilter').val();
                d.min_price = $('#minPriceFilter').val();
                d.max_price = $('#maxPriceFilter').val();
                d.search = $('#searchFilter').val();
                d.article = $('#articleFilter').val();
                d.sellerID = $('#SellerSelect').val();
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
                "render": function(data, type, row) {
                    const sellerId = $('meta[name="seller_id"]').attr('content');
                    return `<a href="javascript:void(0);" class="fw-bold">#${sellerId}-${data}</a>`;
                }
            },
            {
                "data": "items_html",
                "name": "Позиции",
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    return data; // Просто возвращаем HTML из сервера
                }
            },
            {
                "data": "seller_total",
                "name": "Сумма",
                "render": function(data) {
                    return `<span class="fw-bold text-dark">${data} руб.</span>`;
                }
            },
            {
                "data": "created_at",
                "name": "Дата",
                "render": function(data) {
                    return data;
                }
            },
            {
                "data": null,
                "name": "Статус",
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    const statusName = row.status;
                    const statusColor = row.status_color || 'secondary'; // Берём цвет из ответа сервера
                    return `<div class="badge bg-${statusColor} text-black p-2">${statusName}</div>`;
                }
            },
            {
                "data": "actions",
                "name": "Действия",
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

    // Обработчик изменения значения в select
    $('#SellerSelect').on('change', function() {
        // Сохраняем выбранное значение в куки (до закрытия браузера — без указания дней)
        cookieHelper.set('selectedSellerId', $(this).val());
        // Перезагружаем данные таблицы
        dataTable.ajax.reload();
    });
});

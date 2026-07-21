import 'datatables.net';
import 'datatables.net-bs5';

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
    var csrfToken = $("meta[name='csrf-token']").attr('content');
    // Инициализация DataTable
    var dataTable = $("#bookOrdersTable").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href + "/ajax", // Динамический URL, как в примере
            "type": "POST",                         // Используем GET, как в твоем образце
            "data": function(d) {
                // Собираем все фильтры здесь. Сейчас только sellerID, но структура готова
                d.sellerID = $('#sellerFilter').val() || $('#SellerSelect').val();
                d._token = csrfToken;
                // Сюда можно добавить другие фильтры, если появятся:
                // d.search = $('#bookSearch').val();
                // d.status = $('#statusFilter').val();
            },
            "error": function(xhr, error, code) {
                console.error('DataTable AJAX error (Book Orders):', {
                    xhr: xhr,
                    error: error,
                    code: code,
                    response: xhr.responseText
                });
                alert('Ошибка загрузки данных заказов книг: ' + (xhr.responseJSON?.error || 'Неизвестная ошибка'));
            }
        },
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "columns": [
            {
                "data": "image_html",
                "name": "book_name",
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    // Явно возвращаем HTML, чтобы DataTables не экранировал теги
                    return data;
                }
            },
            {
                "data": "amount",
                "name": "amount",
                "render": function(data) {
                    return `<span class="fw-bold text-dark">${data} руб.</span>`;
                }
            },
            {
                "data": "status",
                "name": "status",
                "orderable": true,
                "searchable": true,
                "render": function(data, type, row) {
                    const statusName = row.status;
                    const statusColor = row.status_color || 'secondary';
                    return `<span class="badge bg-${statusColor}">${statusName}</span>`;
                }
            },
            {
                "data": "created_at",
                "name": "created_at",
                "render": function(data) {
                    return data;
                }
            },
            {
                "data": "paid_at",
                "name": "paid_at",
                "render": function(data) {
                    return data === '-' ? '<span class="text-muted">—</span>' : data;
                }
            },
            {
                "data": "user_email",
                "name": "user.email",
                "render": function(data) {
                    return `<a href="mailto:${data}" class="text-decoration-none">${data}</a>`;
                }
            },
        ],
        "language": {
            "processing": "Загрузка данных заказов книг...",
            "lengthMenu": "Показать _MENU_ записей",
            "zeroRecords": "Ничего не найдено",
            "info": "Записи с _START_ по _END_ из _TOTAL_",
            "infoEmpty": "Записей нет",
            "search": "Поиск",
            "paginate": {
                "first": "Первая",
                "last": "Последняя",
                "next": "Следующая",
                "previous": "Предыдущая"
            }
        }
    });

    // Обработчик изменения значения в select (фильтр по продавцу)
    // Проверяем оба возможных ID селекта на всякий случай
    var $sellerSelect = $('#sellerFilter, #SellerSelect').first();

    if ($sellerSelect.length) {
        $sellerSelect.on('change', function() {
            var selectedVal = $(this).val();

            // Сохраняем выбранное значение в куки (до закрытия браузера)
            cookieHelper.set('selectedSellerId', selectedVal);

            // Перезагружаем данные таблицы с новым фильтром
            dataTable.ajax.reload();
        });

        // Восстановление значения из куки при загрузке страницы
        var savedSellerId = cookieHelper.get('selectedSellerId');
        if (savedSellerId && $sellerSelect.find('option[value="' + savedSellerId + '"]').length) {
            $sellerSelect.val(savedSellerId).trigger('change');
        }
    }
});


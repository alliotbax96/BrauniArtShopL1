$(document).ready(function() {
    var $table = $("#proposalList");
    if ($table.length === 0) {
        console.error('Элемент #proposalList не найден в DOM');
        return;
    }
    console.log('Таблица найдена, инициализируем DataTable...');

    var dataTable = $("#proposalList").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href + "/ajax",
            "type": "GET",
            "data": function(d) {
                d.search = $('#searchFilter').val();
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
            { "data": "name", "name": "Наименование", "orderable": false, "searchable": false },
            { "data": "article", "name": "Артикул" },
            { "data": "quantity", "name": "Остаток на складе" }
        ],
        "columnDefs": [
            {
                "targets": 0,
                "render": function(data, type, row, meta) {
                    return data; // HTML уже сформирован на сервере
                }
            },
            {
                "targets": 2,
                "render": function(data, type, row, meta) {
                    return data; // HTML уже сформирован на сервере
                }
            }
        ],
        "language": {
            "processing": "Загрузка...",
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

    $(document).on('change', 'input[form="stocks"]', function() {
        var $input = $(this);
        // Получаем ID товара из атрибута data-product-id строки таблицы
        var productId = $input.closest('tr').data('product-id');
        var newQuantity = $input.val();

        $.ajax({
            type: 'POST',
            url: '/seller/products/quantity',
            data: {
                product_id: productId,
                quantity: newQuantity,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    console.log('Остаток успешно обновлён');
                    $input.addClass('is-valid');
                    setTimeout(function() {
                        $input.removeClass('is-valid');
                    }, 1000);
                } else {
                    alert('Ошибка: ' + response.error);
                    $input.val($input.data('old-value'));
                    $input.addClass('is-invalid');
                    setTimeout(function() {
                        $input.removeClass('is-invalid');
                    }, 1000);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                alert('Ошибка при сохранении остатка: ' + (xhr.responseJSON?.error || 'Неизвестная ошибка'));
                $input.val($input.data('old-value'));
                $input.addClass('is-invalid');
                setTimeout(function() {
                    $input.removeClass('is-invalid');
                }, 1000);
            }
        });
    });


});

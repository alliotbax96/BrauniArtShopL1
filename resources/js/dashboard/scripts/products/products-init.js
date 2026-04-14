$(document).ready(function() {
    var dataTable = $("#proposalList").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href+"/ajax",
            "type": "GET",
            "data": function(d) {
                d.category = $('#categoryFilter').val();
                d.price_type = $('#priceTypeFilter').val();
                d.min_price = $('#minPriceFilter').val();
                d.max_price = $('#maxPriceFilter').val();
                d.search = $('#searchFilter').val();
                d.article = $('#articleFilter').val();
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
            { "data": null, "orderable": false, "searchable": false }, // Чекбокс
            { "data": "name", "name": "Наименование", "orderable": false, "searchable": false },
            { "data": "article", "name": "Артикул" },
            { "data": "category", "name": "Категория" },
            { "data": "price", "name": "Цена" }
        ],
        "columnDefs": [
            {
                "targets": 0,
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
                "targets": 1,
                "render": function(data, type, row, meta) {
                    // Данные уже содержат готовый HTML из контроллера
                    return data;
                }
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

    // Обновляем таблицу при изменении фильтров
    $('.filter-input').on('change keyup', function() {
        dataTable.draw();
    });

    // Логика работы с чекбоксами
    $(document).on('change', '#checkAllProject', function() {
        var isChecked = $(this).is(':checked');

        $('.checkbox').each(function() {
            $(this).prop('checked', isChecked);
            $(this).closest('.single-item').toggleClass('selected', isChecked);
        });
    });

    $(document).on('change', '.checkbox', function() {
        var $checkbox = $(this);
        var $row = $checkbox.closest('.single-item');
        var isChecked = $checkbox.is(':checked');

        // Добавляем/убираем класс selected у строки
        $row.toggleClass('selected', isChecked);

        // Проверяем, все ли чекбоксы отмечены
        var allChecked = true;
        var anyChecked = false;

        $('.checkbox').each(function() {
            if ($(this).is(':checked')) {
                anyChecked = true;
            } else {
                allChecked = false;
            }
        });

        // Обновляем состояние главного чекбокса
        $('#checkAllProject').prop('checked', allChecked);
        $('#checkAllProject').prop('indeterminate', !allChecked && anyChecked);
    });

    // Обработчик для удаления товаров
    $(document).on('click', '.delete_product', function() {
        var productId = $(this).data('id');
        if (confirm('Вы уверены, что хотите удалить этот товар?')) {
            $.ajax({
                type: 'GET',
                url: '/seller/products/delete/'+productId,
                data: {},
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                      location.reload();
                    } else {
                      alert(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error, xhr.responseText);
                }
            });
            console.log('Удаляем товар с ID:', productId);
        }
    });
});

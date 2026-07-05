$(document).ready(function () {
    var dataTable = $("#proposalList").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href + "/ajax",
            "type": "GET",
            "data": function (d) {
                d.type = $('#typeFilter').val();
                d.author = $('#authorFilter').val();
                d.status = $('#statusFilter').val();
                d.min_price = $('#minPriceFilter').val();
                d.max_price = $('#maxPriceFilter').val();
                d.search = $('#searchFilter').val();
            },
            "error": function (xhr, error, code) {
                console.error('DataTable AJAX error:', { xhr, error, code, response: xhr.responseText });
                alert('Ошибка загрузки данных: ' + (xhr.responseJSON?.error || 'Неизвестная ошибка'));
            }
        },
        "pageLength": 10,
        "lengthMenu": [10, 20, 50, 100, 200, 500],
        "columns": [
            {"data": null, "orderable": false, "searchable": false}, // Чекбокс
            {"data": "name", "name": "Название", "orderable": true}, // Теперь тут полный HTML
            {"data": "type", "name": "Тип"},
            {"data": "author", "name": "Автор"},
            {"data": "status", "name": "Статус"},
            {"data": "price", "name": "Цена"}
        ],
        "columnDefs": [
            {
                "targets": 0,
                "render": function (data, type, row, meta) {
                    return `
                    <div class="item-checkbox ms-1">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checkbox" id="checkBox_${row.id}">
                            <label class="custom-control-label" for="checkBox_${row.id}"></label>
                        </div>
                    </div>`;
                }
            },
            // УДАЛИЛИ блок рендера для targets: 1.
            // Теперь DataTable сам вставит HTML из поля name.

            {
                "targets": 4,
                "render": function (data, type, row, meta) {
                    const statusClasses = {
                        'draft': 'badge bg-soft-warning text-warning',
                        'moderation': 'badge bg-soft-info text-info',
                        'approved': 'badge bg-soft-success text-success',
                        'rejected': 'badge bg-soft-danger text-danger',
                        'published': 'badge bg-soft-primary text-primary'
                    };
                    const statusNames = {
                        'draft': 'Черновик',
                        'moderation': 'На модерации',
                        'approved': 'Одобрено',
                        'rejected': 'Отклонено',
                        'published': 'Опубликовано'
                    };

                    const className = statusClasses[data] || 'badge bg-soft-secondary text-secondary';
                    return `<span class="${className}">${statusNames[data]}</span>`;
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
        },
        // ВАЖНО: Разрешаем вывод HTML в колонке name
        "createdRow": function(row, data, dataIndex) {
            // Опционально: можно добавить класс строке, если нужно
        }
    });

    // ... остальной код (фильтры, чекбоксы, удаление) оставляем без изменений ...

    $('.filter-input').on('change keyup', function () {
        dataTable.draw();
    });

    $(document).on('change', '#checkAllProject', function () {
        var isChecked = $(this).is(':checked');
        $('.checkbox').each(function () {
            $(this).prop('checked', isChecked);
            $(this).closest('.single-item').toggleClass('selected', isChecked);
        });
    });

    $(document).on('change', '.checkbox', function () {
        var $checkbox = $(this);
        var $row = $checkbox.closest('.single-item');
        var isChecked = $checkbox.is(':checked');

        $row.toggleClass('selected', isChecked);

        var allChecked = true;
        var anyChecked = false;

        $('.checkbox').each(function () {
            if ($(this).is(':checked')) anyChecked = true;
            else allChecked = false;
        });

        $('#checkAllProject').prop('checked', allChecked);
        $('#checkAllProject').prop('indeterminate', !allChecked && anyChecked);
    });

    $(document).on('click', '.delete_book', function () {
        var bookId = $(this).data('id');
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        if (confirm('Вы уверены, что хотите удалить эту книгу?')) {
            $.ajax({
                type: 'DELETE',
                url: '/seller/books/delete/' + bookId,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.error || 'Произошла ошибка');
                    }
                },
                error: function (xhr) {
                    console.error('AJAX error:', xhr.responseText);
                    alert('Ошибка при удалении книги');
                }
            });
        }
    });

});

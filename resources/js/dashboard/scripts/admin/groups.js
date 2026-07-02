import 'datatables.net';
import 'datatables.net-bs5';

$(document).ready(function() {
    var dataTable = $("#productGroupsTable").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href+"/ajax",
            "type": "GET",
            "data": function(d) {
                d.search = $('#searchFilter').val();
                d.parent_id = $('#parentFilter').val();
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
            { "data": "name", "name": "Наименование", "orderable": true, "searchable": true },
            { "data": "parent", "name": "Родительская категория", "orderable": true, "searchable": true },
            { "data": "products_count", "name": "Кол-во товаров", "orderable": true, "searchable": false }
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
                    // Формируем HTML для столбца «Наименование» по аналогии с товарами
                    const hasImage = row.image && row.image !== '';
                    const imageHtml = hasImage
                        ? `<img src="${row.image}" alt="" class="img-fluid" style="width: 40px; height: 40px; object-fit: cover;">`
                        : '';

                    const warningHtml = !hasImage
                        ? '<p class="badge bg-soft-warning text-warning mt-1 mb-0">Изображение не загружено</p>'
                        : '';

                    // Проверяем, есть ли дочерние группы
                    const hasChildren = row.children_count > 0;
                    const childrenWarningHtml = hasChildren
                        ? '<p class="badge bg-soft-info text-info mt-1 mb-0">Есть дочерние группы (' + row.children_count + ')</p>'
                        : '';

                    const nameHtml = `
                        <div class="hstack gap-4">
                            <div class="avatar-image border-0">${imageHtml}</div>
                            <div>
                                <a href="/admin/productGroups/${row.id}" class="text-truncate-2-line fw-semibold">${row.name || 'Без названия'}</a>
                                ${warningHtml}
                                ${childrenWarningHtml}
                                <div class="project-list-action fs-12 d-flex align-items-center gap-3 mt-2">
                                    <a href="/admin/productGroups/${row.id}">Изменить</a>
                                    ${!hasChildren && row.products_count === 0 ? `
                                        <span class="vr text-muted"></span>
                                        <a href="javascript:void(0);" class="text-danger delete-group" data-id="${row.id}" data-name="${row.name}">Удалить</a>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;

                    return nameHtml;
                }
            },
            {
                "targets": 2,
                "render": function(data, type, row, meta) {
                    // Столбец с родительской категорией
                    if (!data || data === '—') {
                        return '<span class="text-muted">—</span>';
                    }
                    return `<span class="badge bg-secondary">${data}</span>`;
                }
            },
            {
                "targets": 3,
                "render": function(data, type, row, meta) {
                    // Столбец с количеством товаров
                    const count = data || 0;
                    let badgeClass = 'bg-secondary';
                    if (count > 50) badgeClass = 'bg-success';
                    else if (count > 10) badgeClass = 'bg-primary';
                    else if (count > 0) badgeClass = 'bg-info';

                    return `<span class="badge ${badgeClass}">${count} ${declension(count, ['товар', 'товара', 'товаров'])}</span>`;
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

    // Функция склонения слов
    function declension(number, titles) {
        const cases = [2, 0, 1, 1, 1, 2];
        return titles[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
    }

    // Обновляем таблицу при изменении фильтров
    $('.filter-input').on('change keyup', function() {
        dataTable.draw();
    });

    // Логика работы с чекбоксами
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

    // Обработчик для удаления группы
    $(document).on('click', '.delete-group', function() {
        var groupId = $(this).data('id');
        var groupName = $(this).data('name');

        if (confirm(`Вы уверены, что хотите удалить группу "${groupName}"?`)) {
            $.ajax({
                type: 'DELETE',
                url: `/admin/productGroups/${groupId}`,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        dataTable.draw();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Успешно!', response.message, 'success');
                        } else {
                            alert(response.message);
                        }
                    } else {
                        alert(response.error || 'Ошибка при удалении группы');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error, xhr.responseText);
                    let errorMsg = 'Произошла ошибка при удалении группы';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    alert(errorMsg);
                }
            });
        }
    });
});

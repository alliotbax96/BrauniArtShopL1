$(document).ready(function() {
    var dataTable = $("#proposalList").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href + "/ajax",
            "type": "GET",
            "data": function(d) {
                d.search = $('#searchFilter').val();
                d.group_id = $('#groupFilter').val();
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
                "data": "name",
                "name": "ФИО",
                "render": function(data) {
                    return `<a href="javascript:void(0);" class="fw-bold">${data}</a>`;
                }
            },
            {
                "data": "email",
                "name": "E-mail"
            },
            {
                "data": "phone",
                "name": "Телефон"
            },
            {
                "data": "group_name",
                "name": "Группа",
                "render": function(data, type, row) {
                    let selectHtml = `
                    <select class="user_group_edit form-control" data-select2-selector="status" data-id="${row.id}">`;
                    // Предполагаем, что группы передаются как массив в row.groups
                    row.groups.forEach(group => {
                        const selected = group.id === row.group_id ? 'selected' : '';
                        selectHtml += `
                        <option value="${group.id}" ${selected}>${group.name}(${group.description})</option>`;
                    });
                    selectHtml += `</select>`;
                    return selectHtml;
                }
            },
            // {
            //     "data": null,
            //     "name": "Действия",
            //     "orderable": false,
            //     "searchable": false,
            //     "render": function(data, type, row) {
            //         if(data.groups[0].id === 2) {
            //             return `<td class="text-end"></td>`;
            //         }
            //         return `<td class="text-end">
            //                     <a class="delete_user" data-id="${row.id}" href="javascript:void();">
            //                         <i class="feather feather-trash-2"></i>
            //                     </a>
            //                 </td>`;
            //     }
            // }
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
        "initComplete": function() {
            $(".user_group_edit").select2({
                theme: "bootstrap-5"
            });

            // Обработчик изменения выбора группы
            $('.user_group_edit').on('change', function() {
                const userId = $(this).data('id'); // ID пользователя из data-атрибута
                const groupId = $(this).val(); // Выбранный ID группы

                if (!userId || !groupId) {
                    console.error('Не указаны ID пользователя или группы '+userId+' '+groupId);
                    return;
                }
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                $.ajax({
                    url: '/seller/users/GroupUpdate', // URL маршрута
                    method: 'POST',
                    data: {
                        user_id: userId,
                        group_id: groupId,
                        _token: csrfToken // CSRF‑токен для защиты
                    },
                    success: function(response) {
                        if (response.success) {
                            // Показываем уведомление об успехе
                            showNotification('success', response.message);
                        } else {
                            // Показываем ошибку
                            showNotification('error', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        showNotification('error', 'Произошла ошибка при выполнении запроса');
                        console.error('AJAX error:', error);
                    }
                });
            });
        }
    });

    function showNotification(type, message) {
        // Здесь можно реализовать показ toast‑уведомлений или alert
        if (type === 'success') {
            alert('Успех: ' + message);
        } else {
            alert('Ошибка: ' + message);
        }
    }

    // Обновляем таблицу при изменении фильтров
    $('#searchFilter, #groupFilter').on('change', function() {
        dataTable.draw();
    });
});

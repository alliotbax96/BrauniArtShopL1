$(document).ready(function() {
    var dataTable = $("#proposalList").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href + "/ajax",
            "type": "GET",
            "data": function (d) {
                d.search = $('#searchFilter').val();
            },
            "error": function (xhr, error, code) {
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
                "render": function (data, type, row, meta) {
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
                "name": "Наименование",
                "orderable": true,
                "searchable": true
            },
            {
                "data": "type",
                "name": "Тип",
                "orderable": true,
                "searchable": true
            },
            {
                "data": "price",
                "name": "Цена",
                "orderable": true,
                "searchable": false
            },
            {
                "data": "difficulty",
                "name": "Сложность",
                "orderable": true,
                "searchable": true
            },
            {
                "data": "duration",
                "name": "Длительность",
                "orderable": true,
                "searchable": false
            },
            {
                "data": "min_age",
                "name": "Возраст",
                "orderable": true,
                "searchable": false
            },
            {
                "data": "players",
                "name": "Игроки",
                "orderable": true,
                "searchable": false
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
            },
            "emptyTable": "В таблице нет данных",
            "loadingRecords": "Загрузка...",
            "aria": {
                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                "sortDescending": ": активировать для сортировки столбца по убыванию"
            }
        }
    });


});


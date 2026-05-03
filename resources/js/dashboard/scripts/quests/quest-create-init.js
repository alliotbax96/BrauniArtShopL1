"use strict";
$(document).ready(function () {
    $(".file-upload").on("change", function () {
        var e, t;
        (e = this).files && e.files[0] && ((t = new FileReader).onload = function (e) {
            $(".upload-pic").attr("src", e.target.result)
        }, t.readAsDataURL(e.files[0]))
    }), $(".upload-button").on("click", function () {
        $(".file-upload").click()
    })
}),
$(document).ready(function () {
        var i = 0; // Начинаем с 0 — пока нет ни одной строки данных

        $("#add_row").click(function () {
            // Если это первый клик и строк ещё нет — добавляем стартовую строку
            if (i === 0 && $('#tab_logic tbody tr').length === 0) {
                $('#tab_logic tbody').append(`
                <tr id="addr0">
                    <td class="col-1">1</td>
                    <td>
                        <input type="text" name="additional_services[0][name]"
                               placeholder="Введите наименование" class="form-control">
                    </td>
                    <td>
                        <input type="number" name="additional_services[0][price]"
                               placeholder="Введите цену" class="form-control price"
                               step="1.00" min="1">
                    </td>
                </tr>
                <tr id="addr1"></tr>
            `);
                i = 1; // После добавления стартовой строки устанавливаем i = 1
            } else {
                // Обычная логика добавления строки на основе предыдущей
                var b = i - 1;

                $("#addr" + i).html($("#addr" + b).html())
                    .find("td:first-child")
                    .html(i + 1);

                // Добавляем следующую пустую строку для будущего добавления
                $("#tab_logic tbody").append('<tr id="addr' + (i + 1) + '"></tr>');
                i++;
            }
        });

        $("#delete_row").click(function () {
            if (i > 1) {
                // Удаляем последнюю строку данных (не пустую шаблонную)
                $("#addr" + (i - 1)).remove();
                i--;
            }
            // Если осталась только стартовая строка, оставляем i = 1
        });
    }),
$(document).ready(function () {
        const $timeslotsContainer = $('#timeslots-container');
        let timeslotCount = 1;

        // Обработчик для кнопки добавления таймслота
        $('#add-timeslot').on('click', function () {
            const newTimeslot = `
            <div class="timeslot-row mb-3 d-flex gap-3 align-items-end">
                <div class="flex-grow-1">
                    <label class="form-label">День недели</label>
                    <select class="form-select dey-of-week" name="timeslots[${timeslotCount}][day_of_week]" required>
                        <option value="">Выберите день</option>
                        <option value="Monday">Понедельник</option>
                        <option value="Tuesday">Вторник</option>
                        <option value="Wednesday">Среда</option>
                        <option value="Thursday">Четверг</option>
                        <option value="Friday">Пятница</option>
                        <option value="Saturday">Суббота</option>
                        <option value="Sunday">Воскресенье</option>
            </select>
        </div>
        <div>
            <label class="form-label">Время</label>
            <input type="time" class="form-control" name="timeslots[${timeslotCount}][start_time]" required>
        </div>
        <div>
            <label class="form-label">Цена</label>
            <input type="number" step="0.01" class="form-control" name="timeslots[${timeslotCount}][price]" placeholder="0 для базовой цены">
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- другие элементы -->
            <a href="javascript:void()" class="remove-timeslot">
                <i class="feather-trash"></i>
            </a>
        </div>
    </div>`;

            $timeslotsContainer.append(newTimeslot);
            timeslotCount++;
            $('.dey-of-week').select2({
                theme: 'bootstrap-5'
            })
        });

        // Делегированный обработчик для удаления таймслотов (работает и для динамически добавленных элементов)
        $timeslotsContainer.on('click', '.remove-timeslot', function () {
            $(this).closest('.timeslot-row').remove();
        });
    }),
$(document).ready(function () {
        $('#quest-create').on('submit', function (e) {
            e.preventDefault(); // Предотвращаем стандартную отправку формы
            const $form = $(this);
            const submitBtn = $form.find('button[type="submit"]');
            const originalBtnText = submitBtn.text();
            // Блокируем кнопку и показываем индикатор загрузки
            submitBtn.prop('disabled', true).text('Создание...');
            $.ajax({
                url: '/seller/quests/create',
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (response) {
                    // Всегда обрабатываем ответ, даже если статус не 200
                    if (response.success) {
                        // Успешное создание квеста
                        $('#quest-alert').html('<div class="alert alert-success">Квест успешно создан</div>');

                        // Перенаправление на страницу просмотра квеста или список квестов
                        if (response.data && response.data.quest_id) {
                            window.location.href = '/seller/quests/' + response.data.quest_id;
                        } else {
                            window.location.reload(); // Перезагрузка страницы, если нет ID
                        }
                    } else {
                        // Обработка ошибок (кроме валидации)
                        handleError(response.message || 'Произошла ошибка при создании квеста');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        // Ошибки валидации — показываем под соответствующими полями
                        const errors = xhr.responseJSON?.errors || {};
                        console.log(xhr);
                        handleError(xhr.responseJSON?.message || 'Произошла ошибка при создании квеста');
                        displayValidationErrors(errors);
                    } else if (xhr.status === 500) {
                        // Внутренняя ошибка сервера
                        handleError('Произошла внутренняя ошибка сервера. Попробуйте ещё раз.');
                    } else {
                        // Другие ошибки
                        handleError('Произошла непредвиденная ошибка. Проверьте подключение к интернету.');
                    }
                },
                complete: function () {
                    // Восстанавливаем кнопку после завершения запроса
                    submitBtn.prop('disabled', false).text(originalBtnText);
                }
            });
        });

// Функция отображения ошибок валидации
        function displayValidationErrors(errors) {
            // Очищаем предыдущие ошибки
            $('.form-error').remove();
            $('.is-invalid').removeClass('is-invalid');

            // Обрабатываем новые ошибки
            Object.keys(errors).forEach(field => {
                const errorMessage = errors[field][0]; // Берём первое сообщение для поля
                const $field = $('[name="' + field + '"], [name="' + field + '[]"]');

                if ($field.length) {
                    // Добавляем красную рамку к полю
                    $field.addClass('is-invalid');

                    // Создаём элемент с ошибкой под полем
                    const $errorElement = $('<div>')
                        .addClass('form-error text-danger fs-12 mt-1')
                        .text(errorMessage)
                        .attr('data-field', field);

                    // Вставляем после поля или его контейнера
                    if ($field.closest('.form-group').length) {
                        $field.closest('.form-group').append($errorElement);
                    } else {
                        $field.after($errorElement);
                    }
                }
            });
        }


// Общая функция обработки ошибок
        function handleError(message) {
            $('#quest-alert').html(
                '<div class="alert alert-danger">' + message + '</div>'
            );
        }


    }),
$(document).ready(function () {
        const featuresContainer = document.getElementById('features-container');
        const addFeatureInput = document.getElementById('add-feature');
        const addFeatureBtn = document.getElementById('add-feature-btn');
        let featuresArray = []; // Массив для хранения особенностей

// Функция для добавления тега
        function addTag(text, colorClass) {
            const tag = document.createElement('div');
            tag.className = `feature-tag ${colorClass}`;
            tag.innerHTML = `${text} <span class="remove-btn" onclick="removeTag(this)">×</span>`;
            tag.innerHTML += `<input type="hidden" name="features[]" value="${text}">`;

            featuresContainer.appendChild(tag);
            featuresArray.push(text); // Добавляем в массив

            addFeatureInput.value = ''; // Очищаем поле ввода
        }

// Функция для удаления тега
        window.removeTag = function (elem) {
            const tag = elem.parentNode;
            const text = tag.innerText.split('×'); // Получаем текст тега

            // Удаляем из DOM
            featuresContainer.removeChild(tag);

            // Удаляем из массива
            featuresArray = featuresArray.filter(feature => feature !== text);
        }

// Обработка нажатия кнопки "Добавить"
        addFeatureBtn.addEventListener('click', () => {
            const inputValue = addFeatureInput.value.trim();
            if (inputValue) {
                // Определяем класс цвета по тексту (можно расширить логику)
                let colorClass = 'personal'; // по умолчанию
                switch (inputValue.toLowerCase()) {
                    case 'vip':
                        colorClass = 'vip';
                        break;
                    case 'bugs':
                        colorClass = 'bugs';
                        break;
                    case 'team':
                        colorClass = 'team';
                        break;
                    case 'primary':
                        colorClass = 'primary';
                        break;
                }
                addTag(inputValue, colorClass);
            }
        });

// Обработка нажатия Enter в поле ввода
        addFeatureInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                addFeatureBtn.click();
            }
        });

// Функция для получения массива особенностей (например, для отправки на сервер)
        function getFeaturesArray() {
            return featuresArray;
        }

    }),
$(document).ready(function () {
        // Получаем textarea через jQuery
        var $textarea = $('#descriptionTextArea');
        // Получаем начальное содержимое
        var QuillText = $textarea.val();

        // Инициализируем редактор (если ещё не инициализирован)
        var quill = new Quill('#description', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{'header': [1, 2, 3, 4, 5, 6, false]}],
                    [{'color': []}, {'background': []}],
                    [{'font': []}],
                ]
            }
        });

        // Загружаем начальное содержимое в редактор, если оно есть
        if (QuillText && QuillText.trim()) {
            quill.clipboard.dangerouslyPasteHTML(0, QuillText);
        }

        // Синхронизируем изменения из Quill в textarea
        quill.on('text-change', function() {
            // Получаем содержимое в формате HTML
            var htmlContent = quill.root.innerHTML;

            // Обновляем значение textarea
            $textarea.html(htmlContent);

            // Опционально: триггер изменения для валидации и других обработчиков
            $textarea.trigger('input');
            $textarea.trigger('change');
        });

        // Дополнительно: синхронизация при потере фокуса редактором
        quill.root.addEventListener('blur', function() {
            var htmlContent = quill.root.innerHTML;
            $textarea.val(htmlContent);
            $textarea.trigger('blur');
        });
});
$(function () {
    $('#js-file-list').sortable();

    $('#choose-file').change(function () {
        const $fileInput = $(this);
        const $label = $('#choose-file-label');
        const files = $fileInput[0].files;

        // Проверка поддержки FormData
        if (window.FormData === undefined) {
            showNotification('В вашем браузере загрузка файлов не поддерживается', 'error');
            return;
        }

        // Валидация файлов
        if (!validateFiles(files)) {
            $fileInput.val('');
            return;
        }

        // Показываем индикатор загрузки
        $label.html('Загрузка... <i class="spinner-border spinner-border-sm"></i>');
        $label.prop('disabled', true);

        const formData = new FormData();
        const productId = $('#quest-create').find('input[name="product_id"]').val();

        // Добавляем CSRF‑токен
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        formData.append('_token', csrfToken);

        // Добавляем ID товара в данные
        formData.append('product_id', productId);
        formData.append('mode', 4);

        // Добавляем файлы
        $.each(files, function (key, file) {
            formData.append('file[]', file);
        });

        $.ajax({
            type: 'POST',
            url: `/seller/files/tempImageUpload`,
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: 'json',
            success: function (response) {
                console.log('Server response:', response);
                response = response[0];
                // Проверяем поле error в ответе
                if (response.error && response.error.trim() !== '') {
                    // Есть ошибка — показываем её
                    showNotification('Ошибка при загрузке: ' + response.error, 'error');
                } else {
                    // Ошибок нет — добавляем HTML превью в список
                    if (response.data && response.data.trim() !== '') {
                        $('#js-file-list').append(response.data);
                        // showNotification(`Загружено ${$(response.data).length} изображений`, 'success');
                    } else {
                        // Если data пустое, но error тоже пустое — странная ситуация
                        showNotification('Файлы загружены, но превью не созданы', 'warning');
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error details:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });

                // Обработка стандартных сетевых ошибок
                switch (xhr.status) {
                    case 401:
                        showNotification('Требуется авторизация. Обновите страницу.', 'error');
                        break;
                    case 403:
                        showNotification('Доступ запрещён. Проверьте права доступа.', 'error');
                        break;
                    case 419:
                        showNotification('Срок действия сессии истёк. Обновите страницу.', 'error');
                        break;
                    case 500:
                        showNotification('Внутренняя ошибка сервера. Попробуйте позже.', 'error');
                        break;
                    default:
                        showNotification('Ошибка сети: ' + error, 'error');
                }
            },
            complete: function () {
                // Восстанавливаем label и очищаем input
                $label.html('Выберите файл');
                $label.prop('disabled', false);
                $fileInput.val('');
            }
        });
    });

    // Функция валидации файлов
    function validateFiles(files) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        const maxSize = 5 * 1024 * 1024; // 5 МБ
        const maxFiles = 5;

        if (files.length > maxFiles) {
            showNotification(`Можно загрузить не более ${maxFiles} файлов`, 'error');
            return false;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // Проверка типа файла
            if (!allowedTypes.includes(file.type)) {
                showNotification('Допустимы только изображения (JPG, PNG, GIF)', 'error');
                return false;
            }

            // Проверка размера файла
            if (file.size > maxSize) {
                showNotification(`Файл "${file.name}" слишком большой. Максимум 5 МБ`, 'error');
                return false;
            }
        }

        return true;
    }

// Универсальная функция для уведомлений
    function showNotification(message, type) {
        // Здесь можно реализовать отображение уведомлений
        // Например, через Toast или простой alert
        alert(message);
    }

    /* Удаление загруженной картинки */
    window.remove_img = function (target) {
        const $card = $(target).closest('.card'); // Находим ближайший родительский .card

        if (confirm('Вы уверены, что хотите удалить это изображение?')) {
            $card.remove(); // Удаляем карточку изображения
        }
    }

});

// Константы
const API_TOKEN = "1a95c0aa5a5f5afd90ffd22d22cd9715288d0c23";
const SELECTORS = {
    Address: "#address",
    Station: "#station"
};

//Готовим опции для модуля подсказок
var options =
    {
        //Наше поле, куда пользователи будут вводить почтовые адреса
        id: SELECTORS.Address,
        //Веб-адрес облачной версии сервиса
        ahunter_url : "https://ahunter.ru/",
        //Не будем показывать предупреждающее сообщение, когда подсказок нет
        empty_msg : "",
        //Будем показывать только 5 топовых подсказок
        limit : 5,
        //Не будем показывать подсказки при возврате фокуса в поле ввода
        suggest_on_focus : false,
        //При выборе подсказки будем выводить её в отладочную консоль
        on_fetch : addressSelect,
        //Указываем свой открытый API-токен, чтобы работал on_fetch
        user : "dietmagazZVawG64zuqfKSz47sMB8E5",
        //Будем запрашивать подсказки для адресов Казахстана
        country : "ru",
    };
//Запускаем модуль подсказок
AhunterSuggest.Address.Solid( options );

$(SELECTORS.Station).suggestions({
    token: API_TOKEN,
    type: "METRO"
});
function addressSelect(Suggestion, Address) {
    $(SELECTORS.Address).parent().find('.form-error').remove();
    // Проверяем, пуст ли массив Address.stations
    if (!Address.stations) {
        var error = `<div class="form-error text-danger fs-12 mt-1" data-field="title">Не получается найти ближайшую станцию метро. Можете сами её указать.</div>`;
        $(SELECTORS.Address).parent().append(error);
        return; // Прерываем выполнение функции, если станция не определена
    }
    var query = Address.fields[0].name + ", " + Address.stations[0].name;
    var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/metro";
    var options = {
        method: "POST",
        mode: "cors",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "Authorization": "Token " + API_TOKEN
        },
        body: JSON.stringify({ query: query })
    };

    fetch(url, options)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json(); // Преобразуем ответ в JSON (объект/массив)
        })
        .then(data => {
            // Пример работы с данными как с массивом
            if (Array.isArray(data.suggestions)) {
                const suggestions = data.suggestions;
                addMetroInfo(suggestions[0].data);
            } else {
                console.log("Поле suggestions не является массивом или отсутствует");
            }
        })
        .catch(error => {
            console.error("Ошибка при выполнении запроса:", error);
        });
}


function addMetroInfo(metroData) {
    $(SELECTORS.Station).val(metroData.name+"("+metroData.line_name+")");
}



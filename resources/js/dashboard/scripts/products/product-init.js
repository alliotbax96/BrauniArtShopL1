"use strict";

let quill_change = null;
let quill_description = null;

$(document).ready(function () {
    // Инициализация валидации для всех форм
    $('#project-type, #project-settings, #project-budgets').validate();

    $('#project-price').validate({
        rules: {
            productPrice: {
                required: true,
                number: true,
                min: 0
            },
        },
        messages: {
            productPrice: {
                required: 'Цена товара обязательна для заполнения',
                number: 'Цена должна быть числом',
                min: 'Цена не может быть отрицательной'
            },
        },
        errorClass: 'error',
        validClass: 'valid',
        errorPlacement: function(error, element) {
            // Показываем ошибки под полем
            error.insertAfter(element);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).removeClass(validClass).addClass(errorClass);
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass(errorClass).addClass(validClass);
        }
    });

    $('#project-details').validate({
        rules: {
            productName: 'required',
            productGroupId: 'required',
            productSeller: 'required',
            productCode: 'string'
            // Добавьте правила для остальных полей по необходимости
        },
        messages: {
            productName: 'Название товара обязательно для заполнения',
            productGroupId: 'Группа товара обязательна для выбора',
            productSeller: 'Продавец товара обязателен для выбора',
            productCode: 'Код товара должен быть текстом'
        },
        errorClass: 'error',
        validClass: 'valid',
        errorPlacement: function(error, element) {
            // Показываем ошибки под полем
            error.insertAfter(element);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).removeClass(validClass).addClass(errorClass);
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass(errorClass).addClass(validClass);
        }
    });

    // Настройка шагов проекта
    $('#project-create-steps').steps({
        headerTag: 'div.step-title',
        bodyTag: 'section.step-body',
        transitionEffect: 'slideLeft',
        titleTemplate: '#title#',
        enableAllSteps: true,
        onStepChanging: function (event, currentIndex, newIndex) {
            // Валидация текущей формы при переходе между шагами
            if (newIndex > currentIndex) {
                const currentForm = $('.body.current form');
                if (currentForm.length === 1) {
                    currentForm.validate().settings.ignore = ':disabled,:hidden,.ajax-error';
                    return currentForm.valid();
                }
            }
            return true;
        },
        onFinishing: function (event, currentIndex) {
            const currentForm = $('.body.current form');
            if (currentForm.length === 1) {
                currentForm.validate().settings.ignore = ':disabled';
                return currentForm.valid();
            }
            return true;
        },
        onFinished: function () {
            const swalMixin = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success m-1',
                    cancelButton: 'btn btn-danger m-1'
                },
                buttonsStyling: false
            });

            swalMixin.fire({
                title: 'Все готово?',
                text: 'Вы уверены, что все готово и товар можно обновлять?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Да, все готово!',
                cancelButtonText: 'Нет, отмена!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    submitProductForm(); // Вызываем отправку формы через AJAX
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalMixin.fire(
                        'Отменено',
                        'Вы отменили изменение товара :)',
                        'error'
                    );
                }
            });
        }
    });

    // Инициализация Select2 для всех элементов
    $('#productGroup, #tragetAssigned, #billingType, #projectStatus, #projectTags, #tragetTags, [data-select2-teammates="teammates"], #sendcontactsNotifications').select2({
        theme: 'bootstrap-5'
    });

    // Получаем textarea через jQuery
    var $textarea = $('#ProductDescriptionTextArea');

    // Получаем начальное содержимое
    var QuillText = $textarea.val();

    // Инициализируем редактор (если ещё не инициализирован)
    var quill = new Quill('#ProductDescription', {
        theme: 'snow',
    });
    // Загружаем начальное содержимое в редактор, если оно есть
    if (QuillText && QuillText.trim()) {
        quill.pasteHTML(QuillText);
    }
    var syncTimeout = null;
    quill.on('text-change', function() {
            $textarea.html(quill.root.innerHTML);
    });

    // Поле «Вес» — только цифры и точка
    $('#ProductWeight').on('input', function() {
        let value = $(this).val();
        value = value.replace(/[^0-9.]/g, ''); // Удаляем запрещённые
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join(''); // Оставляем первую точку
        }
        $(this).val(value);
    });

    // Поле «Артикул» — только английские буквы, нижние подчёркивания и пробелы
    $('#ProductArticle').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9a-zA-Z_\s]/g, ''));
    });

    // Поля с классом priceInput — только цифры
    $('.price_input').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });
});
/**
 * Удаляет фото по ID
 * @param {string|number} id — ID элемента фото
 */
window.delete_photo = function($this, id) {
    const photoBlock = $($this).closest('.card');
    console.log($($this));

    if (!photoBlock.length) {
        console.error('Фото-блок не найден');
        return;
    }

    const imagePath = photoBlock.find('input[name="images[]"]').val();

    if (!imagePath) {
        console.error('Путь к изображению не найден');
        return;
    }

    // Получаем CSRF-токен из мета-тега
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: `/seller/files/tempImageDelete/${id}`,
        type: 'POST',
        data: { image: imagePath },
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(data) {
            console.log(data);
            if (data.success) {
                photoBlock.closest('.col-sm-6').remove();
            } else {
                console.warn('Не удалось удалить фото:', data.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Ошибка при удалении фото:', textStatus, errorThrown);
        }
    });
};
/**
 * Собирает данные всех шагов и отправляет через AJAX
 */
function submitProductForm() {
    // Собираем данные со всех форм шагов
    const formData = new FormData();
    const productId = $('#project-details').find('input[name="product_id"]').val(); // Получаем ID товара, если есть

    // Добавляем CSRF-токен
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    formData.append('_token', csrfToken);

    // Проходим по всем формам шагов и собираем данные
    $('.step-body form').each(function() {
        const $form = $(this);
        $form.find(':input').each(function() {
            const $input = $(this);
            const name = $input.attr('name');
            const type = $input.attr('type');

            if (name) {
                if (type === 'file' && $input[0].files.length > 0) {
                    $.each($input[0].files, function(i, file) {
                        formDat.append(name, file);
                    });
                }
                // Обычные поля
                else {
                    formData.append(name, $input.val());
                }
            }
        });
    });

    formData.append('productDescription', $('#ProductDescriptionTextArea').val());
    // Если есть изображения в списке, добавляем их
    const images = [];
    $('.photo-container').each(function() {
        const imageUrl = $(this).find('img').data('url');
        if (imageUrl) {
            images.push(imageUrl);
        }
    });
    if (images.length > 0) {
        formData.append('images', JSON.stringify(images));
    }

    // Определяем URL для отправки
    let url = '/seller/products';
    if (productId) {
        url += '/' + productId;
    }

    // Показываем индикатор загрузки
    const swalLoading = Swal.fire({
        title: 'Отправка данных...',
        text: 'Пожалуйста, подождите',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Отправляем AJAX-запрос
    $.ajax({
        type: 'POST',
        url: url,
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log(response);
            swalLoading.close();

            if (response.success) {
                $('#project-details').validate().resetForm();
                $('#project-details').validate().resetForm();

                Swal.fire({
                    icon: 'success',
                    title: 'Успех!',
                    text: response.message || 'Товар успешно сохранён'
                }).then(() => {
                    // Переходим на страницу товара или обновляем форму
                    if (response.redirect_url) {
                        window.location.href = response.redirect_url;
                    }
                });
            } else {
                if (response.detailed_errors) {
                    // Обрабатываем ошибки валидации от сервера
                    handleServerValidationErrors(response.detailed_errors);
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Ошибка!',
                    text: response.error || 'Произошла ошибка при сохранении'
                });
            }
        },
        error: function(xhr, status, error) {
            swalLoading.close();
            console.error('AJAX error:', error, xhr.responseText);

            Swal.fire({
                icon: 'error',
                title: 'Ошибка сети!',
                text: 'Произошла ошибка соединения. Попробуйте ещё раз.'
            });
        }
    });
}
function handleServerValidationErrors(detailedErrors) {
    // Сначала сбрасываем все ошибки
    $('#project-details').validate().resetForm();
    $('#project-price').validate().resetForm();

    $('.form-control').removeClass('error valid ajax-error');
    $('li').removeClass('error');

    // Обрабатываем ошибки от сервера
    $.each(detailedErrors, function(field, errorData) {
        var $field = $('[name="' + field + '"]');
        if ($field.length) {
            var step = $field.parents('section').attr('aria-labelledby');
            $('a[href="#'+step+'"]').parent('li').addClass('error')
            $field.addClass('error');
            $field.addClass('ajax-error');

            // Создаём элемент ошибки, если его ещё нет
            var $errorElement = $field.next('.error-message');
            if (!$errorElement.length) {
                $errorElement = $('<label id="ProductArticle-error" class="error" for="ProductArticle">Это поле является обязательным.</label>');
                $errorElement.insertAfter($field);
            }

            // Заполняем сообщения об ошибках
            var errorMessages = errorData.messages.join('<br>');
            $errorElement.html(errorMessages);
        }
    });
}

$(document).ready(function() {
    const API_TOKEN = "1a95c0aa5a5f5afd90ffd22d22cd9715288d0c23";
    initSuggestionsSelfAccount();
    // Маска для ИНН физлица (12 цифр)
    $('#selfEmployedINN').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Оставляем только цифры
        $("#checkINNBtn").prop('disabled', false);
        // Ограничиваем длину до 12 символов
        if (value.length > 12) {
            value = value.substr(0, 12);
        }
        $(this).val(value);

        // Убираем ошибки при вводе
        clearFieldError('#selfEmployedINN');
    });
    // Обработчик отправки формы
    $('#selfEmployed').on('submit', function (){
        saveSelfEmployedLegal($(this));
    });
    // Обработчик кнопки «Проверить»
    $('#checkINNBtn').on('click', function() {
        submitFormManually();
    });
    // Функция отправки формы по кнопке
    function submitFormManually() {
        const inn = $('#selfEmployedINN').val().trim();
        const userId = $('input[name="user_id"]').val();
        let isValid = true;

        // Очищаем предыдущие ошибки
        clearAllErrors();

        // Валидация ИНН перед отправкой
        if (!inn) {
            showFieldError('#selfEmployedINN', 'ИНН обязателен для заполнения');
            isValid = false;
        } else if (!/^\d{12}$/.test(inn)) {
            showFieldError('#selfEmployedINN', 'ИНН должен содержать только цифры');
            isValid = false;
        } else if (!validateINN(inn)) {
            showFieldError('#selfEmployedINN', 'Некорректный ИНН: неверные контрольные цифры');
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        // Отправляем запрос на сохранение записи с статусом notVerifiable
        saveSelfEmployedRecord(inn, userId);
    }
    // Сохранение записи самозанятого
    function saveSelfEmployedRecord(inn, userId) {
        showAlert('info', 'Сохраняем данные для проверки...');

        $.ajax({
            url: '/api/selfEmployed/save',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            data: {
                inn: inn,
                user_id: userId,
                verification_status: 'notVerifiable'
            },
            success: function(response) {
                $(".alert-block").remove();
                showAlert('success', 'Данные сохранены. Проверка статуса займет некоторое время.');
                $("#selfEmployedINN").prop('disabled', true);
                $("#checkINNBtn").prop('disabled', true);
                $("#PaymentDetails").addClass('hidden');
                blockINNField();
            },
            error: function(xhr) {
                let message = 'Ошибка при сохранении данных';

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(field => {
                        const errorMessage = errors[field][0];
                        switch(field) {
                            case 'inn':
                                showFieldError('#selfEmployedINN', errorMessage);
                                break;
                            case 'user_id':
                                showFieldError('input[name="user_id"]', errorMessage);
                                break;
                        }
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                showAlert('danger', message);
            }
        });
    }
    // Сохранение реквизитов
    function saveSelfEmployedLegal(form) {
        showAlert('info', 'Сохраняем данные...');

        $.ajax({
            url: '/api/selfEmployed/save',
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            data: form.serialize(),
            success: function(response) {
                $(".alert-block").remove();
                showAlert('success', 'Данные сохранены.');
            },
            error: function(xhr) {
                let message = 'Ошибка при сохранении данных';

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(field => {
                        const errorMessage = errors[field][0];
                        switch(field) {
                            case 'inn':
                                showFieldError('#selfEmployedINN', errorMessage);
                                break;
                            case 'user_id':
                                showFieldError('input[name="user_id"]', errorMessage);
                                break;
                            default:
                                showFieldError('input[name="'+field+'"]', errorMessage);
                                break;
                        }
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                showAlert('danger', message);
            }
        });
    }
    // Блокировка поля ИНН и показ заглушки
    function blockINNField() {
        $('#selfEmployedINN')
            .prop('disabled', true)
            .closest('.row')
            .after(`
                <div class="row mb-4 alert-block">
                    <div class="col-lg-4"></div>
            <div class="col-lg-8">
                <div class="alert alert-info">
                    Данные сохранены. Проверка статуса займет некоторое время
                </div>
            </div>
        </div>
    `);
    }
    // Валидация только 12‑значного ИНН физлица
    function validateINN(inn) {
        // Проверяем, что ИНН состоит ровно из 12 цифр
        if (inn.length !== 12 || !/^\d{12}$/.test(inn)) {
            return false;
        }

        return validateINN12(inn);
    }
    // Функция валидации 12‑значного ИНН (контрольные цифры)
    function validateINN12(inn) {
        const digits = inn.split('').map(Number);

        // Расчёт 11‑й контрольной цифры
        const sum11 = (
            7 * digits[0] +
            2 * digits[1] +
            4 * digits[2] +
            10 * digits[3] +
            3 * digits[4] +
            5 * digits[5] +
            9 * digits[6] +
            4 * digits[7] +
            6 * digits[8] +
            8 * digits[9]
        ) % 11;
        const control11 = sum11 % 10;

        // Расчёт 12‑й контрольной цифры
        const sum12 = (
            3 * digits[0] +
            7 * digits[1] +
            2 * digits[2] +
            4 * digits[3] +
            10 * digits[4] +
            3 * digits[5] +
            5 * digits[6] +
            9 * digits[7] +
            4 * digits[8] +
            6 * digits[9] +
            8 * digits[10]
        ) % 11;
        const control12 = sum12 % 10;

        // Проверка контрольных цифр
        return digits[10] === control11 && digits[11] === control12;
    }
    // Показ уведомлений
    function showAlert(type, message) {
        const alertClass = `alert-${type}`;
        const html = `
            <div class="alert ${alertClass} alert-dismissible fade show w-100" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        $('#selfEmployedAlert').html(html);

        // Автоматически скрываем через 5 секунд
        setTimeout(function() {
            $('#selfEmployedAlert .alert').alert('close');
        }, 5000);
    }
    // Подсветка поля с ошибкой
    function showFieldError(selector, message) {
        const $field = $(selector);
        $field.addClass('is-invalid');

        // Удаляем старую подсказку, если есть
        $field.next('.invalid-feedback').remove();

        // Добавляем подсказку с ошибкой
        $field.after(`<div class="invalid-feedback">${message}</div>`);
    }
    // Очистка ошибок поля
    function clearFieldError(selector) {
        const $field = $(selector);
        $field.removeClass('is-invalid');
        $field.next('.invalid-feedback').remove();
    }
    // Очистка всех ошибок
    function clearAllErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }
    function PaymentDetails(action='show'){
        var PaymentDetails = $("#PaymentDetails");
        if(action == 'show'){
            PaymentDetails.removeClass('hidden');
            PaymentDetails.find('input').prop('disabled', false);
        } else {
            PaymentDetails.addClass('hidden');
            PaymentDetails.find('input').prop('disabled', true);
        }
    }
    function initSuggestionsSelfAccount() {
        $("#bank_nameSelf").suggestions({
            token: API_TOKEN,
            type: "BANK",
            onSelect: handleBankSelectSelf
        });
        $("#account_holder_name").suggestions({
            token: API_TOKEN,
            type: "NAME",
            // onSelect: handleBankSelect
        });
    }
    function handleBankSelectSelf(suggestion){
        $("input[name='bic']").val(suggestion.data.bic).addClass('is-valid');
        $("input[name='correspondent_account']").val(suggestion.data.correspondent_account).addClass('is-valid');
        $("#bank_nameSelf").addClass('is-valid');
    }
});


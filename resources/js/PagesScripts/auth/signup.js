function timer(num) {
    var highestTimeoutId = setTimeout(";");
    for (var i = 0; i < highestTimeoutId; i++) {
        clearTimeout(i);
    }
    var timerBlock = $('.seconds');
    var index = num;
    var timerId = setInterval(function () {
        timerBlock.html(--index);
    }, 1000);

    setTimeout(function () {
        clearInterval(timerId);
        $("#get_sms").parent().html('<a href="#" id="get_sms" class="fs-11 text-primary">Получить код повторно</a>');
        $("#get_sms").click(function () {
            $.post('/sensmskode', { phone: $("input[name=\"phone\"]").val() }, function (data) {
                if (data == true) {
                    alert('Вам отправлено СМС с кодом!');
                    $("#get_sms").parent().html('<span id="get_sms" class="fs-11 text-primary">Получить код повторно через <span class="seconds">120</span> сек</span>');
                    timer(60);
                } else {
                    alert(data);
                }
            });

        });
    }, num * 1000);

}

$(".mask_phone").mask("+7 (999) 999-99-99", {
    onComplete: function () {
        $.post('/auth/code', $("#signup").serialize(), function (result) {
            if (result.result === true) {
                $("input[name=\"phone\"]").removeClass('is-invalid');
                $("input[name=\"phone\"]").addClass('is-valid');
                $("input[name=\"code\"]").parent().removeClass('hidden');
                $("#alert").html('<div class="alert alert-warning" role="alert">Сейчас вам позвонит наш робот, введите последние четыре цифры номер с которого поступил звонок!</div');
                $("#get_sms").html('Получить код повторно через <span class="seconds">60</span> сек</a>');
                timer(60);
            }
        });
    }
});

$(".code").mask("9999", {
    onComplete: function () {
        // removeclass("#" + $(this).attr('id'));
        $.post('/auth/checkCode', $("#signup").serialize(), function (result) {
            if (result.result === true) {
                $("input[name=\"code\"]").addClass('is-valid');
                $("input[name=\"code\"]").removeClass('is-invalid');
                $("#get_sms").html('');
                $("#alert").html('');
            } else {
                $("input[name=\"code\"]").removeClass('is-valid');
                $("input[name=\"code\"]").addClass('is-invalid');
                $("#alert").html('<div class="alert alert-danger" role="alert">'+result.error+'</div>');
            }
        });
    }
});

$('#signup').on('submit', function(event) {
    event.preventDefault(); // Предотвращаем стандартную отправку формы

    const $form = $(this);
    const $alertContainer = $('#alert');

    // Очищаем предыдущие состояния валидации
    function clearValidation() {
        $('input').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $alertContainer.html('');
    }

    clearValidation();

    $.post('/signup', $form.serialize())
        .done(function(data) {
            console.log(data);

            if (data.result) {
                // Успешная регистрация
                showAlert('success', 'Регистрация прошла успешно!');
                setTimeout(() => {
                    window.location.href = '/';
                }, 1500); // Задержка перед редиректом — пользователь успеет увидеть сообщение
            } else {
                handleError(data);
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Обработка ошибок AJAX-запроса
            console.error('AJAX error:', textStatus, errorThrown);
            showAlert('danger', 'Ошибка соединения. Проверьте интернет или попробуйте позже.');
        });
});

// Функция отображения алерта
function showAlert(type, message) {
    const alertClass = `alert-${type}`;
    const alertHtml = `<div class="alert ${alertClass}" role="alert">${message}</div>`;
    $('#alert').html(alertHtml);
}

// Функция обработки ошибок от сервера
function handleError(data) {
    if (data.error) {
        showAlert('danger', data.error);
    } else if (data.errors && data.errors.length > 0) {
        showAlert('danger', 'Проверьте правильность заполнения полей!');
        applyFieldErrors(data.errors);
    } else {
        showAlert('danger', 'Неизвестная ошибка! Обратитесь к администрации или попробуйте позже!');
    }
}

// Применение ошибок к конкретным полям
function applyFieldErrors(errors) {
    errors.forEach(function(error) {
        const $input = $(`input[name="${error.field}"]`);
        if ($input.length) {
            $input.addClass('is-invalid');
            const $parent = $input.parent();
            const feedbackId = `validationServer${error.field}Feedback`;

            // Проверяем, нет ли уже такого элемента
            if (!$parent.find(`#${feedbackId}`).length) {
                $parent.append(`
                    <div id="${feedbackId}" class="invalid-feedback">
                        ${error.message}
                    </div>
                `);
            }
        }
    });
}

export function showAlert(message, type = 'success') {
    const $alertBook = $('#alert_book');
    $alertBook
        .html(`<div class="alert alert-${type}">${message}</div>`)
        .hide()
        .slideDown(300);

    setTimeout(() => {
        $alertBook.find('.alert').fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);
}

export function getErrorMessages(xhr) {
    if (xhr.responseJSON?.errors) {
        const errors = [];
        $.each(xhr.responseJSON.errors, (key, messages) => {
            const $field = $(`[name="${key}"]`);
            if ($field.length) {
                $field.addClass('is-invalid');
                setTimeout(() => $field.removeClass('is-invalid'), 3000);
            }
            errors.push(...messages);
        });
        return errors.join('<br>');
    }

    if (xhr.responseJSON?.message) return xhr.responseJSON.message;

    const statusMessages = {
        422: 'Ошибка валидации данных',
        404: 'Ресурс не найден',
        500: 'Внутренняя ошибка сервера',
        403: 'Доступ запрещен',
        401: 'Необходима авторизация'
    };
    return statusMessages[xhr.status] || `Ошибка ${xhr.status}: Произошла неизвестная ошибка`;
}

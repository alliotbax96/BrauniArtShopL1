export function showAlert(message, type = 'success') {
    const $alertBook = $('#alert_chapter');
    $alertBook
        .html(`<div class="alert alert-${type}">${message}</div>`)
        .hide()
        .slideDown(300);

    setTimeout(() => {
        $alertBook.find('.alert').fadeOut(500, function () {
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

export function handleChapterFormSubmit(e, chapterData, $form, $submitBtn, originalBtnText) {
    e.preventDefault();

    // Очищаем предыдущие ошибки и алерт
    $('.is-invalid').removeClass('is-invalid');
    $('#alert_chapter').empty();

    // Синхронизация контента только для электронных книг
    if (window.bookType === 'ebook') {
        const $editorContainer = $('#editor-container .ql-editor');
        const htmlContent = $editorContainer.length > 0 ? $editorContainer.html() : '';
        const cleanHtml = htmlContent
            .replace(/<div class="ql-tooltip[^>]*>[\s\S]*?<\/div>/g, '')
            .replace(/<span class="ql-cursor[^>]*>[\s\S]*?<\/span>/g, '');
        $('#content-textarea').val(cleanHtml);
    }

    // Установка статуса
    if($('input[name="is_active"]').is(':checked')){
        $('input[name="status"]').val('published');
    } else {
        $('input[name="status"]').val('draft');
    }

    // Создаём FormData из формы
    const formData = new FormData($form[0]);

    // Определяем URL для запроса
    const url = chapterData.isEditMode && chapterData.chapterId
        ? `/seller/books/${chapterData.bookId}/chapters/${chapterData.chapterId}`
        : `/seller/books/${chapterData.bookId}/chapters`;

    // Если режим редактирования, добавляем метод PUT
    if (chapterData.isEditMode && chapterData.chapterId) {
        formData.append('_method', 'PUT');
    }

    // Блокируем кнопку отправки
    $submitBtn
        .prop('disabled', true)
        .val('Сохранение...')
        .addClass('btn-loading');

    // AJAX‑запрос
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function (data) {
            showAlert('Часть книги успешно сохранена!', 'success');

            // Если создание новой части, перенаправляем на страницу созданной части
            if (!chapterData.isEditMode && data.chapter) {
                setTimeout(() => {
                    window.location.href = `/seller/books/${data.chapter.book_id}/chapters/${data.chapter.id}`;
                }, 1500);
            } else if (chapterData.isEditMode && data.chapter) {
                console.log('Часть книги обновлена:', data.chapter);
                showAlert('Часть книги успешно обновлена!', 'success');
            }
        },
        error: function (xhr) {
            const errorMsg = getErrorMessages(xhr);
            showAlert(errorMsg, 'danger');

            // Прокручиваем к первому полю с ошибкой
            const $firstError = $('.is-invalid').first();
            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 100
                }, 500);
            }
        },
        complete: function () {
            // Восстанавливаем кнопку
            $submitBtn
                .prop('disabled', false)
                .val(originalBtnText)
                .removeClass('btn-loading');
        }
    });
}

import { showAlert, getErrorMessages } from './utils.js';

export function handleFormSubmit(e, bookData, $form, $submitBtn, originalBtnText) {
    e.preventDefault();

    $('.is-invalid').removeClass('is-invalid');
    $('#alert_book').empty();

    const formData = new FormData($form[0]);
    const url = bookData.isEditMode && bookData.bookId
        ? `/seller/books/${bookData.bookId}`
        : '/seller/books';


    if (bookData.isEditMode && bookData.bookId) {
        formData.append('_method', 'PUT');
    }

    $submitBtn
        .prop('disabled', true)
        .val('Сохранение...')
        .addClass('btn-loading');

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
        success: function(data) {
            showAlert('Книга успешно сохранена!', 'success');
            if (!bookData.isEditMode && data.book) {
                setTimeout(() => {
                    window.location.href = `/seller/books/${data.book.id}`;
                }, 1500);
            } else if (bookData.isEditMode && data.book) {
                console.log('Книга обновлена:', data.book);
            }
        },
        error: function(xhr) {
            const errorMsg = getErrorMessages(xhr);
            showAlert(errorMsg, 'danger');

            const $firstError = $('.is-invalid').first();
            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 100
                }, 500);
            }
        },
        complete: function() {
            $submitBtn
                .prop('disabled', false)
                .val(originalBtnText)
                .removeClass('btn-loading');
        }
    });
}


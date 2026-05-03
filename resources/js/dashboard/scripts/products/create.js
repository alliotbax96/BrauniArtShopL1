/* Удаление загруженной картинки */
window.remove_img = function(target) {
    const $card = $(target).closest('.card'); // Находим ближайший родительский .card

    if (confirm('Вы уверены, что хотите удалить это изображение?')) {
        $card.remove(); // Удаляем карточку изображения
    }
}

/* Удаление цифрового файла */
function remove_digital_file(target) {
    const $fileItem = $(target).closest('.file-item'); // Находим родительский элемент файла
    const fileId = $(target).data('file-id'); // Получаем ID файла из data-атрибута

    if (!fileId) {
        console.warn('Не найден ID файла для удаления');
        return;
    }

    if (confirm('Вы уверены, что хотите удалить этот цифровой файл?')) {
        $.ajax({
            url: '/products/manage_products/create/remove_digital_file/',
            type: 'POST',
            data: { file_id: fileId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $fileItem.remove(); // Удаляем элемент только после успешного ответа сервера
                    showNotification('Файл успешно удалён', 'success');
                } else {
                    showNotification('Ошибка при удалении файла: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotification('Ошибка сети: ' + error, 'error');
                console.error('AJAX error:', error);
            }
        });
    }
}

/* Инициализация sortable с сохранением порядка */
$(function() {
    $('#js-file-list').sortable();

    $('#choose-file').change(function() {
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
        const productId = $('#project-images').find('input[name="product_id"]').val();

        // Добавляем CSRF‑токен
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        formData.append('_token', csrfToken);

        // Добавляем ID товара в данные
        formData.append('product_id', productId);

        // Добавляем файлы
        $.each(files, function(key, file) {
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
            success: function(response) {
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
            error: function(xhr, status, error) {
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
            complete: function() {
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

});

/* Универсальная функция для уведомлений */
function showNotification(message, type) {
    // Здесь можно реализовать отображение уведомлений
    // Например, через Toast или простой alert
    alert(message);
}

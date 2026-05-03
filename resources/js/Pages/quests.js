$(".rising-rating").on("change", function(ev, data) {
    $("#form_estimation").val(data.to);
});
document.addEventListener('DOMContentLoaded', function() {
    const triggerTabList = [].slice.call(document.querySelectorAll('#myTab [data-bs-toggle="tab"]'));
    triggerTabList.forEach(function(triggerEl) {
        const tab = new bootstrap.Tab(triggerEl);

        triggerEl.addEventListener('click', function(e) {
            e.preventDefault();
            tab.show();
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const timeslotCells = document.querySelectorAll('.timeslot-active');
    const modal = document.getElementById('bookingModal');
    const closeBtn = document.querySelector('.close');
    const bookingForm = document.getElementById('bookingForm');

    // Данные авторизованного пользователя (должны передаваться с сервера)
    const authUserData = window.authUserData;

    // Загрузка дополнительных услуг
    async function loadAdditionalServices(questId) {
        try {
            const response = await fetch(`/api/quests/${questId}/services`);
            const data = await response.json();

            // Проверяем флаг success и наличие массива services
            if (data.success && data.services && data.services.length > 0) {
                const select = document.getElementById('additionalServices');
                select.innerHTML = '';

                data.services.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    // Убираем лишние нули после запятой в цене (100.00 → 100)
                    const priceWithoutDecimals = parseFloat(service.price).toFixed(0);
                    option.textContent = `${service.name} (+${priceWithoutDecimals} руб.)`;
                    option.dataset.price = service.price; // сохраняем исходную цену для расчётов
                    select.appendChild(option);
                });
            } else {
                // Если услуг нет, показываем сообщение
                const select = document.getElementById('additionalServices');
                select.innerHTML = '<option disabled>Дополнительные услуги отсутствуют</option>';
            }
        } catch (error) {
            console.error('Ошибка загрузки услуг:', error);
            // В случае ошибки показываем сообщение в выпадающем списке
            const select = document.getElementById('additionalServices');
            select.innerHTML = '<option disabled>Ошибка загрузки услуг</option>';
        }
    }

    // Обновление итоговой цены
    function updateTotalPrice() {
        // Исходная базовая цена слота (не меняется при расчётах)
        const baseSlotPrice = parseFloat(document.getElementById('baseSlotPrice').value);
        const basePlayerCount = parseInt(document.getElementById('basePlayerCount').value, 10);
        const additionalPlayerPrice = parseFloat(document.getElementById('additionalPlayerPrice').value);
        const playerCount = Math.max(
            parseInt(document.getElementById('playerCount').value, 10),
            basePlayerCount
        ); // Гарантируем минимум basePlayerCount игроков

        // Пересчитываем базовую стоимость с учётом количества игроков
        let calculatedBasePrice = baseSlotPrice;

        if (playerCount > basePlayerCount) {
            const additionalPlayers = playerCount - basePlayerCount;
            calculatedBasePrice += additionalPlayers * additionalPlayerPrice;
        }

        // Считаем стоимость дополнительных услуг (с нуля)
        let servicesCost = 0;
        const selectedServices = document.querySelectorAll('#additionalServices option:checked');
        selectedServices.forEach(option => {
            servicesCost += parseFloat(option.dataset.price);
        });

        // Итоговая цена = базовая цена (с учётом игроков) + услуги
        const total = calculatedBasePrice + servicesCost;

        // Обновляем отображение
        document.getElementById('displayTotalPrice').textContent = total.toFixed(0);

        // Обновляем hidden-поле для отправки формы
        document.getElementById('totalPrice').value = total;
    }

    // Открытие модального окна

    function openBookingModal(timeslotId, date, price, questId, basePlayerCount, additionalPlayerPrice) {
        document.getElementById('timeslotId').value = timeslotId;
        document.getElementById('date').value = date;
        document.getElementById('totalPrice').value = price;
        document.getElementById('baseSlotPrice').value = price; // сохраняем исходную цену
        document.getElementById('basePlayerCount').value = basePlayerCount;
        document.getElementById('additionalPlayerPrice').value = additionalPlayerPrice;

        // Устанавливаем минимальное количество игроков
        const playerCountInput = document.getElementById('playerCount');
        playerCountInput.min = basePlayerCount;
        playerCountInput.value = basePlayerCount; // по умолчанию — базовое количество

        // Заполняем данные пользователя, если авторизован
        if (authUserData.isAuthenticated) {
            document.getElementById('customerName').value = authUserData.name;
            document.getElementById('customerPhone').value = authUserData.phone;
        } else {
            // Очищаем поля, если не авторизован
            document.getElementById('customerName').value = '';
            document.getElementById('customerPhone').value = '';
        }
        $(".mask_phone").mask("+7 (999) 999-99-99");

        // Загружаем дополнительные услуги
        loadAdditionalServices(questId);

        // Обновляем итоговую цену с учётом базового количества игроков
        updateTotalPrice();

        modal.style.display = 'block';
    }

    // Закрытие модального окна
    function closeModal() {
        modal.style.display = 'none';
    }

    // Обработчики событий
    timeslotCells.forEach(cell => {
        cell.addEventListener('click', function() {
            const timeslotId = this.dataset.timeslotId;
            const date = this.dataset.date;
            const price = this.dataset.price;
            const questId = this.dataset.questId;
            const basePlayerCount = this.dataset.basePlayerCount; // берём из data-атрибута
            const additionalPlayerPrice = this.dataset.additionalPlayerPrice; // берём из data-атрибута

            openBookingModal(timeslotId, date, price, questId, basePlayerCount, additionalPlayerPrice);
        });
    });

    closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Обработка изменения выбора услуг
    document.getElementById('additionalServices').addEventListener('change', updateTotalPrice);
    // Обработчик изменения количества игроков
    document.getElementById('playerCount').addEventListener('change', updateTotalPrice);

    // Отправка формы
    bookingForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Очищаем предыдущие ошибки
        clearFormErrors();

        const formData = new FormData(bookingForm);

        try {
            const response = await fetch('/api/bookings', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Успешное бронирование
                showAlert('Бронирование успешно создано!', 'success');

                setTimeout(function (){
                    closeModal()
                }, 5000);
                bookingForm.reset();
            } else {
                if (result.errors) {
                    // Ошибки валидации
                    showValidationErrors(result.errors);
                } else if (result.message) {
                    // Другие ошибки
                    showAlert(result.message, 'danger');
                } else {
                    showAlert('Ошибка при бронировании', 'danger');
                }
            }
        } catch (error) {
            console.error('Ошибка сети:', error);
            showAlert('Произошла сетевая ошибка. Проверьте подключение и попробуйте снова.', 'danger');
        }
    });

    // Функция для отображения алерта
    function showAlert(message, type) {
        const alertElement = document.getElementById('bookingAlert');
        alertElement.className = `alert alert-${type}`;
        alertElement.textContent = message;
        alertElement.style.display = 'block';
    }

// Функция для очистки ошибок формы
    function clearFormErrors() {
        // Убираем классы ошибок у полей
        const errorFields = document.querySelectorAll('.is-invalid');
        errorFields.forEach(field => {
            field.classList.remove('is-invalid');
            // Удаляем подпись с ошибкой, если есть
            const existingError = field.nextElementSibling;
            if (existingError && existingError.classList.contains('field-error')) {
                existingError.remove();
            }
        });

        // Скрываем алерт
        const alertElement = document.getElementById('bookingAlert');
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }

// Функция для показа ошибок валидации
    function showValidationErrors(errors) {
        // Показываем общий алерт с первой ошибкой
        const firstError = Object.values(errors)[0][0];
        showAlert(firstError, 'danger');

        // Обрабатываем ошибки для каждого поля
        for (const [field, errorMessages] of Object.entries(errors)) {
            const fieldElement = document.querySelector(`[name="${field}"]`);
            if (fieldElement) {
                // Добавляем класс ошибки
                fieldElement.classList.add('is-invalid');

                // Создаём элемент с описанием ошибки
                const errorElement = document.createElement('div');
                errorElement.className = 'field-error';
                errorElement.textContent = errorMessages[0]; // Берём первое сообщение для поля
                errorElement.style.color = 'red';
                errorElement.style.fontSize = '0.8em';
                errorElement.style.marginTop = '5px';

                // Вставляем после поля
                fieldElement.parentNode.insertBefore(errorElement, fieldElement.nextSibling);
            }
        }
    }

});




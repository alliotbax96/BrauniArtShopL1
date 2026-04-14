$(document).ready(function () {
    let boxCounter = 1;
    const $boxesContainer = $('#boxesContainer');

    // Обработчики событий
    $(document).on('click', '.loadOrderBtn', function(e) {
        loadOrder($(this).attr('data-id'));
    });

    $(document).on('click', '.GoToDelivery', function (e){
        GoToDelivery($(this).attr('data-id'));
    });

    $('#addBoxBtn').on('click', addBox);

    function loadOrder(id) {
        $.ajax({
            url: '/seller/orders/packing',
            method: 'GET',
            data: {order_id: id},
            success: function (response) {
                if (response.success) {
                    console.log(response.data);
                    initializePacking(response.data);
                } else {
                    alert('Ошибка загрузки заказа: ' + response.error);
                }
            },
            error: function () {
                alert('Ошибка связи с сервером');
            }
        });
    }

    function initializePacking(orderData) {
        $boxesContainer.empty();
        boxCounter = 1;
        currentOrderData = orderData; // Сохраняем текущие данные заказа
        addBox();

        const $firstBoxItems = $('#box-1 .box-items');
        orderData.items.forEach(item => {
            item.order_id = orderData.order_id;
            addItemToBox($firstBoxItems, item);
        });

        updateAllWeights();
    }

    function addBox() {
        const boxId = `box-${boxCounter}`;
        const boxHtml = `
  <div class="card mb-4" id="${boxId}">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="h5 mb-0">Коробка ${boxCounter}</h3>
      <input type="hidden" class="box_number" value="${boxCounter}">
      <button type="button" class="btn btn-sm btn-outline-danger remove-box-btn">Удалить</button>
    </div>
    <div class="card-body">
      <p class="text-warning mb-3"><strong>Внимание!</strong> В одной коробке может быть товара общим весом не более 25 кг</p>
      <p>Вес: <span class="weight">0,0</span> кг (макс. 25 кг)</p>

      <!-- Блок размеров -->
      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">Длина (Д), см</label>
          <input type="number"
                 class="form-control length-input"
                 placeholder="см"
                 min="1"
                 step="1"
                 value="50">
        </div>
        <div class="col-md-4">
          <label class="form-label">Высота (В), см</label>
          <input type="number"
                 class="form-control height-input"
                 placeholder="см"
                 min="1"
                 step="1"
                 value="30">
        </div>
        <div class="col-md-4">
          <label class="form-label">Ширина (Ш), см</label>
          <input type="number"
                 class="form-control width-input"
                 placeholder="см"
                 min="1"
                 step="1"
                 value="40">
        </div>
      </div>

      <!-- Отображение объёма -->
      <div class="mb-3">
        <small class="text-muted">Объём: <span class="volume-value">60 000 см³</span></small>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Номер отправления</th>
              <th>Наименование</th>
              <th>Артикул</th>
              <th>Количество</th>
            </tr>
          </thead>
          <tbody class="box-items" data-box-id="${boxId}"></tbody>
        </table>
      </div>
    </div>
  </div>`;


        $boxesContainer.append(boxHtml);
        initBoxEvents(`#${boxId}`);
        boxCounter++;
    }

    function initBoxEvents(boxSelector) {
        const $box = $(boxSelector);
        const $itemsContainer = $box.find('.box-items');

        // Перетаскивание
        $itemsContainer.on('dragstart', 'tr', handleDragStart);
        $itemsContainer.on('dragover', 'tr', handleDragOver);
        $itemsContainer.on('drop', 'tr', handleDrop);

        // Удаление коробки
        $box.find('.remove-box-btn').on('click', function () {
            removeBox($box);
        });

        // Перетаскивание в пустую область коробки
        $box.on('dragover', '.card-body', function (e) {
            e.preventDefault();
        });

        $box.on('drop', '.card-body', function (e) {
            e.preventDefault();
            const draggedItemId = e.originalEvent.dataTransfer.getData('text/plain');
            const $draggedItem = $(`#${draggedItemId}`);
            if ($draggedItem.length) {
                const $targetContainer = $box.find('.box-items');
                moveOneItem($draggedItem, $targetContainer);
            }
        });


// Привязываем обработчики изменения размеров
        $box.find('.length-input, .height-input, .width-input').on('input', function() {
            calculateBoxVolume($box);
        });

// Инициализируем расчёт при создании коробки
        calculateBoxVolume($box);
    }

    function handleDragStart(e) {
        const $itemRow = $(e.target).closest('tr');
        if (!$itemRow.length) return;
        e.originalEvent.dataTransfer.setData('text/plain', $itemRow.attr('id'));
        $itemRow.addClass('dragging');
    }

    function handleDragOver(e) {
        e.preventDefault();
    }

    function handleDrop(e) {
        e.preventDefault();
        const draggedItemId = e.originalEvent.dataTransfer.getData('text/plain');
        const $draggedItem = $(`#${draggedItemId}`);
        if (!$draggedItem.length) return;

        let $targetContainer = $(e.target).closest('.box-items');
        if (!$targetContainer.length) {
            $targetContainer = $(e.target).closest('.card').find('.box-items');
        }
        if (!$targetContainer.length) return;

        moveOneItem($draggedItem, $targetContainer);
    }

    let isMoving = false; // Флаг для защиты от повторного вызова

    function moveOneItem($item, $targetContainer) {
        // Защита от повторного вызова
        if (isMoving) {
            console.log('Пропуск повторного вызова moveOneItem');
            return;
        }

        isMoving = true;
        console.log('=== НАЧАЛО ПЕРЕМЕЩЕНИЯ ===');

        try {
            const $sourceCard = $item.closest('.card');
            const $targetCard = $targetContainer.closest('.card');

            // Читаем текущее количество ДО любых изменений
            const currentQuantity = parseInt($item.find('[name="quantity"]').val()) || 0;
            console.log('Исходное количество:', currentQuantity);

            if (currentQuantity <= 0) {
                alert('Нельзя переместить товар — в коробке нет товаров');
                return;
            }

            // Проверка веса целевой коробки
            const targetWeight = parseFloat($targetCard.find('.weight').text()) || 0;
            const itemWeight = parseFloat($item.data('weight')) || 0;

            if (targetWeight + itemWeight > 25) {
                alert('Нельзя добавить товар — превышен лимит веса в 25 кг!');
                return;
            }

            const productId = $item.data('productId');
            let $targetItem = $targetContainer.find(`[data-product-id="${productId}"]`);

            // Создаём новый элемент в целевой коробке, если его там нет
            if (!$targetItem.length) {
                $targetItem = $item.clone();
                $targetItem.attr('id', `item-${Date.now()}`);
                $targetItem.find('[name="quantity"]').val(1);
                $targetContainer.append($targetItem);
                bindQuantityChangeHandler($targetItem.find('.quantity-input'));
            } else {
                // Увеличиваем количество на 1 в целевой коробке
                const targetQuantity = parseInt($targetItem.find('[name="quantity"]').val()) || 0;
                $targetItem.find('[name="quantity"]').val(targetQuantity + 1);
            }

            // Уменьшаем количество на 1 в исходной коробке
            $item.find('[name="quantity"]').val(currentQuantity - 1);

            // Обновляем defaultValue после всех изменений
            $item.find('[name="quantity"]').prop('defaultValue', currentQuantity - 1);
            if ($targetItem.length) {
                const newTargetQuantity = parseInt($targetItem.find('[name="quantity"]').val());
                $targetItem.find('[name="quantity"]').prop('defaultValue', newTargetQuantity);
            }

            // После перемещения — жёсткий контроль общего количества
            const orderQuantity = parseInt($item.find('[name="orderQuantity"]').val());
            enforceTotalQuantity(productId, orderQuantity);

            // Обновляем веса коробок
            updateWeight($targetCard);
            updateWeight($sourceCard);

            checkEmptyBox($sourceCard);
        } catch (error) {
            console.error('Ошибка в moveOneItem:', error);
        } finally {
            // Сбрасываем флаг после завершения операции
            setTimeout(() => {
                isMoving = false;
            }, 100);
        }
    }

    function addItemToBox($container, itemData) {
        const itemId = `item-${Date.now()}`;
        const newItemHtml = `
      <tr id="${itemId}" data-itemId="${itemData.item_id}" data-product-id="${itemData.product_id}" data-weight="${itemData.weight}" draggable="true">
        <td><input type="text" class="form-control" value="${itemData.seller_id+itemData.order_id+"-"+itemData.item_id || '—'}" readonly></td>
        <td><input type="text" class="form-control" value="${itemData.name}" readonly></td>
        <td><input type="text" class="form-control" value="${itemData.sku}" readonly></td>
        <td>
          <input type="number" class="form-control quantity-input" name="quantity" value="${itemData.quantity}" min="1" max="${itemData.order_quantity}">
          <input type="hidden" name="orderQuantity" value="${itemData.order_quantity}">
          <input type="hidden" name="itemWeight" value="${itemData.weight}">
        </td>
      </tr>`;

        $container.append(newItemHtml);
        bindQuantityChangeHandler($container.find(`#${itemId} .quantity-input`));
    }

    function bindQuantityChangeHandler($input) {
        // Удаляем существующий обработчик перед новой привязкой
        $input.off('change');

        $input.on('change', function(e) {
            handleQuantityChange(e);
        });
    }


    function handleQuantityChange(e) {
        const $input = $(e.target);
        const newQuantity = parseInt($input.val());
        const $currentRow = $input.closest('tr');
        const productId = $currentRow.data('productId');
        const orderQuantity = parseInt($currentRow.find('[name="orderQuantity"]').val());


        // Проверяем корректность ввода
        if (isNaN(newQuantity) || newQuantity < 1) {
            $input.val($input.prop('defaultValue'));
            return;
        }

        // Получаем текущее значение ДО изменений (из defaultValue)
        const currentQuantity = parseInt($input.prop('defaultValue')) || 0;

        // Если значение не изменилось — ничего не делаем
        if (newQuantity === currentQuantity) return;

        // Общее количество товара во всех коробках ДО изменений
        const totalBefore = getTotalQuantityInAllBoxes(productId);
        // Разница, которую нужно перераспределить
        const delta = newQuantity - currentQuantity;
        // Новое общее количество после изменений
        const potentialTotal = totalBefore + delta;

        // Если новое общее количество превышает лимит — запрещаем изменение
        if (potentialTotal > orderQuantity) {
            alert(`Нельзя установить количество ${newQuantity}. Общее количество товара не может превышать ${orderQuantity} шт.`);
            $input.val(currentQuantity);
            return;
        }

        // Обновляем значение и сохраняем новое значение по умолчанию
        $input.val(newQuantity);
        $input.prop('defaultValue', newQuantity);

        // Жёсткий контроль: перераспределяем так, чтобы общее количество ВСЕГДА равнялось лимиту заказа
        enforceTotalQuantity(productId, orderQuantity);

        // Обновляем вес коробки
        updateWeight($currentRow.closest('.card'));
    }

    // Подсчёт общего количества товара во всех коробках
    function getTotalQuantityInAllBoxes(productId) {
        let total = 0;
        $('.box-items').find(`[data-product-id="${productId}"]`).each(function() {
            const quantity = parseInt($(this).find('[name="quantity"]').val()) || 0;
            total += quantity;
        });
        return total;
    }


// Обновление веса коробки
    function updateWeight($box) {
        let weight = 0;
        $box.find('.box-items tr').each(function () {
            const itemWeight = parseFloat($(this).data('weight')) || 0;
            const quantity = parseInt($(this).find('[name="quantity"]').val()) || 0;
            weight += itemWeight * quantity;
        });
        $box.find('.weight').text(weight.toFixed(1));
    }

// Обновление весов всех коробок
    function updateAllWeights() {
        $('.card').each(function () {
            updateWeight($(this));
            calculateBoxVolume($(this)); // Пересчитываем объём для каждой коробки
        });
    }

// Проверка, не стала ли коробка пустой
    function checkEmptyBox($box) {
        const $itemsContainer = $box.find('.box-items');
        const hasItems = $itemsContainer.find('tr').length > 0;
        const totalQuantity = $itemsContainer.find('.quantity-input').get().reduce((sum, input) => {
            return sum + (parseInt($(input).val()) || 0);
        }, 0);

        if (!hasItems || totalQuantity === 0) {
            setTimeout(() => {
                if ($('.card').length > 1) {
                    $box.remove();
                }
            }, 300);
        }
    }

// Удаление коробки
    function removeBox($box) {
        if ($('.card').length > 1) {
            // Перед удалением собираем все данные о товарах в удаляемой коробке
            const itemsToReassign = [];
            $box.find('.box-items tr').each(function() {
                const $item = $(this);
                const productId = $item.data('productId');
                const quantity = parseInt($item.find('[name="quantity"]').val()) || 0;

                if (quantity > 0) {
                    itemsToReassign.push({
                        productId: productId,
                        quantity: quantity
                    });
                }
            });

            // Удаляем коробку
            $box.remove();

            // Перераспределяем товары из удалённой коробки
            reassignItemsFromRemovedBox(itemsToReassign);
        } else {
            alert('Нельзя удалить последнюю коробку!');
        }
    }


    function enforceTotalQuantity(productId, targetTotal) {
        // Находим все копии товара во всех коробках
        const $allCopies = $('.box-items').find(`[data-product-id="${productId}"]`);
        if ($allCopies.length === 0) return;

        // Пересчитываем текущее общее количество ПОСЛЕ перемещения
        let currentTotal = 0;
        $allCopies.each(function() {
            currentTotal += parseInt($(this).find('[name="quantity"]').val()) || 0;
        });

        const difference = targetTotal - currentTotal;
        if (difference === 0) return; // Всё в порядке

        distributeDifference($allCopies, difference);
    }

    function distributeDifference($copies, difference) {
        const copiesCount = $copies.length;
        if (copiesCount === 0 || difference === 0) return;

        const baseChange = Math.floor(difference / copiesCount);
        let remainder = difference % copiesCount;

        $copies.each(function(index) {
            const $input = $(this).find('[name="quantity"]');
            let currentQuantity = parseInt($input.val()) || 0;
            let newQuantity = currentQuantity + baseChange;

            // Распределяем остаток по одной единице
            if (remainder > 0) {
                newQuantity++;
                remainder--;
            } else if (remainder < 0) {
                newQuantity--;
                remainder++;
            }

            // Защита от отрицательных значений
            if (newQuantity < 0) newQuantity = 0;

            $input.val(newQuantity);
            $input.prop('defaultValue', newQuantity);
            updateWeight($(this).closest('.card'));
        });
    }

    function calculateBoxVolume($box) {
        const length = parseFloat($box.find('.length-input').val()) || 0;
        const height = parseFloat($box.find('.height-input').val()) || 0;
        const width = parseFloat($box.find('.width-input').val()) || 0;

        // Расчёт объёма в см³
        const volumeCm3 = length * height * width;

        // Форматирование с разделителями тысяч
        const formattedVolume = volumeCm3.toLocaleString('ru-RU');

        // Обновляем отображение объёма
        $box.find('.volume-value').text(`${formattedVolume} см³`);

        // Сохраняем объём в data‑атрибут коробки для дальнейшего использования
        $box.data('volume', volumeCm3);

        console.log(`Коробка ${$box.attr('id')}: Д=${length} см, В=${height} см, Ш=${width} см → Объём: ${formattedVolume} см³`);
    }

    function reassignItemsFromRemovedBox(items) {
        // Если нет товаров для перераспределения — выходим
        if (items.length === 0) return;

        items.forEach(item => {
            const { productId, quantity } = item;

            // Находим все оставшиеся коробки с этим товаром
            const $existingCopies = $('.box-items').find(`[data-product-id="${productId}"]`);

            if ($existingCopies.length > 0) {
                // Если есть коробки с этим товаром — добавляем количество к первой найденной
                const $firstCopy = $existingCopies.first();
                const currentQuantity = parseInt($firstCopy.find('[name="quantity"]').val()) || 0;
                const newQuantity = currentQuantity + quantity;

                $firstCopy.find('[name="quantity"]').val(newQuantity);
                $firstCopy.find('[name="quantity"]').prop('defaultValue', newQuantity);

                // Обновляем вес коробки
                updateWeight($firstCopy.closest('.card'));
            } else {
                // Если ни в одной коробке нет этого товара — добавляем в первую коробку
                const $firstBoxItems = $('.card:first .box-items');
                // Ищем исходный товар в данных заказа (предполагаем, что данные доступны)
                const originalItem = findOriginalItemData(productId);

                if (originalItem) {
                    originalItem.quantity = quantity; // Устанавливаем количество из удалённой коробки
                    addItemToBox($firstBoxItems, originalItem);
                }
            }
        });

        // После перераспределения проверяем общее количество по всем товарам
        $('.box-items [data-product-id]').each(function() {
            const $item = $(this);
            const productId = $item.data('productId');
            const orderQuantity = parseInt($item.find('[name="orderQuantity"]').val());

            enforceTotalQuantity(productId, orderQuantity);
        });
    }

    // Глобальная переменная для хранения данных заказа
    let currentOrderData = null;

// Обновляем функцию loadOrder для сохранения данных
    function loadOrder(id) {
        $("#OrderID").html(id);
        $.ajax({
            url: '/seller/orders/packing',
            method: 'GET',
            data: { order_id: id },
            success: function (response) {
                if (response.success) {
                    currentOrderData = response.data; // Сохраняем данные заказа
                    initializePacking(response.data);
                } else {
                    alert('Ошибка загрузки заказа: ' + response.error);
                }
            },
            error: function () {
                alert('Ошибка связи с сервером');
            }
        });
    }

    function findOriginalItemData(productId) {
        if (!currentOrderData || !currentOrderData.items) return null;
        return currentOrderData.items.find(item => item.product_id === productId);
    }

    function GoToDelivery(orderId){
        $.ajax({
            url: '/seller/orders/SentForDelivery/'+orderId,
            method: 'GET',
            data: {},
            success: function (response) {
                // console.log(response);
                if (response.success) {
                    alert('Заказ успешно отгружен! Передайте его в пункт выдачи!');
                    location.reload();
                } else {
                    alert('Ошибка отгрузки заказа: ' + response.error);
                }
            },
            error: function () {
                alert('Ошибка связи с сервером');
            }
        });
    }

    function submitPackingForm(orderId) {
        // Собираем данные формы
        const packingData = {
            order_id: orderId, // Статичный ID заказа (заменено с window.currentOrderId)
            boxes: []
        };

        $('.card').each(function() {
            const $box = $(this);

            // Рассчитываем объём
            const length = parseFloat($box.find('.length-input').val()) || 0;
            const height = parseFloat($box.find('.height-input').val()) || 0;
            const width = parseFloat($box.find('.width-input').val()) || 0;
            const volume = length * height * width;

            const boxData = {
                box_number: $box.find('.box_number').val(),
                length: length,
                height: height,
                width: width,
                volume: volume,
                weight: parseFloat($box.find('.weight').text()) || 0,
                notes: $box.find('.notes-input').val() || null,
                items: []
            };

            $box.find('.box-items tr').each(function() {
                const $item = $(this);
                boxData.items.push({
                    product_id: $item.data('productId'),
                    quantity: parseInt($item.find('[name="quantity"]').val()) || 0,
                    item_id: $item.data('itemid'),
                });
            });

            if (boxData.items.length > 0) {
                packingData.boxes.push(boxData);
            }

        });


        // Добавляем CSRF‑токен
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Показываем индикатор загрузки
        const $submitBtn = $('#savePackingBtn');
        const originalBtnText = $submitBtn.html();
        $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохраняем...').prop('disabled', true);

        // Отправка данных на сервер
        $.ajax({
            url: '/seller/orders/packing',
            method: 'POST',
            data: {
                _token: csrfToken,
                data: packingData
            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    // Успешное сохранение
                    alert('Данные успешно сохранены!');

                    // Закрываем модальное окно
                    $('#addNewTasks').modal('hide');

                    // Обновляем таблицу заказов
                    if (typeof $('#proposalList').DataTable === 'function') {
                        $('#proposalList').DataTable().ajax.reload();
                    }
                } else {
                    // Ошибка от сервера
                    let errorMessage = response.error || 'Неизвестная ошибка';

                    if (response.errors) {
                        // Обрабатываем ошибки валидации
                        errorMessage = 'Ошибки валидации:\n';
                        $.each(response.errors, function(field, messages) {
                            errorMessage += `- ${messages.join(', ')}\n`;
                        });
                    }

                    alert(`Ошибка сохранения: ${errorMessage}`);
                }
            },
            error: function(xhr, status, error) {
                // Критическая ошибка (500, 404 и т. д.)
                let errorMessage = 'Произошла непредвиденная ошибка';

                if (xhr.status === 422) {
                    // Ошибки валидации
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        errorMessage = 'Ошибки заполнения формы:\n';
                        $.each(errors, function(field, messages) {
                            errorMessage += `- ${messages.join(', ')}\n`;
                        });
                    }
                } else if (xhr.status === 500) {
                    errorMessage = 'Внутренняя ошибка сервера. Попробуйте позже.';
                } else if (xhr.status === 403) {
                    errorMessage = 'У вас нет прав для выполнения этого действия.';
                } else if (xhr.status === 0) {
                    errorMessage = 'Нет соединения с сервером. Проверьте интернет.';
                }

                alert(`Ошибка: ${errorMessage}`);
            },
            complete: function() {
                // Восстанавливаем кнопку
                $submitBtn.html(originalBtnText).prop('disabled', false);
            },
            statusCode: {
                422: function(response) {
                    console.log(response);
                }
            }
        });
    }



// Привязываем обработчик к кнопке отправки формы
    $(document).ready(function() {
        $('#savePackingBtn').on('click', function(){
            submitPackingForm($("#OrderID").html());
        });
    });


});

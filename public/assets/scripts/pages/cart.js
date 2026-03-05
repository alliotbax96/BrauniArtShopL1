
$(document).ready(function () {
    (function(w) {
        function startWidget() {
            w.YaDelivery.createWidget({
                containerId: 'pvz_map',         // Идентификатор HTML-элемента (контейнера), в котором будет отображаться виджет
                params: {
                    city: "Москва",                     // Город, отображаемый на карте при запуске
                    size:{                              // Размеры виджета
                        "height": "800px",              // Высота
                        "width": "100%"                 // Ширина
                    },
                    // source_platform_station: "05e809bb-4521-42d9-a936-0fb0744c0fb3",  // Станция отгрузки
                    physical_dims_weight_gross: 10000,  // Вес отправления
                    delivery_price: "от 100",           // Стоимость доставки
                    delivery_term: "от 1 дня",          // Срок доставки
                    show_select_button: true,           // Отображение кнопки выбора ПВЗ (false — скрыть кнопку, true — показать кнопку)
                    filter: {
                        type: [                         // Тип способа доставки
                            "pickup_point",             // Пункт выдачи заказа
                            "terminal"                  // Постамат
                        ],
                        is_yandex_branded: false,       // Тип пункта выдачи заказа (false — Партнерские ПВЗ, true — ПВЗ Яндекса)
                        payment_methods: [              // Способ оплаты
                            "already_paid",             // Доступен для доставки предоплаченных заказов
                            "card_on_receipt"           // Доступна оплата картой при получении
                        ],
                        payment_methods_filter: "or"    // Фильтр по типам оплаты
                    }
                },
            });
        }
        w.YaDelivery
            ? startWidget()
            : document.addEventListener('YaNddWidgetLoad', startWidget);
    })(window);

    // Подписка на событие

    document.addEventListener('YaNddWidgetPointSelected', function (data) {
        // $.post('/add_pvz', { client_pvz: data.detail.id, pvz_name: data.detail.address.full_address, pvz_kode: null}, function (data) {
        $('.collapse').hide();
        //     console.log(data);
        //     $("#pvz").html(data);
        // });
    });
});


/*=============================================
	=    		 Cart Active  	         =
=============================================*/

$(document).ready(function() {
    $('.point-select').select2({theme: 'bootstrap-5'});
});

class CartManager {
    constructor() {
        this.init();
    }

    init() {
        // Инициализация кнопок +/-
        this.initQuantityButtons();
        // Добавляем обработчики для полей ввода количества
        this.initInputHandlers();
        // Обработчики удаления товаров
        this.initRemoveButtons();
    }

    /**
     * Инициализация кнопок увеличения/уменьшения количества
     */
    initQuantityButtons() {
        document.querySelectorAll('.cart-plus-minus').forEach(container => {
            // Создаём кнопки, если их ещё нет
            if (!container.querySelector('.qtybutton')) {
                const minusBtn = document.createElement('div');
                minusBtn.className = 'dec qtybutton';
                minusBtn.textContent = '−';

                const plusBtn = document.createElement('div');
                plusBtn.className = 'inc qtybutton';
                plusBtn.textContent = '+';

                container.appendChild(minusBtn);
                container.appendChild(plusBtn);
            }
        });

        // Делегирование событий для кнопок
        document.addEventListener('click', (e) => {
            if (e.target.matches('.qtybutton')) {
                this.handleQuantityChange(e.target);
            }
        });
    }

    /**
     * Обработчик изменения количества через кнопки
     */
    handleQuantityChange(button) {
        const input = button.closest('.cart-plus-minus').querySelector('input[type="number"]');
        if (!input) return;

        const currentValue = parseInt(input.value) || 0;
        let newValue;

        if (button.classList.contains('inc')) {
            newValue = currentValue + 1;
        } else {
            newValue = Math.max(0, currentValue - 1);
        }

        input.value = newValue;
        this.updateItemQuantity(input);
    }

    /**
     * Инициализация обработчиков для полей ввода
     */
    initInputHandlers() {
        document.querySelectorAll('input.item_count').forEach(input => {
            input.addEventListener('change', () => {
                this.updateItemQuantity(input);
            });

            // Ограничиваем ввод только числами
            input.addEventListener('input', () => {
                input.value = input.value.replace(/[^0-9]/g, '');
            });
        });
    }

    /**
     * Обновление количества товара в корзине
     */
    async updateItemQuantity(input) {
        const itemId = input.dataset.id;
        const newQuantity = parseInt(input.value);

        if (!itemId || isNaN(newQuantity) || newQuantity < 0) {
            this.showError('Некорректное количество');
            return;
        }

        try {
            const response = await fetch(`/cart/update/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    item_id: itemId,
                    quantity: newQuantity
                })
            });

            if (!response.ok) {
                throw new Error('Ошибка сети');
            }

            const data = await response.json();

            if (data.success) {
                this.updateUI(data, itemId);
            } else {
                this.showError(data.message || 'Ошибка при обновлении корзины');
            }
        } catch (error) {
            console.error('Error updating cart item:', error);
            this.showError('Произошла ошибка при обновлении корзины');
        }
    }

    /**
     * Обновление интерфейса после успешного изменения количества
     */
    updateUI(data, itemId) {
        // Обновляем сумму для конкретного товара
        const sumElement = document.getElementById(`sum${itemId}`);
        if (sumElement) {
            sumElement.textContent = this.formatPrice(data.item.subtotal);
        }

        // Обновляем общую сумму корзины
        const totalElement = document.getElementById('pitog');
        if (totalElement) {
            totalElement.textContent = this.formatPrice(data.total_price);
        }

        // Обновляем количество товаров в шапке (если есть)
        const cartCount = document.querySelector('[data-cart-count]');
        if (cartCount) {
            cartCount.textContent = data.cart_items_count;
        }
    }

    /**
     * Инициализация обработчиков удаления товаров
     */
    initRemoveButtons() {
        document.addEventListener('click', async (e) => {
            if (e.target.closest('.wishlist-remove.cart_item_del')) {
                e.preventDefault();
                const button = e.target.closest('.wishlist-remove.cart_item_del');
                const itemId = button.dataset.id ||
                    button.closest('tr').dataset.itemId;

                if (itemId) {
                    await this.removeItem(itemId);
                }
            }
        });
    }

    /**
     * Удаление товара из корзины
     */
    async removeItem(itemId) {
        try {
            const response = await fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Ошибка сети');

            const data = await response.json();

            if (data.success) {
                this.removeItemFromUI(itemId);
                this.updateTotalUI(data);
            } else {
                this.showError(data.message || 'Ошибка при удалении товара');
            }
        } catch (error) {
            console.error('Error removing cart item:', error);
            this.showError('Произошла ошибка при удалении товара');
        }
    }

    /**
     * Удаление товара из интерфейса
     */
    removeItemFromUI(itemId) {
        const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
        if (row) {
            row.remove();
        }

        // Если корзина стала пустой, показываем соответствующее сообщение
        if (document.querySelectorAll('tr[data-item-id]').length === 0) {
            location.reload(); // Перезагружаем страницу для показа сообщения «Корзина пуста»
        }
    }

    /**
     * Обновление общего интерфейса корзины
     */
    updateTotalUI(data) {
        const totalElement = document.getElementById('pitog');
        if (totalElement) {
            totalElement.textContent = this.formatPrice(data.total_price);
        }

        const cartCount = document.querySelector('[data-cart-count]');
        if (cartCount) {
            cartCount.textContent = data.cart_items_count;
        }
    }

    /**
     * Форматирование цены (1234.56 → 1 234,56)
     */
    formatPrice(price) {
        return new Intl.NumberFormat('ru-RU', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(price);
    }

    /**
     * Показ сообщения об ошибке
     */
    showError(message) {
        // Создаём или обновляем элемент с ошибкой
        let alertDiv = document.getElementById('alert');
        if (!alertDiv) {
            alertDiv = document.createElement('div');
            alertDiv.id = 'alert';
            document.querySelector('.shop-cart-widget').prepend(alertDiv);
        }

        alertDiv.innerHTML = `
        <div class="alert alert-danger" role="alert">
            ${message}
        </div>
    `;

        // Автоматически скрываем через 5 секунд
        setTimeout(() => {
            if (alertDiv) alertDiv.innerHTML = '';
        }, 5000);
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    new CartManager();
});




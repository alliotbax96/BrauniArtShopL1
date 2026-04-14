


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
            newValue = Math.max(1, currentValue - 1);
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
                input.value = input.value.replace(/[^1-9]/g, '');
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

    $(function() {
        const containerId = 'pvz_map';
        const defaultCity = 'Москва';

        function initDeliveryWidget() {
            if (window.YaDelivery) {
                createWidget();
            } else {
                document.addEventListener('YaNddWidgetLoad', createWidget);
            }
        }

        function createWidget() {
            window.YaDelivery.createWidget({
                containerId: containerId,
                params: {
                    city: defaultCity,
                    size: {
                        height: '800px',
                        width: '100%'
                    },
                    physical_dims_weight_gross: 10000,
                    delivery_price: 'от 100',
                    delivery_term: 'от 1 дня',
                    show_select_button: true,
                    filter: {
                        type: ['pickup_point', 'terminal'],
                        is_yandex_branded: false,
                        payment_methods: ['already_paid', 'card_on_receipt'],
                        payment_methods_filter: 'or'
                    }
                }
            });
        }

        function handlePointSelection(event) {
            const { detail } = event;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            $.ajax({
                url: '/user/pvz/update',
                type: 'POST',
                data: JSON.stringify({
                    client_pvz: detail.id,
                    pvz_name: detail.address.full_address,
                    pvz_kode: null
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                contentType: false,
                processData: false,
                success: function(response) {
                    $('.collapse').hide();
                    console.log(response);

                    // Преобразуем response.pvzs в набор option элементов
                    if (response.pvzs && Array.isArray(response.pvzs)) {
                        const optionsHtml = response.pvzs.map(pvz =>
                            `<option value="${pvz.pvz}">${pvz.pvz_name}</option>`
                        ).join('');

                        $('#pvz').html(optionsHtml);
                    } else {
                        // Обрабатываем случай, когда pvzs отсутствует или не является массивом
                        $('#pvz').html('<option value="">Нет доступных ПВЗ</option>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Ошибка отправки данных:', textStatus, errorThrown);
                    // В случае ошибки показываем сообщение об ошибке
                    $('#pvz').html('<option value="">Ошибка загрузки ПВЗ</option>');
                }
            });
        }

        initDeliveryWidget();
        document.addEventListener('YaNddWidgetPointSelected', handlePointSelection);
    });




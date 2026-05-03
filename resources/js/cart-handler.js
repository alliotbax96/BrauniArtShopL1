// cart-handler.js
class CartHandler {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('click', this.handleEvents.bind(this));
        // Добавляем обработчик для обновления корзины при загрузке страницы
        this.updateMiniCart();
    }

    handleEvents(e) {
        // Обработка добавления в корзину
        const addLink = e.target.closest('.AdToCartLink');
        if (addLink) {
            e.preventDefault();
            this.handleAddToCart(addLink);
            return;
        }

        // Обработка удаления из корзины
        const removeLink = e.target.closest('.cart_item_del, .minicart form button[type="submit"]');
        if (removeLink) {
            e.preventDefault();
            const form = removeLink.closest('form');
            if (form) {
                this.handleRemoveFromCart(form, removeLink); // Передаём кнопку как параметр
            }
            return;
        }
    }

    async handleAddToCart(link) {
        const productId = link.dataset.productId;
        if (!productId) {
            console.error('Не найден productId в data-атрибутах');
            this.showNotification('Ошибка: не указан товар', 'error');
            return;
        }

        link.classList.add('loading');
        const originalText = link.textContent;
        link.textContent = 'Добавляем...';

        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('product_id', productId);
            formData.append('seller_id', link.dataset.sellerId || '');
            formData.append('quantity', parseInt(link.dataset.quantity) || 1);
            formData.append('product_type', link.dataset.productType || 'App\\Models\\Product');

            const response = await fetch('/cart/add', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();

            link.classList.remove('loading');
            link.textContent = originalText;

            if (data.success) {
                this.updateCartBadge(data.total_items || 0);
                this.updateMiniCart(); // Обновляем мини‑корзину
                this.showNotification(data.message, 'success');
                link.style.animation = 'cart-pulse 0.5s ease';
                setTimeout(() => { link.style.animation = ''; }, 500);
            } else {
                this.showNotification(data.message || 'Ошибка при добавлении в корзину', 'error');
            }
        } catch (error) {
            console.error('Ошибка AJAX‑запроса:', error);
            link.classList.remove('loading');
            link.textContent = originalText;
            this.showNotification('Произошла ошибка. Проверьте консоль.', 'error');
        }
    }

    async handleRemoveFromCart(form, button) { // Получаем кнопку как параметр
        const itemId = form.dataset.itemId || form.action.split('/').pop();

        // Проверяем флаг skip-ajax
        if (button && button.classList.contains('skip-ajax')) {
            return; // Выходим из функции — форма будет отправлена стандартным способом
        }

        if (!confirm('Вы уверены, что хотите удалить этот товар из корзины?')) {
            return;
        }

        button.classList.add('loading'); // Используем переданный параметр

        try {
            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();

            if (data.success) {
                this.updateCartBadge(data.total_items || 0);
                this.updateMiniCart(); // Обновляем мини‑корзину
                this.showNotification(data.message, 'success');

                // Удаляем строку товара из мини‑корзины
                const cartItemRow = form.closest('li.d-flex');
                if (cartItemRow) {
                    cartItemRow.style.transition = 'opacity 0.3s ease';
                    cartItemRow.style.opacity = '0';
                    setTimeout(() => {
                        if (cartItemRow && cartItemRow.parentNode) {
                            cartItemRow.parentNode.removeChild(cartItemRow);
                        }
                        // Если корзина стала пустой, показываем сообщение
                        if (document.querySelectorAll('.minicart li.d-flex').length === 0) {
                            this.renderEmptyCartMessage();
                        }
                    }, 300);
                }
            } else {
                this.showNotification(data.message || 'Ошибка при удалении товара', 'error');
                button.classList.remove('loading');
            }
        } catch (error) {
            console.error('Ошибка при удалении:', error);
            this.showNotification('Ошибка сети. Проверьте консоль.', 'error');
            button.classList.remove('loading');
        }
    }

    // Функция для обновления мини‑корзины
    async updateMiniCart() {
        try {
            const response = await fetch('/cart/mini', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Не удалось загрузить данные корзины');

            const data = await response.json();
            this.renderMiniCart(data);
        } catch (error) {
            console.error('Ошибка обновления мини‑корзины:', error);
        }
    }

    // Отрисовка мини‑корзины
    renderMiniCart(cartData) {
        const miniCart = document.getElementById('minicart');
        if (!miniCart) return;

        let html = '';

        if (cartData.items && cartData.items.length > 0) {
            cartData.items.forEach(item => {
                html += `
        <li class="d-flex align-items-start">
            <div class="cart-img">
                <a href="${item.product_url}">
                    <img src="${item.image_url}" alt="${item.name}">
                </a>
            </div>
            <div class="cart-content">
                <h4>
            <a href="${item.product_url}">
                ${item.name} (x${item.quantity})
            </a>
                </h4>
                <div class="cart-price">
                    <span class="new">
                ${item.total_price} руб.<br>
                (${item.price_per_unit} руб./шт)
            </span>
                </div>
            </div>
            <div class="del-icon">
                <form action="${item.remove_url}" method="POST" style="display: inline;">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" style="background: none; border: none; cursor: pointer;">
                <i class="far fa-trash-alt"></i>
            </button>
                </form>
            </div>
        </li>`;
            });

            // Добавляем итоговую сумму и ссылку на корзину
            html += `
        <li>
            <div class="total-price">
                <span class="f-left">Итого:</span>
                <span class="f-right">${cartData.total_price} руб.</span>
            </div>
        </li>
        <li>
            <div class="checkout-link">
                <a href="/cart">Корзина</a>
            </div>
        </li>`;
        } else {
            html = '<li class="empty-cart-message"><p>Корзина пуста</p></li>';
        }

        miniCart.innerHTML = html;

// Обновляем счётчик и общую сумму в шапке
        this.updateCartBadge(cartData.total_items || 0);


        const cartTotalPriceElement = document.querySelector('.cart-total-price');
        if (cartTotalPriceElement) {
            cartTotalPriceElement.textContent = `${cartData.total_price} руб.`;
        }

// Обновляем количество товаров в счётчике корзины
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = cartData.total_items || 0;
        }
    }

// Функция для отображения сообщения о пустой корзине
    renderEmptyCartMessage() {
        const miniCart = document.getElementById('minicart');
        if (!miniCart) return;

        miniCart.innerHTML = '<li class="empty-cart-message"><p>Корзина пуста</p></li>';

        // Обновляем счётчики
        this.updateCartBadge(0);
        const cartTotalPriceElement = document.querySelector('.cart-total-price');
        if (cartTotalPriceElement) {
            cartTotalPriceElement.textContent = '0 руб.';
        }
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = '0';
        }
    }

    updateCartBadge(count) {
        const badge = document.querySelector('.cart-badge, .cart-count');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }

    showNotification(message, type) {
        document.querySelectorAll('.notification').forEach(n => n.remove());
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '15px 20px',
            backgroundColor: type === 'success' ? '#4CAF50' : '#F44336',
            color: 'white',
            borderRadius: '4px',
            zIndex: '10000',
            boxShadow: '0 2px 10px rgba(0,0,0,0.2)'
        });
        document.body.appendChild(notification);
        setTimeout(() => {
            if (notification.parentNode) notification.remove();
        }, 3000);
    }
}

// Инициализируем один раз при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    window.cartHandler = new CartHandler();
});


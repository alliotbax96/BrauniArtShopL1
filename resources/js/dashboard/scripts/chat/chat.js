import Pusher from 'pusher-js';

class ChatApp {
    constructor(chatId, userId) {
        this.chatId = chatId;
        this.userId = userId;
        this.pusher = null;
        this.channel = null;

        console.log('🔔 ChatApp: Инициализация чата', { chatId, userId });

        // Проверяем доступность глобальных переменных
        if (!window.pusherAppKey || !window.pusherCluster) {
            console.error('❌ ChatApp: Отсутствуют настройки Pusher (pusherAppKey или pusherCluster)');
            return;
        }

        this.init();
    }

    init() {
        console.log('🚀 ChatApp: Запуск инициализации...');

        try {
            // Инициализация Pusher с отладкой
            this.pusher = new Pusher(window.pusherAppKey, {
                cluster: window.pusherCluster,
                encrypted: true,
                debug: true, // Включаем отладку Pusher
                logToConsole: true // Выводим логи Pusher в консоль
            });

            console.log('✅ ChatApp: Pusher инициализирован');

            // Подписка на канал чата
            console.log(`📡 ChatApp: Подписываемся на канал 'chat.${this.chatId}'...`);
            this.channel = this.pusher.subscribe(`chat.${this.chatId}`);

            // Обработчики системных событий Pusher
            this.channel.bind('pusher:subscription_succeeded', () => {
                console.log('✅ ChatApp: Успешная подписка на канал');
                this.setupMessageSending();
            });

            this.channel.bind('pusher:subscription_error', (status) => {
                console.error('❌ ChatApp: Ошибка подписки на канал:', status);
            });

            // Привязка событий
            this.bindEvents();

            // Отметка сообщений как прочитанных
            this.markMessagesAsRead();

            // Автоскролл к последним сообщениям
            this.scrollToBottom();

            console.log('🎉 ChatApp: Инициализация завершена успешно');
        } catch (error) {
            console.error('💥 ChatApp: Критическая ошибка при инициализации:', error);
        }
    }

    bindEvents() {
        console.log('🎯 ChatApp: Привязываем события к каналу...');

        this.channel.bind('message.sent', (data) => {
            console.log('📩 ChatApp: Получено событие "message.sent"', data);

            const message = data.message;
            if (message.user_id !== this.userId) {
                console.log('💬 ChatApp: Добавляем новое сообщение в чат (не от текущего пользователя)');
                this.addMessageToChat(message);
                this.playNotificationSound();
                this.markMessagesAsRead();
            } else {
                console.log('💬 ChatApp: Сообщение от текущего пользователя — пропускаем отображение');
            }
        });

        // Обработчик события о прочтении сообщений
        this.channel.bind('messages.read', (data) => {
            console.log('📖 ChatApp: Получено событие "messages.read"', data);

            // Обновляем метки непрочитанных сообщений
            this.updateUnreadMarkers(data);
        });

    }

    updateUnreadMarkers(data) {
        console.log('🔄 ChatApp: Обновляем метки непрочитанных сообщений...', data);
        // Для других пользователей — обновляем только их сообщения
        const messages = document.querySelectorAll(`[data-message-id]`);
        messages.forEach(messageEl => {
            const messageId = messageEl.getAttribute('data-message-id');
            const messageUserEl = messageEl.querySelector('.avatar-image img');
            if (messageUserEl) {
                const messageUserId = messageUserEl.getAttribute('alt')?.split(' ')[0]; // Либо другой способ получения ID
                // Здесь нужна корректная логика извлечения user_id из DOM
                // В реальной реализации лучше хранить user_id в data-атрибуте
            }
        });
    }


    setupMessageSending() {
        const sendButton = document.querySelector('.send-message a');
        const messageInput = document.querySelector('.emoji-picker');

        if (!sendButton) {
            console.warn('⚠️ ChatApp: Кнопка отправки сообщений не найдена');
            return;
        }

        if (!messageInput) {
            console.warn('⚠️ ChatApp: Поле ввода сообщений не найдено');
            return;
        }

        sendButton.addEventListener('click', () => {
            console.log('🖱️ ChatApp: Клик по кнопке отправки сообщения');
            this.sendMessage(messageInput);
        });

        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                console.log('↩️ ChatApp: Нажатие Enter — отправляем сообщение');
                this.sendMessage(messageInput);
            }
        });

        console.log('✅ ChatApp: Обработчики отправки сообщений настроены');
    }

    async sendMessage(input) {
        const content = input.value.trim();

        if (!content) {
            console.log('⚠️ ChatApp: Попытка отправить пустое сообщение — игнорируем');
            return;
        }

        console.log('📤 ChatApp: Отправляем сообщение:', { content, chatId: this.chatId });

        try {
            const response = await fetch(`/seller/chat/${this.chatId}/message`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ content })
            });

            const data = await response.json();

            if (response.ok) {
                console.log('✅ ChatApp: Сообщение успешно отправлено', data);
                input.value = '';
                this.scrollToBottom();
            } else {
                console.error('❌ ChatApp: Ошибка отправки сообщения:', {
                    status: response.status,
                    statusText: response.statusText,
                    data
                });
            }
        } catch (error) {
            console.error('💥 ChatApp: Критическая ошибка отправки:', error);
        }
    }

    addMessageToChat(message) {
        console.log('💾 ChatApp: Добавляем сообщение в DOM:', message);

        const messageElement = this.createMessageElement(message);
        const chatBody = document.querySelector('.content-area-body');

        if (!chatBody) {
            console.error('❌ ChatApp: Контейнер .content-area-body не найден!');
            return;
        }

        chatBody.insertAdjacentHTML('beforeend', messageElement);
        console.log('✅ ChatApp: Сообщение добавлено в чат');
        this.scrollToBottom();
    }

    createMessageElement(message) {
        console.log('🎨 ChatApp: Создаём HTML для сообщения:', message.id);
        console.log(message.user.id+"==="+this.userId);
        console.log(message);
        return `
            <div class="single-chat-item mb-5" data-message-id="${message.id}">
                <div class="d-flex ${String(message.user.id) === String(this.userId) ? 'flex-row-reverse' : ''} align-items-center gap-3 mb-3">
                    <a href="javascript:void(0)" class="avatar-image">
                        <img src="${message.user.avatar || '/assets/dashboard/images/avatar/1.png'}" class="img-fluid rounded-circle" alt="${message.user.name}">
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="javascript:void(0);">${message.user.name}</a>
                <span class="wd-5 ht-5 bg-gray-400 rounded-circle"></span>
                <span class="fs-11 text-muted">${this.formatTime(message.created_at)}</span>
            </div>
        </div>
        <div class="wd-500 p-1 rounded-5 bg-gray-200 ${String(message.user.id) === String(this.userId) ? 'ms-auto' : ''}">
            <p class="py-2 px-3 rounded-5 bg-white mb-0">${message.content}</p>
        </div>
        ${!message.is_read && message.user.id === this.userId ? '<span class="fs-10 text-muted ms-3 no-read">Непрочитано</span>' : ''}
    </div>`;
    }

    playNotificationSound() {
        // Можно добавить звук уведомления
        // const audio = new Audio('/sounds/notification.mp3');
        // audio.play();
    }

    async markMessagesAsRead() {
        console.log('📝 ChatApp: Отмечаем сообщения как прочитанные...');

        try {
            await fetch(`/seller/chat/${this.chatId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            console.log('✅ ChatApp: Сообщения отмечены как прочитанные');
        } catch (error) {
            console.error('❌ ChatApp: Ошибка отметки сообщений как прочитанных:', error);
        }
    }

    formatTime(timestamp) {
        console.log('🕰️ ChatApp: Форматируем время:', timestamp);

        const date = new Date(timestamp);
        const timeString = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        console.log('🕰️ ChatApp: Отформатированное время:', timeString);
        return timeString;
    }

    scrollToBottom() {
        console.log('⬇️ ChatApp: Пытаемся скроллить к низу...');

        const chatBody = document.querySelector('.content-area-body');


        if (!chatBody) {
            console.error('❌ ChatApp: Контейнер .content-area-body не найден!');
            return;
        }

        // Проверяем, что элемент действительно можно скроллить
        if (chatBody.scrollHeight === chatBody.clientHeight) {
            console.log('🔄 ChatApp: Чат уже прокручен до конца или нет контента для скролла');
            return;
        }

        const scrollTop = chatBody.scrollHeight - chatBody.clientHeight;
        chatBody.scrollTop = scrollTop;

        console.log(`⬇️ ChatApp: Скролл выполнен, позиция: ${scrollTop}`);
    }

    // Очистка ресурсов при уничтожении компонента
    destroy() {
        console.log('🗑️ ChatApp: Очищаем ресурсы...');

        if (this.channel) {
            this.channel.unbind_all();
            this.pusher.unsubscribe(`chat.${this.chatId}`);
            console.log('🗑️ ChatApp: Канал отписан');
        }

        if (this.pusher) {
            this.pusher.disconnect();
            console.log('🗑️ ChatApp: Pusher отключён');
        }
    }
}

// Экспорт для использования в Blade
window.ChatApp = ChatApp;


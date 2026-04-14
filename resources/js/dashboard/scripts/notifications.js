import Pusher from 'pusher-js';

class NotificationsApp {
    constructor(userId, admin=false) {
        this.userId = userId;
        this.pusher = null;
        this.channels = new Map();
        this.chatIds = new Set();
        this.admin = admin;

        console.log('🔔 NotificationsApp: Инициализация системы уведомлений', { userId });

        if (!window.pusherAppKey || !window.pusherCluster) {
            console.error('❌ NotificationsApp: Отсутствуют настройки Pusher (pusherAppKey или pusherCluster)');
            return;
        }

        this.init();
    }

    init() {
        console.log('🚀 NotificationsApp: Запуск инициализации...');

        try {
            this.pusher = new Pusher(window.pusherAppKey, {
                cluster: window.pusherCluster,
                encrypted: true,
                debug: true,
                logToConsole: true
            });

            console.log('✅ NotificationsApp: Pusher инициализирован');
            this.loadChatsAndSubscribe();
        } catch (error) {
            console.error('💥 NotificationsApp: Критическая ошибка при инициализации:', error);
        }
    }

    async loadChatsAndSubscribe() {
        try {
            const response = await fetch('/seller/chat/ajax');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const chats = await response.json();
            console.log('📥 NotificationsApp: Получен список чатов:', chats);

            // Подписываемся на все чаты
            chats.forEach(chat => {
                if (chat.id) {
                    this.subscribeToChatChannel(chat.id);
                }
            });
            if(this.admin){
                this.subscribeToChatChannel('admin');
            }
            // Запускаем периодическую проверку новых чатов (каждые 30 секунд)
            setInterval(() => this.checkForNewChats(), 30000);
        } catch (error) {
            console.error('❌ NotificationsApp: Ошибка загрузки чатов:', error);
            // Повторяем попытку через 10 секунд при ошибке
            setTimeout(() => this.loadChatsAndSubscribe(), 10000);
        }
    }

    async checkForNewChats() {
        try {
            const response = await fetch('/seller/chat/ajax?check_new=true');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const newChats = await response.json();

            newChats.forEach(chat => {
                if (chat.id && !this.chatIds.has(chat.id)) {
                    console.log('🆕 NotificationsApp: Обнаружен новый чат:', chat.id);
                    this.subscribeToChatChannel(chat.id);
                    this.chatIds.add(chat.id);
                }
            });
        } catch (error) {
            console.error('❌ NotificationsApp: Ошибка проверки новых чатов:', error);
        }
    }

    subscribeToChatChannel(chatId) {
        const channelName = `chat.${chatId}`;
        console.log(`📡 NotificationsApp: Подписываемся на канал '${channelName}'...`);

        const channel = this.pusher.subscribe(channelName);

        // Обработчики системных событий Pusher
        channel.bind('pusher:subscription_succeeded', () => {
            console.log(`✅ NotificationsApp: Успешная подписка на канал ${channelName}`);
            this.bindChatEvents(channel, chatId);
            this.chatIds.add(chatId);
        });

        channel.bind('pusher:subscription_error', (status) => {
            console.error(`❌ NotificationsApp: Ошибка подписки на канал ${channelName}:`, status);
        });

        this.channels.set(chatId, channel);
    }

    bindChatEvents(channel, chatId) {
        console.log(`🎯 NotificationsApp: Привязываем события к каналу ${chatId}...`);

        channel.bind('message.sent', (data) => {
            console.log('📩 NotificationsApp: Получено событие "message.sent"', data);

            const message = data.message;

            // Не показываем уведомления для сообщений текущего пользователя
            if (message.user.id === this.userId) {
                console.log('💬 NotificationsApp: Сообщение от текущего пользователя — пропускаем уведомление');
                return;
            }

            // this.showNotificationInUI({
            //     chat_id: chatId,
            //     user: message.user,
            //     message: message,
            //     created_at: message.created_at
            // });
            this.showToastNotification({
                chat_id: chatId,
                user: message.user,
                message: message,
                created_at: message.created_at
            });
        });
    }

    showNotificationInUI(notification) {
        const notificationsMenu = document.querySelector('.nxl-notifications-menu');
        if (!notificationsMenu) {
            console.warn('⚠️ NotificationsApp: Меню уведомлений не найдено');
            return;
        }

        const notificationItem = this.createNotificationItem(notification);
        const notificationsList = notificationsMenu.querySelector('.notifications-item')?.parentElement;

        if (notificationsList) {
            notificationsList.insertAdjacentHTML('afterbegin', notificationItem);
            this.updateNotificationCounter(1);
        }
    }

    createNotificationItem(notification) {
        const timeAgo = this.getTimeAgo(notification.created_at);
        return `
            <div class="notifications-item">
                <img src="${notification.user?.avatar || '/assets/dashboard/images/avatar/1.png'}" alt="" class="rounded me-3 border" />
                <div class="notifications-desc">
                    <a href="/seller/chat/${notification.chat_id}" class="font-body text-truncate-2-line">
                        <span class="fw-semibold text-dark">${notification.user?.name || 'Пользователь'}</span>
                        ${notification.message?.content || 'Новое сообщение'}
            </a>
            <div class="d-flex justify-content-between align-items-center">
                <div class="notifications-date text-muted border-bottom border-bottom-dashed">${timeAgo}</div>
                <div class="d-flex align-items-center float-end gap-2">
                    <a href="javascript:void(0);" class="d-block wd-8 ht-8 rounded-circle bg-gray-300 mark-as-read" data-notification-id="${notification.message?.id || 'unknown'}" data-bs-toggle="tooltip" title="Отметить как прочитанное"></a>
            <a href="javascript:void(0);" class="text-danger delete-notification" data-notification-id="${notification.message?.id || 'unknown'}" data-bs-toggle="tooltip" title="Удалить">
                <i class="feather-x fs-12"></i>
            </a>
                </div>
            </div>
        </div>
    </div>`;
    }

    showToastNotification(notification) {
        // Проверяем, существует ли уже контейнер для тостов
        let toastContainer = document.getElementById('toastContainer');

        if (!toastContainer) {
            // Создаём контейнер для тостов, если его нет
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        // Генерируем уникальный ID для тоста
        const toastId = `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

        // Создаём элемент тоста с правильной вёрсткой Bootstrap
        const toastElement = document.createElement('div');
        toastElement.id = toastId;
        toastElement.className = 'toast';
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');

        // Форматируем время
        const timeAgo = this.getTimeAgo(notification.created_at);

        // Заполняем содержимое тоста
        toastElement.innerHTML = `
         <a href="/seller/chat/${notification.chat_id}">
          <div class="toast-header">
              <img src="${notification.user?.avatar || '/assets/dashboard/images/avatar/1.png'}"
                   class="rounded me-2"
                   alt="${notification.user?.name || 'Пользователь'}"
                   width="20"
                   height="20">
              <strong class="me-auto">${notification.user?.name || 'Новый чат'}</strong>
              <small>${timeAgo}</small>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
          </div>
          <div class="toast-body">
              ${notification.message?.content || 'Новое сообщение'}
          </div>
         </a>
        `;
        if(notification.support){
            toastElement.innerHTML = `
         <a href="/seller/chat/${notification.chat_id}">
          <div class="toast-header">
              <img src="/assets/dashboard/images/avatar/1.png"
                   class="rounded me-2"
                   alt="!"
                   width="20"
                   height="20">
              <strong class="me-auto">Новый чат с поддержкой</strong>
              <small>${timeAgo}</small>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
          </div>
          <div class="toast-body">
              Инициирован новый чат с поддержкой!
          </div>
         </a>
        `;
        }

        // Добавляем тост в контейнер
        toastContainer.appendChild(toastElement);

        // Инициализируем тост через Bootstrap
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastElement, {
            autohide: true,
            delay: 5000
        });

        // Показываем тост
        toastBootstrap.show();

        // Удаляем элемент из DOM после скрытия
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();

            // Если в контейнере не осталось тостов, удаляем сам контейнер
            if (toastContainer.children.length === 0) {
                toastContainer.remove();
            }
        });
    }


    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.position = 'fixed';
        container        .style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '1060';
        document.body.appendChild(container);
        return container;
    }


    updateNotificationCounter(change = 1) {
        const badge = document.querySelector('.nxl-h-badge');
        if (badge) {
            const currentCount = parseInt(badge.textContent) || 0;
            const newCount = Math.max(0, currentCount + change);
            badge.textContent = newCount;
            badge.style.display = newCount > 0 ? 'block' : 'none';
        }
    }

    getTimeAgo(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return 'Только что';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} минут назад`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} часов назад`;
        return date.toLocaleDateString();
    }

    destroy() {
        console.log('🗑️ NotificationsApp: Очищаем ресурсы...');

        this.channels.forEach((channel, chatId) => {
            channel.unbind_all();
            console.log(`🗑️ NotificationsApp: Канал ${chatId} отписан`);
        });

        if (this.pusher) {
            this.pusher.disconnect();
            console.log('🗑️ NotificationsApp: Pusher отключён');
        }

        // Очищаем интервалы
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }
    }

    // Метод для динамической подписки на новый чат
    subscribeToNewChat(chatId) {
        if (!this.channels.has(chatId)) {
            this.subscribeToChatChannel(chatId);
        } else {
            console.log(`⚠️ NotificationsApp: Уже подписаны на канал чата ${chatId}`);
        }
    }

    // Метод для отписки от чата
    unsubscribeFromChat(chatId) {
        const channel = this.channels.get(chatId);
        if (channel) {
            channel.unbind_all();
            this.pusher.unsubscribe(`chat.${chatId}`);
            this.channels.delete(chatId);
            this.chatIds.delete(chatId);
            console.log(`🗑️ NotificationsApp: Отписались от канала чата ${chatId}`);
        }
    }
}

function debugNotificationsInit() {
    console.log('🔎 NotificationsApp: Диагностика инициализации');
    console.log('Pusher доступен:', typeof Pusher !== 'undefined');
    console.log('window.pusherAppKey:', window.pusherAppKey);
    console.log('window.pusherCluster:', window.pusherCluster);
    console.log('userId предоставлен:', !!window.currentUserId);
}

// Экспорт для использования в Blade
window.NotificationsApp = NotificationsApp;

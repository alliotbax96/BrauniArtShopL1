import Pusher from 'pusher-js';

class NotificationsApp {
    constructor(userId, admin = false) {
        this.userId = userId;
        this.pusher = null;
        this.channels = new Map();
        this.chatIds = new Set();
        this.admin = admin;
        this.checkInterval = null;
        this.newChatsCallback = null;

        if (!window.pusherAppKey || !window.pusherCluster) {
            console.error('❌ NotificationsApp: Отсутствуют настройки Pusher');
            return;
        }

        this.init();
    }

    init() {
        try {
            this.pusher = new Pusher(window.pusherAppKey, {
                cluster: window.pusherCluster,
                encrypted: true,
                debug: false,
                logToConsole: false
            });

            console.log('✅ NotificationsApp: Pusher инициализирован');

            this.pusher.connection.bind('connected', () => {
                console.log('✅ NotificationsApp: Pusher подключен');
                this.loadChatsAndSubscribe();
            });

            if (this.pusher.connection.state === 'connected') {
                this.loadChatsAndSubscribe();
            }
        } catch (error) {
            console.error('💥 NotificationsApp: Ошибка инициализации:', error);
        }
    }

    onNewChat(callback) {
        this.newChatsCallback = callback;
    }

    async loadChatsAndSubscribe() {
        try {
            const response = await fetch('/seller/chat/ajax');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const chats = await response.json();

            if (Array.isArray(chats)) {
                chats.forEach(chat => {
                    if (chat.id) {
                        this.subscribeToChatChannel(chat.id);
                    }
                });
            }

            if (this.admin) {
                this.subscribeToAdminChannel();
            }

            if (this.checkInterval) {
                clearInterval(this.checkInterval);
            }
            this.checkInterval = setInterval(() => this.checkForNewChats(), 30000);
        } catch (error) {
            console.error('❌ NotificationsApp: Ошибка загрузки чатов:', error);
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

            if (Array.isArray(newChats)) {
                newChats.forEach(chat => {
                    if (chat.id && !this.chatIds.has(chat.id)) {
                        console.log('🆕 NotificationsApp: Новый чат:', chat);
                        this.subscribeToChatChannel(chat.id);
                        this.chatIds.add(chat.id);
                    }
                });
            }
        } catch (error) {
            console.error('❌ NotificationsApp: Ошибка проверки новых чатов:', error);
        }
    }

    subscribeToAdminChannel() {
        try {
            if (this.channels.has('admin')) {
                const oldChannel = this.channels.get('admin');
                oldChannel.unbind_all();
                this.pusher.unsubscribe('admin.chats');
            }

            const adminChannel = this.pusher.subscribe('admin.chats');

            adminChannel.bind('pusher:subscription_succeeded', () => {
                console.log('✅ NotificationsApp: Подписка на админский канал');
            });

            adminChannel.bind('chat.created', (data) => {
                console.log('🆕 NotificationsApp: Новый чат покупателя', data);

                if (data.chat && data.chat.id) {
                    this.subscribeToChatChannel(data.chat.id);
                    this.chatIds.add(data.chat.id);

                    this.showToastNotification({
                        chat_id: data.chat.id,
                        user: {
                            id: 0,
                            name: data.chat.client_name || 'Покупатель',
                            avatar: '/assets/dashboard/images/avatar/2.png'
                        },
                        message: {
                            id: null,
                            content: 'Новое обращение в поддержку'
                        },
                        created_at: data.chat.created_at,
                        support: true
                    });

                    if (this.newChatsCallback) {
                        this.newChatsCallback(data.chat);
                    }
                }
            });

            adminChannel.bind('pusher:subscription_error', (error) => {
                console.error('❌ NotificationsApp: Ошибка подписки на админский канал:', error);
            });

            this.channels.set('admin', adminChannel);
        } catch (error) {
            console.error('❌ NotificationsApp: Ошибка подписки на админский канал:', error);
        }
    }

    subscribeToChatChannel(chatId) {
        if (this.channels.has(chatId) || this.chatIds.has(chatId)) {
            return;
        }

        try {
            const channelName = `chat.${chatId}`;
            const channel = this.pusher.subscribe(channelName);

            channel.bind('pusher:subscription_succeeded', () => {
                console.log('✅ NotificationsApp: Подписка на чат', chatId);
                this.bindChatEvents(channel, chatId);
                this.chatIds.add(chatId);
            });

            channel.bind('pusher:subscription_error', (status) => {
                console.error(`❌ NotificationsApp: Ошибка подписки на чат ${chatId}:`, status);
            });

            this.channels.set(chatId, channel);
        } catch (error) {
            console.error(`❌ NotificationsApp: Ошибка подписки на чат ${chatId}:`, error);
        }
    }

    bindChatEvents(channel, chatId) {
        channel.bind('message.sent', (data) => {
            const message = data.message;
            if (!message) return;

            const messageUserId = parseInt(message.user_id);
            if (messageUserId === this.userId) return;

            this.showToastNotification({
                chat_id: chatId,
                user: message.user || {
                    id: messageUserId,
                    name: 'Пользователь',
                    avatar: '/assets/dashboard/images/avatar/1.png'
                },
                message: message,
                created_at: message.created_at
            });
        });
    }

    showToastNotification(notification) {
        let toastContainer = document.getElementById('toastContainer');

        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        const toastId = `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        const timeAgo = this.getTimeAgo(notification.created_at);

        const toastElement = document.createElement('div');
        toastElement.id = toastId;
        toastElement.className = 'toast';
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');

        const userName = notification.user?.name || 'Пользователь';
        const userAvatar = notification.user?.avatar || '/assets/dashboard/images/avatar/1.png';
        const messageContent = notification.message?.content || 'Новое сообщение';

        if (notification.support) {
            toastElement.innerHTML = `
                <a href="/seller/chat/${notification.chat_id}" class="text-decoration-none">
                    <div class="toast-header">
                        <img src="${userAvatar}" class="rounded me-2" alt="${userName}" width="20" height="20">
                        <strong class="me-auto">Новое обращение</strong>
                        <small>${timeAgo}</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
                    </div>
                    <div class="toast-body">
                        ${userName} запрашивает поддержку
                    </div>
                </a>
            `;
        } else {
            toastElement.innerHTML = `
                <a href="/seller/chat/${notification.chat_id}" class="text-decoration-none">
                    <div class="toast-header">
                        <img src="${userAvatar}" class="rounded me-2" alt="${userName}" width="20" height="20">
                        <strong class="me-auto">${userName}</strong>
                        <small>${timeAgo}</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
                    </div>
                    <div class="toast-body">
                        ${messageContent}
                    </div>
                </a>
            `;
        }

        toastContainer.appendChild(toastElement);

        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastElement, {
            autohide: true,
            delay: 5000
        });

        toastBootstrap.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
            if (toastContainer.children.length === 0) {
                toastContainer.remove();
            }
        });
    }

    getTimeAgo(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return 'Только что';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} мин. назад`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} ч. назад`;
        return date.toLocaleDateString();
    }

    destroy() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }

        this.channels.forEach((channel, chatId) => {
            channel.unbind_all();
            if (chatId !== 'admin') {
                this.pusher.unsubscribe(`chat.${chatId}`);
            }
        });

        if (this.pusher) {
            this.pusher.disconnect();
        }
    }

    subscribeToNewChat(chatId) {
        if (!this.channels.has(chatId)) {
            this.subscribeToChatChannel(chatId);
        }
    }

    unsubscribeFromChat(chatId) {
        const channel = this.channels.get(chatId);
        if (channel) {
            channel.unbind_all();
            this.pusher.unsubscribe(`chat.${chatId}`);
            this.channels.delete(chatId);
            this.chatIds.delete(chatId);
        }
    }
}

window.NotificationsApp = NotificationsApp;

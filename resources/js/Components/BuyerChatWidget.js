import Pusher from 'pusher-js';

class BuyerChatWidget {
    constructor() {
        this.chatId = null;
        this.guestToken = null;
        this.isOpen = false;
        this.isInitialized = false;
        this.pusher = null;
        this.channel = null;
        this.unreadCount = 0;
        this.isMobile = window.innerWidth < 480;

        // Ждем загрузки DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    init() {
        // Создаем HTML виджета
        this.createWidgetHTML();

        // Загружаем стили
        this.injectStyles();

        // Привязываем события
        this.bindEvents();

        // Инициализируем Pusher
        this.initPusher();

        // Проверяем, есть ли сохраненный chatId в localStorage
        this.loadSavedChat();

        this.isInitialized = true;
        console.log('BuyerChatWidget initialized');
    }

    createWidgetHTML() {
        const widgetHTML = `
        <div id="buyer-chat-widget" class="buyer-chat-widget">
            <!-- Кнопка открытия чата -->
            <div id="chat-button" class="chat-button">
                <div class="chat-button-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <span id="unread-badge" class="unread-badge" style="display: none;">0</span>
            </div>

            <!-- Окно чата -->
            <div id="chat-window" class="chat-window" style="display: none;">
                <!-- Заголовок -->
                <div class="chat-header">
                    <div class="chat-header-info">
                        <div class="support-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none">
                                <circle cx="21" cy="21" r="21" fill="url(#gradient)"/>
                                <circle cx="21" cy="16" r="8" fill="white" opacity="0.9"/>
                                <path d="M5 35 Q21 42 37 35" fill="white" opacity="0.9"/>
                                <defs>
                                    <linearGradient id="gradient" x1="0" y1="0" x2="42" y2="42">
                                        <stop offset="0%" stop-color="#667eea"/>
                                        <stop offset="100%" stop-color="#764ba2"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>
                        <div>
                            <h3>Поддержка</h3>
                            <p id="support-status">Мы онлайн</p>
                        </div>
                    </div>
                    <div class="chat-header-actions">
                        <button id="minimize-chat" class="header-btn" title="Свернуть">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </button>
                        <button id="close-chat" class="header-btn" title="Закрыть">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Форма регистрации -->
                <div id="registration-form" class="registration-form">
                    <div class="registration-content">
                        <div class="welcome-icon" style="text-align: center; margin-bottom: 20px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                <circle cx="9" cy="11" r="1"></circle>
                                <circle cx="15" cy="11" r="1"></circle>
                                <path d="M9 15s2 2 4 2 4-2 4-2"></path>
                            </svg>
                        </div>
                        <h4>👋 Здравствуйте!</h4>
                        <p>Представьтесь, пожалуйста, чтобы начать чат</p>
                        <div class="form-group">
                            <input type="text" id="client-name" placeholder="Ваше имя *" required>
                        </div>
                        <div class="form-group">
                            <input type="email" id="client-email" placeholder="Email (необязательно)">
                        </div>
                        <div class="form-group">
                            <input type="tel" id="client-phone" placeholder="Телефон (необязательно)">
                        </div>
                        <button id="start-chat-btn" class="start-chat-btn">
                            <span>Начать чат</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Область сообщений -->
                <div id="messages-container" class="messages-container" style="display: none;">
                    <div id="messages-list" class="messages-list">
                        <div class="messages-welcome">
                            <div class="welcome-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="1.5">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </div>
                            <p>Опишите ваш вопрос, и мы поможем вам</p>
                        </div>
                    </div>
                    <div id="typing-indicator" class="typing-indicator" style="display: none;">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>

                <!-- Поле ввода -->
                <div id="input-container" class="input-container" style="display: none;">
                    <div class="input-wrapper">
                        <textarea id="message-input"
                                  placeholder="Введите сообщение..."
                                  rows="1"
                                  maxlength="5000"></textarea>
                        <button id="send-message-btn" class="send-btn" title="Отправить">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        document.body.insertAdjacentHTML('beforeend', widgetHTML);
    }

    injectStyles() {
        const styles = `
        <style>
            /* Основной контейнер */
            .buyer-chat-widget {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 999999;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            }

            /* Кнопка чата */
            .chat-button {
                display: flex;
                align-items: center;
                gap: 10px;
                background: linear-gradient(135deg, #0465d2 0%, #3b8cff 100%);
                color: white;
                padding: 14px 24px;
                border-radius: 50px;
                cursor: pointer;
                box-shadow: 0 4px 15px rgba(4, 101, 210, 0.4);
                transition: all 0.3s ease;
                user-select: none;
            }

            .chat-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(4, 101, 210, 0.6);
            }

            .chat-button:active {
                transform: translateY(0);
            }

            .chat-button-icon {
                display: flex;
                align-items: center;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }

            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }

            .chat-button-text {
                font-weight: 500;
                font-size: 14px;
            }

            .unread-badge {
                background: #ff4757;
                color: white;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: bold;
                min-width: 20px;
                text-align: center;
            }

            /* Окно чата */
            .chat-window {
                position: absolute;
                bottom: 80px;
                right: 0;
                width: 380px;
                height: 600px;
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                display: flex;
                flex-direction: column;
                overflow: hidden;
                animation: slideUp 0.3s ease;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Заголовок */
            .chat-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 20px;
                background: linear-gradient(135deg, #0465d2 0%, #3b8cff 100%);
                color: white;
            }

            .chat-header-info {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .support-avatar {
                width: 42px;
                height: 42px;
                border-radius: 50%;
                overflow: hidden;
                border: 2px solid rgba(255, 255, 255, 0.5);
            }

            .support-avatar svg {
                width: 100%;
                height: 100%;
                display: block;
            }

            .chat-header h3 {
                margin: 0;
                font-size: 16px;
                font-weight: 600;
            }

            .chat-header p {
                margin: 0;
                font-size: 12px;
                opacity: 1;
            }

            /* Статусы поддержки */
            #support-status {
                transition: color 0.3s ease;
            }

            #support-status.status-waiting {
                color: #FFD700 !important;
                font-weight: 500;
            }

            #support-status.status-active {
                color: #4caf50 !important;
                font-weight: 500;
            }

            #support-status.status-closed {
                color: #ccc !important;
                font-weight: 500;
            }

            #support-status.status-online {
                color: rgba(255, 255, 255, 0.9) !important;
            }

            .chat-header-actions {
                display: flex;
                gap: 4px;
            }

            .header-btn {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                cursor: pointer;
                padding: 6px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.2s;
            }

            .header-btn:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            /* Форма регистрации */
            .registration-form {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
            }

            .registration-content {
                width: 100%;
                max-width: 300px;
                text-align: center;
            }

            .registration-content h4 {
                margin-bottom: 8px;
                color: #333;
                font-size: 20px;
            }

            .registration-content p {
                color: #666;
                margin-bottom: 20px;
                font-size: 14px;
            }

            .form-group {
                margin-bottom: 12px;
            }

            .form-group input {
                width: 100%;
                padding: 12px 16px;
                border: 2px solid #e0e0e0;
                border-radius: 12px;
                font-size: 14px;
                transition: border-color 0.2s;
                outline: none;
            }

            .form-group input:focus {
                border-color: #0465d2;
            }

            .start-chat-btn {
                width: 100%;
                padding: 12px 24px;
                background: linear-gradient(135deg, #0465d2 0%, #3b8cff 100%);
                color: white;
                border: none;
                border-radius: 12px;
                cursor: pointer;
                font-size: 15px;
                font-weight: 500;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                transition: all 0.2s;
                margin-top: 8px;
            }

            .start-chat-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(4, 101, 210, 0.4);
            }

            .start-chat-btn:active {
                transform: translateY(0);
            }

            .start-chat-btn:disabled {
                opacity: 0.7;
                cursor: not-allowed;
                transform: none;
            }

            /* Контейнер сообщений */
            .messages-container {
                flex: 1;
                overflow-y: auto;
                padding: 20px;
                background: #f8f9fa;
            }

            .messages-list {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .messages-welcome {
                text-align: center;
                padding: 40px 20px;
                color: #999;
            }

            .welcome-icon {
                margin-bottom: 16px;
                opacity: 0.5;
            }

            .message {
                display: flex;
                flex-direction: column;
                max-width: 85%;
                animation: messageSlide 0.3s ease;
            }

            @keyframes messageSlide {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .message.client {
                align-self: flex-end;
            }

            .message.support {
                align-self: flex-start;
            }

            .message-bubble {
                padding: 10px 16px;
                border-radius: 16px;
                font-size: 14px;
                line-height: 1.5;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            .message.client .message-bubble {
                background: linear-gradient(135deg, #0465d2 0%, #3b8cff 100%);
                color: white;
                border-bottom-right-radius: 4px;
            }

            .message.support .message-bubble {
                background: white;
                color: #333;
                border-bottom-left-radius: 4px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .message-time {
                font-size: 11px;
                opacity: 0.7;
                margin-top: 4px;
                padding: 0 8px;
            }

            .message.client .message-time {
                text-align: right;
            }

            /* Индикатор печати */
            .typing-indicator {
                display: flex;
                gap: 4px;
                padding: 8px 16px;
                background: white;
                border-radius: 16px;
                width: fit-content;
                margin-top: 8px;
            }

            .typing-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #999;
                animation: typing 1.4s infinite;
            }

            .typing-dot:nth-child(2) {
                animation-delay: 0.2s;
            }

            .typing-dot:nth-child(3) {
                animation-delay: 0.4s;
            }

            @keyframes typing {
                0%, 60%, 100% { opacity: 0.3; }
                30% { opacity: 1; }
            }

            /* Поле ввода */
            .input-container {
                padding: 12px 16px;
                background: white;
                border-top: 1px solid #eee;
            }

            .input-wrapper {
                display: flex;
                align-items: flex-end;
                gap: 8px;
                background: #f5f5f5;
                border-radius: 24px;
                padding: 4px;
            }

            .input-wrapper textarea {
                flex: 1;
                padding: 8px 12px;
                border: none;
                background: transparent;
                border-radius: 20px;
                resize: none;
                font-size: 14px;
                font-family: inherit;
                line-height: 1.4;
                outline: none;
                max-height: 100px;
            }

            .send-btn {
                background: linear-gradient(135deg, #0465d2 0%, #3b8cff 100%);
                color: white;
                border: none;
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
            }

            .send-btn:hover {
                transform: scale(1.1);
            }

            .send-btn:active {
                transform: scale(0.95);
            }

            .send-btn:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                transform: none;
            }

            /* Мобильная версия */
            @media (max-width: 480px) {
                .chat-window {
                    width: 100vw;
                    height: 100vh;
                    height: 100dvh;
                    bottom: 0;
                    right: 0;
                    position: fixed;
                    border-radius: 0;
                }

                .chat-button-text {
                    display: none;
                }

                .chat-button {
                    padding: 14px;
                }

                .message {
                    max-width: 90%;
                }
            }
        </style>
    `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    bindEvents() {
        // Кнопка открытия/закрытия
        document.getElementById('chat-button').addEventListener('click', () => this.toggleChat());

        // Кнопки в заголовке
        document.getElementById('close-chat')?.addEventListener('click', () => this.closeChat());
        document.getElementById('minimize-chat')?.addEventListener('click', () => this.toggleChat());

        // Старт чата
        document.getElementById('start-chat-btn')?.addEventListener('click', () => this.startChat());

        // Enter в поле имени
        document.getElementById('client-name')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.startChat();
        });

        // Отправка сообщения
        document.getElementById('send-message-btn')?.addEventListener('click', () => this.sendMessage());

        const messageInput = document.getElementById('message-input');
        if (messageInput) {
            messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            // Автоматическое изменение высоты
            messageInput.addEventListener('input', () => {
                messageInput.style.height = 'auto';
                messageInput.style.height = Math.min(messageInput.scrollHeight, 100) + 'px';
            });
        }

        // Закрытие по Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeChat();
            }
        });

        // Обработка изменения размера окна
        window.addEventListener('resize', () => {
            this.isMobile = window.innerWidth < 480;
        });
    }

    initPusher() {
        // Проверяем наличие глобальных переменных Pusher
        const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY || window.PUSHER_APP_KEY;
        const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || window.PUSHER_APP_CLUSTER;

        if (!pusherKey || !pusherCluster) {
            console.warn('Pusher credentials not found');
            return;
        }

        try {
            this.pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                encrypted: true,
                enabledTransports: ['ws', 'wss'],
                forceTLS: true
            });

            this.pusher.connection.bind('connected', () => {
                console.log('✅ Pusher connected:', this.pusher.connection.socket_id);
            });

            this.pusher.connection.bind('error', (err) => {
                console.error('Pusher connection error:', err);
            });
        } catch (error) {
            console.error('Failed to initialize Pusher:', error);
        }
    }

    loadSavedChat() {
        // Проверяем сохраненные данные в localStorage
        const savedChatId = localStorage.getItem('buyer_chat_id');
        const savedGuestToken = localStorage.getItem('buyer_guest_token');

        if (savedGuestToken) {
            this.guestToken = savedGuestToken;
        }

        if (savedChatId) {
            this.chatId = savedChatId;
            // Если чат был активен, показываем окно сообщений
            document.getElementById('registration-form').style.display = 'none';
            document.getElementById('messages-container').style.display = 'block';
            document.getElementById('input-container').style.display = 'block';

            this.subscribeToChannel();
            this.loadMessages();
        }
    }

    async startChat() {
        const name = document.getElementById('client-name').value.trim();
        if (!name) {
            this.shakeElement(document.getElementById('client-name'));
            return;
        }

        const startBtn = document.getElementById('start-chat-btn');
        startBtn.disabled = true;
        startBtn.innerHTML = '<span>Подключение...</span>';

        try {
            const response = await fetch('/api/buyer/chat/init', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    client_name: name,
                    client_email: document.getElementById('client-email').value.trim(),
                    client_phone: document.getElementById('client-phone').value.trim(),
                    current_page: window.location.href,
                    guest_token: this.guestToken
                })
            });

            const data = await response.json();

            if (data.success) {
                this.chatId = data.chat_id;
                this.guestToken = data.guest_token;

                // Сохраняем в localStorage
                localStorage.setItem('buyer_chat_id', data.chat_id);
                if (data.guest_token) {
                    localStorage.setItem('buyer_guest_token', data.guest_token);
                }

                // Показываем окно сообщений
                document.getElementById('registration-form').style.display = 'none';
                document.getElementById('messages-container').style.display = 'block';
                document.getElementById('input-container').style.display = 'block';

                // Подписываемся на канал
                this.subscribeToChannel();

                // Загружаем сообщения
                if (data.messages && data.messages.length > 0) {
                    const messagesList = document.getElementById('messages-list');
                    messagesList.innerHTML = '';
                    data.messages.forEach(msg => {
                        const type = this.isOwnMessage(msg) ? 'client' : 'support';
                        this.addMessage(msg, type);
                    });
                }

                // Обновляем статус
                this.updateSupportStatus(data.status);
            } else {
                alert(data.message || 'Не удалось начать чат');
            }
        } catch (error) {
            console.error('Error starting chat:', error);
            alert('Произошла ошибка. Пожалуйста, попробуйте позже.');
        } finally {
            startBtn.disabled = false;
            startBtn.innerHTML = '<span>Начать чат</span><svg>...</svg>';
        }
    }

    subscribeToChannel() {
        if (!this.pusher || !this.chatId) return;

        // Отписываемся от старого канала если есть
        if (this.channel) {
            this.channel.unbind_all();
            this.pusher.unsubscribe(`chat.${this.chatId}`);
        }

        this.channel = this.pusher.subscribe(`chat.${this.chatId}`);

        this.channel.bind('message.sent', (data) => {
            const message = data.message;

            // Проверяем, не свое ли это сообщение
            if (!this.isOwnMessage(message)) {
                this.addMessage(message, 'support');

                if (!this.isOpen) {
                    this.unreadCount++;
                    this.updateUnreadBadge();
                }

                // Обновляем статус
                this.updateSupportStatus('active');
            }
        });

        this.channel.bind('pusher:subscription_succeeded', () => {
            console.log('✅ Subscribed to channel: chat.' + this.chatId);
        });

        this.channel.bind('pusher:subscription_error', (error) => {
            console.error('❌ Subscription error:', error);
        });
    }

    async loadMessages() {
        if (!this.chatId) return;

        try {
            const url = new URL(`/api/buyer/chat/${this.chatId}/messages`, window.location.origin);
            if (this.guestToken) {
                url.searchParams.append('guest_token', this.guestToken);
            }

            const response = await fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                const messagesList = document.getElementById('messages-list');
                messagesList.innerHTML = '';

                if (data.messages.length === 0) {
                    messagesList.innerHTML = `
                        <div class="messages-welcome">
                            <div class="welcome-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="1.5">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </div>
                            <p>Опишите ваш вопрос, и мы поможем вам</p>
                        </div>
                    `;
                } else {
                    data.messages.forEach(msg => {
                        const type = this.isOwnMessage(msg) ? 'client' : 'support';
                        this.addMessage(msg, type);
                    });
                }

                this.updateSupportStatus(data.status);
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    addMessage(message, type) {
        const messagesList = document.getElementById('messages-list');

        // Удаляем welcome сообщение если есть
        const welcomeMsg = messagesList.querySelector('.messages-welcome');
        if (welcomeMsg) welcomeMsg.remove();

        // Проверяем, нет ли уже такого сообщения
        if (messagesList.querySelector(`[data-message-id="${message.id}"]`)) {
            return;
        }

        const messageHTML = `
            <div class="message ${type}" data-message-id="${message.id}">
                <div class="message-bubble">
                    ${this.escapeHtml(message.content)}
                </div>
                <div class="message-time">
                    ${this.formatTime(message.created_at)}
                </div>
            </div>
        `;

        messagesList.insertAdjacentHTML('beforeend', messageHTML);
        this.scrollToBottom();
    }

    async sendMessage() {
        const input = document.getElementById('message-input');
        const content = input.value.trim();

        if (!content || !this.chatId) return;

        const sendBtn = document.getElementById('send-message-btn');
        sendBtn.disabled = true;

        try {
            const response = await fetch(`/api/buyer/chat/${this.chatId}/message`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    content: content,
                    guest_token: this.guestToken
                })
            });

            const data = await response.json();

            if (data.success) {
                this.addMessage(data.message, 'client');
                input.value = '';
                input.style.height = 'auto';
            } else {
                console.error('Error sending message:', data);
                if (data.message) alert(data.message);
            }
        } catch (error) {
            console.error('Error sending message:', error);
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    }

    toggleChat() {
        const chatWindow = document.getElementById('chat-window');
        this.isOpen = !this.isOpen;

        if (this.isOpen) {
            chatWindow.style.display = 'flex';
            this.unreadCount = 0;
            this.updateUnreadBadge();

            if (this.chatId) {
                this.loadMessages();
            }

            // Фокус на поле ввода
            setTimeout(() => {
                const input = document.getElementById('message-input');
                if (input && input.offsetParent !== null) {
                    input.focus();
                }
            }, 300);
        } else {
            chatWindow.style.display = 'none';
        }
    }

    closeChat() {
        this.isOpen = false;
        document.getElementById('chat-window').style.display = 'none';
    }

    isOwnMessage(message) {
        // Если сообщение от авторизованного пользователя
        if (message.user_id > 0 && window.USER_ID && message.user_id === window.USER_ID) {
            return true;
        }

        // Если сообщение от гостя с токеном
        if (message.user_id === 0 && this.guestToken && message.guest_token === this.guestToken) {
            return true;
        }

        return false;
    }

    updateSupportStatus(status) {
        const statusElement = document.getElementById('support-status');
        if (!statusElement) return;

        // Удаляем все предыдущие классы статуса
        statusElement.classList.remove('status-waiting', 'status-active', 'status-closed', 'status-online');

        switch(status) {
            case 'waiting':
                statusElement.textContent = 'Ожидайте оператора';
                statusElement.classList.add('status-waiting');
                break;
            case 'active':
                statusElement.textContent = 'Оператор онлайн';
                statusElement.classList.add('status-active');
                break;
            case 'closed':
                statusElement.textContent = 'Чат закрыт';
                statusElement.classList.add('status-closed');
                break;
            default:
                statusElement.textContent = 'Мы онлайн';
                statusElement.classList.add('status-online');
        }
    }

    updateUnreadBadge() {
        const badge = document.getElementById('unread-badge');
        if (!badge) return;

        if (this.unreadCount > 0) {
            badge.style.display = 'inline-block';
            badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
        } else {
            badge.style.display = 'none';
        }
    }

    scrollToBottom() {
        const container = document.getElementById('messages-container');
        if (!container) return;

        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 100);
    }

    shakeElement(element) {
        element.style.animation = 'none';
        element.offsetHeight; // Trigger reflow
        element.style.animation = 'shake 0.5s ease';
        element.style.borderColor = '#ff4757';

        setTimeout(() => {
            element.style.borderColor = '#e0e0e0';
        }, 2000);
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;

        // Если сегодня
        if (diff < 86400000 && date.getDate() === now.getDate()) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        // Если вчера
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        if (date.getDate() === yesterday.getDate()) {
            return 'Вчера ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        // Если в этом году
        if (date.getFullYear() === now.getFullYear()) {
            return date.toLocaleDateString([], { day: 'numeric', month: 'short' }) +
                ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        return date.toLocaleDateString([], { day: 'numeric', month: 'short', year: 'numeric' });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }
}

// Экспорт для использования
export default BuyerChatWidget;

// Автоматическая инициализация если нужно
if (document.querySelector('[data-chat-widget]')) {
    new BuyerChatWidget();
}

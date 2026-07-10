import Pusher from 'pusher-js';

class ChatApp {
    constructor(chatId, userId) {
        this.chatId = parseInt(chatId);
        this.userId = parseInt(userId);
        this.pusher = null;
        this.channel = null;
        this.adminChannel = null;
        this.editingMessageId = null;

        console.log('🔔 ChatApp: Инициализация чата', {
            chatId: this.chatId,
            userId: this.userId,
            pusherKey: window.pusherAppKey ? 'present' : 'missing',
            pusherCluster: window.pusherCluster ? 'present' : 'missing'
        });

        if (!window.pusherAppKey || !window.pusherCluster) {
            console.error('❌ ChatApp: Отсутствуют настройки Pusher');
            return;
        }

        this.init();
    }

    init() {
        console.log('🚀 ChatApp: Запуск инициализации...');

        try {
            // Инициализация Pusher
            this.pusher = new Pusher(window.pusherAppKey, {
                cluster: window.pusherCluster,
                encrypted: true,
                enabledTransports: ['ws', 'wss'],
                forceTLS: true,
                debug: false,
                logToConsole: false
            });

            // Логирование состояния подключения
            this.pusher.connection.bind('connected', () => {
                console.log('✅ Pusher подключен, socket_id:', this.pusher.connection.socket_id);
            });

            this.pusher.connection.bind('connecting', () => {
                console.log('🔄 Pusher подключается...');
            });

            this.pusher.connection.bind('disconnected', () => {
                console.log('❌ Pusher отключен');
            });

            this.pusher.connection.bind('error', (err) => {
                console.error('💥 Pusher ошибка подключения:', err);
            });

            // Подписка на канал чата
            const channelName = `chat.${this.chatId}`;
            console.log(`📡 ChatApp: Подписываемся на канал '${channelName}'...`);

            this.channel = this.pusher.subscribe(channelName);

            // Подписка на админский канал для новых чатов
            if (window.USER_IS_ADMIN) {
                console.log('📡 ChatApp: Подписываемся на админский канал...');
                this.adminChannel = this.pusher.subscribe('admin.chats');
                this.bindAdminEvents();
            }

            // Привязываем события
            this.bindEvents();

            // Обработчики успешной подписки
            this.channel.bind('pusher:subscription_succeeded', () => {
                console.log('✅ ChatApp: Успешная подписка на канал', channelName);
                this.setupMessageSending();
                this.setupFileUploads();
                this.setupContextMenu();
            });

            this.channel.bind('pusher:subscription_error', (status) => {
                console.error('❌ ChatApp: Ошибка подписки на канал:', status);
            });

            // Отмечаем сообщения как прочитанные
            this.markMessagesAsRead();

            // Скроллим вниз
            this.scrollToBottom();

            console.log('🎉 ChatApp: Инициализация завершена');
        } catch (error) {
            console.error('💥 ChatApp: Ошибка при инициализации:', error);
        }
    }

    bindAdminEvents() {
        if (!this.adminChannel) return;

        this.adminChannel.bind('chat.created', (data) => {
            console.log('🆕 ChatApp: Получен новый чат покупателя', data);
            this.handleNewChat(data.chat);
        });

        this.adminChannel.bind('pusher:subscription_succeeded', () => {
            console.log('✅ ChatApp: Успешная подписка на админский канал');
        });
    }

    handleNewChat(chat) {
        // Показываем уведомление
        this.showNewChatNotification(chat);

        // Добавляем чат в список
        this.addChatToList(chat);

        // Обновляем счетчики если есть
        this.updateChatCounters();
    }

    showNewChatNotification(chat) {
        const chatName = chat.client_name || 'Покупатель';

        // Браузерное уведомление
        if (Notification.permission === 'granted') {
            new Notification('Новое обращение', {
                body: `${chatName} ожидает поддержку`,
                icon: '/assets/dashboard/images/avatar/buyer.png',
                tag: 'new-chat'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification('Новое обращение', {
                        body: `${chatName} ожидает поддержку`,
                        icon: '/assets/dashboard/images/avatar/buyer.png',
                        tag: 'new-chat'
                    });
                }
            });
        }

        // Звуковое уведомление
        this.playNewChatSound();

        // Визуальное уведомление на странице
        this.showToastNotification(chat);
    }

    showToastNotification(chat) {
        const chatName = chat.client_name || 'Покупатель';

        // Создаем toast уведомление
        const toast = document.createElement('div');
        toast.className = 'new-chat-toast';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            animation: slideInRight 0.3s ease;
            cursor: pointer;
        `;

        toast.innerHTML = `
            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </div>
            <div style="flex: 1;">
                <div style="font-weight: 600; color: #333;">Новое обращение</div>
                <div style="font-size: 14px; color: #666;">${chatName} ожидает поддержку</div>
            </div>
            <button style="background: none; border: none; color: #999; cursor: pointer; padding: 4px;" onclick="this.parentElement.remove()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;

        toast.addEventListener('click', () => {
            window.location.href = `/seller/chat/${chat.id}`;
        });

        document.body.appendChild(toast);

        // Автоматически удаляем через 10 секунд
        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }
        }, 10000);
    }

    addChatToList(chat) {
        const chatList = document.querySelector('.content-sidebar-items');
        if (!chatList) return;

        const chatName = chat.client_name || chat.name || 'Новый чат';
        const chatAvatar = chat.type === 'buyer_support'
            ? '/assets/dashboard/images/avatar/buyer.png'
            : '/assets/dashboard/images/avatar/support.png';
        const lastMessage = chat.messages && chat.messages.length > 0
            ? chat.messages[0].content
            : 'Нет сообщений';
        const time = new Date(chat.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const isBuyerChat = chat.type === 'buyer_support';
        const isWaiting = chat.status === 'waiting';

        const chatHTML = `
            <div class="p-4 d-flex position-relative border-bottom c-pointer single-item buyer-chat ${isWaiting ? 'buyer-waiting' : ''}"
                 data-chat-id="${chat.id}"
                 data-chat-type="${chat.type}"
                 data-chat-status="${chat.status || 'waiting'}"
                 onclick="window.location.href='/seller/chat/${chat.id}'"
                 style="background-color: #f0f4ff; transition: background-color 1s ease;">

                <div class="avatar-image position-relative">
                    <img src="${chatAvatar}" class="img-fluid" alt="${chatName}">
                    ${isWaiting ? `
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-warning rounded-circle">
                            <span class="visually-hidden">Новый</span>
                        </span>
                    ` : ''}
                </div>

                <div class="ms-3 item-desc flex-grow-1">
                    <div class="w-100 d-flex align-items-center justify-content-between">
                        <div class="hstack gap-2 me-2">
                            <span class="fw-medium">${chatName}</span>
                            ${isBuyerChat && isWaiting ? '<span class="badge bg-warning fs-10">Ожидает</span>' : ''}
                            <span class="badge bg-danger rounded-pill">1</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-10 fw-medium text-muted text-uppercase">${time}</span>
                        </div>
                    </div>
                    <p class="fs-12 fw-semibold text-dark mt-2 mb-0 text-truncate-2-line">
                        ${lastMessage}
                    </p>
                    ${chat.current_page ? `
                        <div class="mt-1">
                            <small class="text-muted">🌐 ${chat.current_page}</small>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;

        // Вставляем в начало списка
        chatList.insertAdjacentHTML('afterbegin', chatHTML);

        // Анимируем подсветку
        const newChatElement = chatList.firstElementChild;
        if (newChatElement) {
            setTimeout(() => {
                newChatElement.style.backgroundColor = '';
            }, 3000);
        }
    }

    playNewChatSound() {
        try {
            const audio = new Audio('/sounds/new-message.mp3');
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Не удалось воспроизвести звук'));
        } catch (e) {
            console.log('Ошибка воспроизведения звука:', e);
        }
    }

    updateChatCounters() {
        // Обновляем счетчик ожидающих чатов если есть
        const waitingBadge = document.querySelector('.waiting-count');
        if (waitingBadge) {
            const currentCount = parseInt(waitingBadge.textContent) || 0;
            waitingBadge.textContent = currentCount + 1;
            waitingBadge.style.display = 'inline-block';
        }
    }

    bindEvents() {
        console.log('🎯 ChatApp: Привязываем события к каналу...');

        // Слушаем все события для отладки
        this.channel.bind_global((eventName, data) => {
            console.log('📨 ChatApp: Получено событие на канале:', eventName, data);
        });

        // Событие нового сообщения
        this.channel.bind('message.sent', (data) => {
            console.log('📩 ChatApp: Получено событие "message.sent"', data);

            const message = data.message;

            if (!message) {
                console.error('❌ ChatApp: Нет данных сообщения в событии');
                return;
            }

            const messageUserId = parseInt(message.user_id);
            const currentUserId = this.userId;

            console.log('👤 ChatApp: Сравнение пользователей:', {
                messageUserId,
                currentUserId,
                isOwnMessage: messageUserId === currentUserId
            });

            if (messageUserId !== currentUserId) {
                console.log('💬 ChatApp: Добавляем сообщение от другого пользователя');
                this.addMessageToChat(message);
                this.playNotificationSound();
                this.markMessagesAsRead();
            } else {
                console.log('💬 ChatApp: Сообщение от текущего пользователя');
                // Обновляем существующее сообщение если оно редактировалось
                const existingElement = document.querySelector(`[data-message-id="${message.id}"]`);
                if (existingElement && message.is_edited) {
                    const contentElement = existingElement.querySelector('.message-content');
                    if (contentElement) {
                        contentElement.textContent = message.content;
                    }
                    const timeElement = existingElement.querySelector('.message-time');
                    if (timeElement && !timeElement.querySelector('.edited-mark')) {
                        timeElement.innerHTML += ' <small class="edited-mark text-muted">(ред.)</small>';
                    }
                }
            }
        });

        // Событие прочтения сообщений
        this.channel.bind('messages.read', (data) => {
            console.log('📖 ChatApp: Получено событие "messages.read"', data);
            if (parseInt(data.user_id) !== this.userId) {
                this.updateUnreadMarkers();
            }
        });

        console.log('✅ ChatApp: События привязаны');
    }

    setupContextMenu() {
        // Добавляем контекстное меню для сообщений
        document.addEventListener('contextmenu', (e) => {
            const messageElement = e.target.closest('.single-chat-item');
            if (!messageElement) return;

            const messageId = messageElement.getAttribute('data-message-id');
            if (!messageId) return;

            // Проверяем, является ли пользователь автором сообщения
            const isCurrentUser = messageElement.classList.contains('own-message');
            if (!isCurrentUser) return;

            e.preventDefault();
            this.showContextMenu(e.clientX, e.clientY, messageId, messageElement);
        });

        // Закрываем контекстное меню при клике вне его
        document.addEventListener('click', () => {
            const menu = document.querySelector('.message-context-menu');
            if (menu) menu.remove();
        });
    }

    showContextMenu(x, y, messageId, messageElement) {
        // Удаляем существующее меню
        const existingMenu = document.querySelector('.message-context-menu');
        if (existingMenu) existingMenu.remove();

        const menu = document.createElement('div');
        menu.className = 'message-context-menu';
        menu.style.cssText = `
            position: fixed;
            left: ${x}px;
            top: ${y}px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            min-width: 150px;
            padding: 4px 0;
        `;

        menu.innerHTML = `
            <a href="javascript:void(0)" class="context-menu-item edit-message" data-message-id="${messageId}">
                <i class="feather-edit me-2"></i>Редактировать
            </a>
            <a href="javascript:void(0)" class="context-menu-item delete-message" data-message-id="${messageId}">
                <i class="feather-trash-2 me-2"></i>Удалить
            </a>
        `;

        document.body.appendChild(menu);

        // Добавляем стили для пунктов меню
        if (!document.getElementById('context-menu-styles')) {
            const style = document.createElement('style');
            style.id = 'context-menu-styles';
            style.textContent = `
                .context-menu-item {
                    display: flex;
                    align-items: center;
                    padding: 8px 16px;
                    color: #333;
                    text-decoration: none;
                    cursor: pointer;
                    transition: background 0.2s;
                }
                .context-menu-item:hover {
                    background: #f8f9fa;
                }
                .context-menu-item i {
                    font-size: 16px;
                }
                .delete-message {
                    color: #dc3545;
                }
                .delete-message:hover {
                    background: #fff5f5;
                }
            `;
            document.head.appendChild(style);
        }

        // Обработчики для пунктов меню
        menu.querySelector('.edit-message').addEventListener('click', () => {
            this.startEditMessage(messageId, messageElement);
            menu.remove();
        });

        menu.querySelector('.delete-message').addEventListener('click', () => {
            this.deleteMessage(messageId);
            menu.remove();
        });
    }

    startEditMessage(messageId, messageElement) {
        const contentElement = messageElement.querySelector('.message-content');
        if (!contentElement) return;

        const currentContent = contentElement.textContent;

        // Создаем поле редактирования
        const editArea = document.createElement('div');
        editArea.className = 'edit-message-area p-2';
        editArea.innerHTML = `
            <textarea class="form-control mb-2 edit-message-input" rows="3">${currentContent}</textarea>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary save-edit-btn">Сохранить</button>
                <button class="btn btn-sm btn-secondary cancel-edit-btn">Отмена</button>
            </div>
        `;

        // Скрываем оригинальный контент
        contentElement.style.display = 'none';
        contentElement.parentNode.insertBefore(editArea, contentElement.nextSibling);

        // Фокус на textarea
        const textarea = editArea.querySelector('.edit-message-input');
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);

        // Обработчик сохранения
        editArea.querySelector('.save-edit-btn').addEventListener('click', () => {
            const newContent = textarea.value.trim();
            if (newContent && newContent !== currentContent) {
                this.updateMessage(messageId, newContent);
            }
            this.cancelEdit(contentElement, editArea);
        });

        // Обработчик отмены
        editArea.querySelector('.cancel-edit-btn').addEventListener('click', () => {
            this.cancelEdit(contentElement, editArea);
        });

        // Сохранение по Ctrl+Enter
        textarea.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.ctrlKey) {
                const newContent = textarea.value.trim();
                if (newContent && newContent !== currentContent) {
                    this.updateMessage(messageId, newContent);
                }
                this.cancelEdit(contentElement, editArea);
            }
            if (e.key === 'Escape') {
                this.cancelEdit(contentElement, editArea);
            }
        });
    }

    cancelEdit(contentElement, editArea) {
        contentElement.style.display = '';
        editArea.remove();
    }

    async updateMessage(messageId, content) {
        try {
            const response = await fetch(`/seller/chat/message/${messageId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ content })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                console.log('✅ ChatApp: Сообщение обновлено');
                const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                if (messageElement) {
                    const contentElement = messageElement.querySelector('.message-content');
                    if (contentElement) {
                        contentElement.textContent = content;
                    }
                    const timeElement = messageElement.querySelector('.message-time');
                    if (timeElement && !timeElement.querySelector('.edited-mark')) {
                        timeElement.innerHTML += ' <small class="edited-mark text-muted">(ред.)</small>';
                    }
                }
            } else {
                console.error('❌ ChatApp: Ошибка обновления:', data);
                alert('Не удалось обновить сообщение');
            }
        } catch (error) {
            console.error('💥 ChatApp: Ошибка:', error);
            alert('Произошла ошибка при обновлении сообщения');
        }
    }

    async deleteMessage(messageId) {
        if (!confirm('Вы уверены, что хотите удалить это сообщение?')) {
            return;
        }

        try {
            const response = await fetch(`/seller/chat/message/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                console.log('✅ ChatApp: Сообщение удалено');
                const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                if (messageElement) {
                    messageElement.style.opacity = '0';
                    messageElement.style.transform = 'scale(0.8)';
                    messageElement.style.transition = 'all 0.3s ease';
                    setTimeout(() => messageElement.remove(), 300);
                }
            } else {
                console.error('❌ ChatApp: Ошибка удаления:', data);
                alert('Не удалось удалить сообщение');
            }
        } catch (error) {
            console.error('💥 ChatApp: Ошибка:', error);
            alert('Произошла ошибка при удалении сообщения');
        }
    }

    updateUnreadMarkers() {
        console.log('🔄 ChatApp: Обновляем метки непрочитанных сообщений...');
        const noReadElements = document.querySelectorAll('.no-read');
        noReadElements.forEach(el => el.remove());
    }

    setupMessageSending() {
        const sendButton = document.getElementById('sendMessageBtn');
        const messageInput = document.getElementById('messageInput');

        if (!sendButton || !messageInput) {
            console.warn('⚠️ ChatApp: Элементы отправки не найдены');
            return;
        }

        // Удаляем старые обработчики
        const newSendButton = sendButton.cloneNode(true);
        sendButton.parentNode.replaceChild(newSendButton, sendButton);

        newSendButton.addEventListener('click', (e) => {
            e.preventDefault();
            this.sendMessage(messageInput.value);
        });

        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage(messageInput.value);
            }
        });

        console.log('✅ ChatApp: Обработчики отправки настроены');
    }

    setupFileUploads() {
        const uploadImage = document.getElementById('uploadImage');
        const uploadFile = document.getElementById('uploadFile');

        const handleFileUpload = (file) => {
            const formData = new FormData();
            formData.append('file', file);

            fetch(`/seller/chat/${this.chatId}/upload`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.sendFileMessage(
                            data.file_path,
                            data.file_name,
                            data.file_type,
                            data.file_size
                        );
                    } else {
                        console.error('Ошибка загрузки файла:', data);
                        alert('Не удалось загрузить файл');
                    }
                })
                .catch(error => {
                    console.error('Ошибка загрузки файла:', error);
                    alert('Не удалось загрузить файл');
                });
        };

        if (uploadImage) {
            uploadImage.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) handleFileUpload(file);
            });
        }

        if (uploadFile) {
            uploadFile.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) handleFileUpload(file);
            });
        }

        console.log('✅ ChatApp: Обработчики загрузки файлов настроены');
    }

    async sendMessage(content) {
        const trimmedContent = content.trim();

        if (!trimmedContent) {
            console.log('⚠️ ChatApp: Пустое сообщение');
            return;
        }

        console.log('📤 ChatApp: Отправка сообщения:', trimmedContent.substring(0, 50));

        try {
            const response = await fetch(`/seller/chat/${this.chatId}/message`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ content: trimmedContent })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                console.log('✅ ChatApp: Сообщение отправлено');

                this.addMessageToChat(data.message);

                const messageInput = document.getElementById('messageInput');
                if (messageInput) messageInput.value = '';

                this.scrollToBottom();
            } else {
                console.error('❌ ChatApp: Ошибка отправки:', data);
                alert('Не удалось отправить сообщение');
            }
        } catch (error) {
            console.error('💥 ChatApp: Ошибка:', error);
            alert('Произошла ошибка при отправке сообщения');
        }
    }

    async sendFileMessage(filePath, fileName, fileType, fileSize = null) {
        console.log('📎 ChatApp: Отправка сообщения с файлом:', fileName);

        try {
            const response = await fetch(`/seller/chat/${this.chatId}/message`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    content: '',
                    file_path: filePath,
                    file_name: fileName,
                    file_type: fileType,
                    file_size: fileSize
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                console.log('✅ ChatApp: Файл отправлен');
                this.addMessageToChat(data.message);
                this.scrollToBottom();
            } else {
                console.error('❌ ChatApp: Ошибка отправки файла:', data);
                alert('Не удалось отправить файл');
            }
        } catch (error) {
            console.error('💥 ChatApp: Ошибка:', error);
            alert('Произошла ошибка при отправке файла');
        }
    }

    addMessageToChat(message) {
        if (document.querySelector(`[data-message-id="${message.id}"]`)) {
            console.log('⚠️ ChatApp: Сообщение уже существует в DOM');
            return;
        }

        console.log('💾 ChatApp: Добавляем сообщение в DOM:', message.id);

        const messageElement = this.createMessageElement(message);
        const chatBody = document.querySelector('.content-area-body');

        if (!chatBody) {
            console.error('❌ ChatApp: Контейнер .content-area-body не найден!');
            return;
        }

        chatBody.insertAdjacentHTML('beforeend', messageElement);

        // Анимация появления
        const newElement = chatBody.lastElementChild;
        if (newElement) {
            newElement.style.opacity = '0';
            newElement.style.transform = 'translateY(20px)';
            newElement.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                newElement.style.opacity = '1';
                newElement.style.transform = 'translateY(0)';
            }, 50);
        }

        console.log('✅ ChatApp: Сообщение добавлено в чат');
        this.scrollToBottom();
    }

    createMessageElement(message) {
        const isCurrentUser = parseInt(message.user_id) === this.userId;

        // Определяем имя пользователя
        let userName = 'Пользователь';
        let userAvatar = '/assets/dashboard/images/avatar/1.png';

        if (message.user && message.user.name) {
            userName = message.user.name;
            userAvatar = message.user.avatar || '/assets/dashboard/images/avatar/1.png';
        } else if (message.user_id === 0) {
            userName = 'Гость';
            userAvatar = '/assets/dashboard/images/avatar/buyer.png';
        }

        let contentHtml = '';
        let fileHtml = '';

        // Контент сообщения
        if (message.content) {
            contentHtml = `
                <div class="message-content-wrapper">
                    <p class="message-content mb-0">${this.escapeHtml(message.content)}</p>
                    ${message.is_edited ? '<small class="text-muted edited-mark">(отредактировано)</small>' : ''}
                </div>`;
        }

        // Файл в сообщении
        if (message.file_path) {
            const fileUrl = `/seller/chat/file/${message.id}`;
            const imageUrl = `/seller/chat/image/${message.id}`;

            if (message.file_type && message.file_type.startsWith('image/')) {
                fileHtml = `
                    <div class="message-image-wrapper">
                        <a href="${imageUrl}" target="_blank" class="d-block">
                            <img src="${imageUrl}" class="img-fluid rounded message-image" alt="${message.file_name || 'Image'}">
                        </a>
                        ${message.file_name ? `<div class="image-name p-2"><small class="${isCurrentUser ? 'text-white-50' : 'text-muted'}">${message.file_name}</small></div>` : ''}
                    </div>`;
            } else if (message.file_name) {
                const fileIcon = this.getFileIcon(message.file_type);
                const fileSize = message.file_size ? this.formatFileSize(message.file_size) : '';

                fileHtml = `
                    <div class="message-file-wrapper p-3">
                        <a href="${fileUrl}" class="text-decoration-none d-flex align-items-center" download="${message.file_name}">
                            <div class="file-icon-wrapper me-3">
                                <i class="${fileIcon} fs-2 text-primary"></i>
                            </div>
                            <div class="file-info">
                                <div class="file-name ${isCurrentUser ? 'text-white' : 'text-dark'} fw-medium">${message.file_name}</div>
                                ${fileSize ? `<small class="${isCurrentUser ? 'text-white-50' : 'text-muted'}">${fileSize}</small>` : ''}
                            </div>
                            <div class="ms-auto">
                                <i class="feather-download ${isCurrentUser ? 'text-white-50' : 'text-muted'}"></i>
                            </div>
                        </a>
                    </div>`;
            }
        }

        return `
            <div class="single-chat-item mb-4 ${isCurrentUser ? 'own-message' : ''}" data-message-id="${message.id}">
                <div class="d-flex ${isCurrentUser ? 'flex-row-reverse' : ''} align-items-end gap-2">
                    <a href="javascript:void(0)" class="avatar-image flex-shrink-0">
                        <img src="${userAvatar}"
                             class="img-fluid rounded-circle"
                             alt="${userName}"
                             style="width: 36px; height: 36px;">
                    </a>
                    <div class="message-body ${isCurrentUser ? 'message-own' : 'message-other'}">
                        <div class="message-header mb-1">
                            <span class="message-sender fw-medium">${userName}</span>
                            <span class="message-time fs-11 text-muted ms-2">
                                ${this.formatTime(message.created_at)}
                                ${message.is_edited ? '<small class="edited-mark text-muted">(ред.)</small>' : ''}
                            </span>
                        </div>
                        <div class="message-bubble ${isCurrentUser ? 'bubble-own' : 'bubble-other'}">
                            ${contentHtml}
                            ${fileHtml}
                        </div>
                        ${!message.is_read && isCurrentUser ? '<span class="fs-10 text-muted mt-1 d-block no-read">Непрочитано</span>' : ''}
                    </div>
                </div>
            </div>`;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    getFileIcon(fileType) {
        if (!fileType) return 'feather-file';

        if (fileType.includes('pdf')) return 'feather-file-text';
        if (fileType.includes('word') || fileType.includes('document')) return 'feather-file-text';
        if (fileType.includes('excel') || fileType.includes('spreadsheet')) return 'feather-file';
        if (fileType.includes('image')) return 'feather-image';
        if (fileType.includes('video')) return 'feather-video';
        if (fileType.includes('audio')) return 'feather-music';
        if (fileType.includes('zip') || fileType.includes('rar') || fileType.includes('archive')) return 'feather-archive';

        return 'feather-file';
    }

    formatFileSize(bytes) {
        if (!bytes) return '';

        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        if (bytes === 0) return '0 Byte';
        const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(100 * (bytes / Math.pow(1024, i))) / 100 + ' ' + sizes[i];
    }

    playNotificationSound() {
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.play().catch(e => console.log('Не удалось воспроизвести звук'));
        } catch (e) {
            console.log('Ошибка воспроизведения звука:', e);
        }
    }

    async markMessagesAsRead() {
        try {
            const response = await fetch(`/seller/chat/${this.chatId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                console.log('✅ ChatApp: Сообщения отмечены как прочитанные');
            }
        } catch (error) {
            console.error('❌ ChatApp: Ошибка отметки сообщений:', error);
        }
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    scrollToBottom() {
        const chatBody = document.querySelector('.content-area-body');
        if (!chatBody) return;

        setTimeout(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 100);
    }

    destroy() {
        if (this.channel) {
            this.channel.unbind_all();
            this.pusher.unsubscribe(`chat.${this.chatId}`);
        }
        if (this.adminChannel) {
            this.adminChannel.unbind_all();
            this.pusher.unsubscribe('admin.chats');
        }
        if (this.pusher) {
            this.pusher.disconnect();
        }
    }
}

// Добавляем стили для анимаций
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    .new-chat-toast {
        animation: slideInRight 0.3s ease;
    }

    .new-chat-toast:hover {
        box-shadow: 0 15px 50px rgba(0,0,0,0.3);
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }
`;
document.head.appendChild(style);

window.ChatApp = ChatApp;


{{-- resources/views/dashboard/support/partials/sidebar.blade.php --}}
<div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
    <h4 class="fw-bolder mb-0">Чаты</h4>
    @if(!auth()->user()->isAdmin())
        <a href="{{route('seller.chat.store')}}" class="avatar-text avatar-md bg-primary text-white">
            <i class="feather-plus"></i>
        </a>
    @endif
    <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex">
        <i class="feather-x"></i>
    </a>
</div>

<div class="content-sidebar-body">
    <!-- Поиск и фильтры -->
    <div class="py-0 px-4 d-flex align-items-center justify-content-between border-bottom">
        <form class="sidebar-search">
            <input type="search" class="py-3 px-0 border-0" id="chattingSearch" placeholder="Поиск...">
        </form>
    </div>

    <!-- Фильтры для админов -->
    @if(auth()->user()->isAdmin())
        <div class="px-4 py-2 border-bottom">
            <div class="btn-group btn-group-sm w-100" role="group" id="chat-filters">
                <button type="button" class="btn btn-outline-primary active chat-filter-btn" data-filter="all">Все</button>
                <button type="button" class="btn btn-outline-warning chat-filter-btn" data-filter="buyer_waiting">Ожидают</button>
                <button type="button" class="btn btn-outline-success chat-filter-btn" data-filter="buyer_active">Активные</button>
                <button type="button" class="btn btn-outline-info chat-filter-btn" data-filter="seller">Продавцы</button>
            </div>
        </div>
    @endif

    <!-- Список чатов -->
    <div class="content-sidebar-items">
        @foreach($chats as $chat)
            @php
                $isBuyerChat = $chat->type === 'buyer_support';
                $otherUser = $chat->users->firstWhere('id', '!=', auth()->id());

                if ($isBuyerChat) {
                    $chatName = $chat->client_name ?? 'Покупатель';
                    $chatAvatar = '/assets/dashboard/images/avatar/2.png';
                    $isWaiting = $chat->status === 'waiting';
                    $isActive = $chat->status === 'active';
                } elseif ($chat->type === 'group') {
                    $chatName = $chat->name ?? 'Группа';
                    $chatAvatar = '/assets/dashboard/images/avatar/3.png';
                } elseif ($chat->type === 'support') {
                    $chatName = $chat->name ?? ($otherUser->name ?? 'Поддержка');
                    $chatAvatar = $otherUser->avatar ?? '/assets/dashboard/images/avatar/4.png';
                } else {
                    $chatName = $chat->name ?? ($otherUser->name ?? 'Чат');
                    $chatAvatar = $otherUser->avatar ?? '/assets/dashboard/images/avatar/1.png';
                }
            @endphp

            <div class="p-4 d-flex position-relative border-bottom c-pointer single-item
                 {{ $isBuyerChat ? 'buyer-chat' : '' }}
                 {{ $isBuyerChat && isset($isWaiting) && $isWaiting ? 'buyer-waiting' : '' }}
                 {{ $isBuyerChat && isset($isActive) && $isActive ? 'buyer-active' : '' }}
                 {{ !$isBuyerChat ? 'seller-chat' : '' }}"
                 data-chat-id="{{ $chat->id }}"
                 data-chat-status="{{ $chat->status ?? '' }}"
                 onclick="window.location.href='{{ route('seller.chat.show', $chat) }}'">

                <div class="avatar-image position-relative">
                    <img src="{{ $chatAvatar }}" class="img-fluid" alt="{{ $chatName }}">
                    @if($isBuyerChat && isset($isWaiting) && $isWaiting)
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-warning rounded-circle">
                            <span class="visually-hidden">Ожидает</span>
                        </span>
                    @endif
                </div>

                <div class="ms-3 item-desc flex-grow-1">
                    <div class="w-100 d-flex align-items-center justify-content-between">
                        <div class="hstack gap-2 me-2">
                            <span class="fw-medium">{{ $chatName }}</span>

                            @if($isBuyerChat)
                                @if(isset($isWaiting) && $isWaiting)
                                    <span class="badge bg-warning fs-10">Ожидает</span>
                                @elseif(isset($isActive) && $isActive)
                                    <span class="badge bg-success fs-10">Активен</span>
                                @endif
                            @endif

                            @if($chat->unread_count > 0)
                                <span class="badge bg-danger rounded-pill">{{ $chat->unread_count }}</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-10 fw-medium text-muted text-uppercase">
                                {{ $chat->messages->last() ? $chat->messages->last()->created_at->diffForHumans() : $chat->created_at->diffForHumans() }}
                            </span>

                            <div class="dropdown">
                                <a href="javascript:void(0)" class="avatar-text avatar-sm" data-bs-toggle="dropdown" onclick="event.stopPropagation();">
                                    <i class="feather-more-vertical"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item mark-as-read" data-chat-id="{{ $chat->id }}" onclick="event.stopPropagation();">
                                            <i class="feather-check-circle me-3"></i>
                                            <span>Прочитано</span>
                                        </a>
                                    </li>

                                    @if($isBuyerChat && auth()->user()->isAdmin())
                                        <li>
                                            <a href="javascript:void(0)" class="dropdown-item transfer-chat-btn"
                                               data-chat-id="{{ $chat->id }}"
                                               data-chat-name="{{ $chatName }}"
                                               onclick="event.stopPropagation();">
                                                <i class="feather-user-plus me-3"></i>
                                                <span>Передать</span>
                                            </a>
                                        </li>
                                    @endif

                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item delete-chat-btn"
                                           data-chat-id="{{ $chat->id }}"
                                           onclick="event.stopPropagation();">
                                            <i class="feather-trash-2 me-3"></i>
                                            <span>Удалить чат</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <p class="fs-12 fw-semibold text-dark mt-2 mb-0 text-truncate-2-line">
                        @if($isBuyerChat && $chat->assignedAdmin)
                            <small class="text-muted">👨‍💼 {{ $chat->assignedAdmin->name }}</small><br>
                        @endif
                        {{ $chat->messages->last() ? $chat->messages->last()->content : 'Нет сообщений' }}
                    </p>

                    @if($isBuyerChat)
                        <div class="mt-1">
                            <small class="text-muted">
                                🌐 {{ $chat->current_page ?? 'Страница не указана' }}
                            </small>
                            @if($chat->country)
                                <br><small class="text-muted">
                                    📍 {{ implode(', ', array_filter([$chat->city, $chat->region, $chat->country])) }}
                                </small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Фильтрация чатов
        const filterButtons = document.querySelectorAll('.chat-filter-btn');
        const chatItems = document.querySelectorAll('.single-item');

        filterButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Убираем активный класс у всех кнопок
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Добавляем активный класс текущей
                this.classList.add('active');

                const filter = this.dataset.filter;

                chatItems.forEach(item => {
                    const chatType = item.dataset.chatType;
                    const chatStatus = item.dataset.chatStatus;

                    switch(filter) {
                        case 'all':
                            item.style.display = '';
                            break;
                        case 'buyer_waiting':
                            if (chatType === 'buyer_support' && chatStatus === 'waiting') {
                                item.style.display = '';
                            } else {
                                item.style.display = 'none';
                            }
                            break;
                        case 'buyer_active':
                            if (chatType === 'buyer_support' && chatStatus === 'active') {
                                item.style.display = '';
                            } else {
                                item.style.display = 'none';
                            }
                            break;
                        case 'seller':
                            if (chatType === 'support') {
                                item.style.display = '';
                            } else {
                                item.style.display = 'none';
                            }
                            break;
                    }
                });
            });
        });

        // Функция передачи чата
        window.transferChat = function(chatId, chatName) {
            // Загружаем список админов
            fetch('/seller/chat/admins-list', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Создаем модальное окно
                        const adminsOptions = data.admins.map(admin =>
                            `<option value="${admin.id}">${admin.name} (${admin.email})</option>`
                        ).join('');

                        const modalHTML = `
                    <div class="modal fade" id="transferChatModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Передать чат "${chatName}"</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Выберите сотрудника для передачи чата:</p>
                                    <select class="form-select" id="adminSelect">
                                        <option value="">Выберите сотрудника...</option>
                                        ${adminsOptions}
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                    <button type="button" class="btn btn-primary" id="confirmTransferBtn">Передать</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                        // Удаляем старое модальное окно если есть
                        const oldModal = document.getElementById('transferChatModal');
                        if (oldModal) oldModal.remove();

                        document.body.insertAdjacentHTML('beforeend', modalHTML);

                        const modal = new bootstrap.Modal(document.getElementById('transferChatModal'));
                        modal.show();

                        // Обработчик подтверждения
                        document.getElementById('confirmTransferBtn').addEventListener('click', function() {
                            const adminId = document.getElementById('adminSelect').value;
                            if (!adminId) {
                                alert('Выберите сотрудника');
                                return;
                            }

                            fetch(`/seller/chat/${chatId}/transfer`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ admin_id: adminId })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        modal.hide();
                                        alert(data.message);
                                        location.reload();
                                    } else {
                                        alert(data.message || 'Ошибка передачи чата');
                                    }
                                })
                                .catch(error => {
                                    console.error('Ошибка:', error);
                                    alert('Произошла ошибка при передаче чата');
                                });
                        });

                        // Очистка при закрытии
                        document.getElementById('transferChatModal').addEventListener('hidden.bs.modal', function() {
                            this.remove();
                        });
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Не удалось загрузить список сотрудников');
                });
        };

        // Обработчики для кнопок "Передать" в dropdown
        document.querySelectorAll('.transfer-chat-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const chatId = this.dataset.chatId;
                const chatName = this.dataset.chatName;
                window.transferChat(chatId, chatName);
            });
        });

        // Обработчики для кнопок "Передать" в header (если есть)
        document.querySelectorAll('.transfer-chat-header-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const chatId = this.dataset.chatId;
                const chatName = this.dataset.chatName;
                window.transferChat(chatId, chatName);
            });
        });
    });
</script>

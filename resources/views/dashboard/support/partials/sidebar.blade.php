<div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
    <h4 class="fw-bolder mb-0">Чаты</h4>
    <a href="{{route('seller.chat.store')}}"
       class="avatar-text avatar-md bg-primary text-white">
        <i class="feather-plus"></i>
    </a>
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

    <!-- Список чатов -->
    <div class="content-sidebar-items">
        @foreach($chats as $chat)
            <div class="p-4 d-flex position-relative border-bottom c-pointer single-item"
                 data-chat-id="{{ $chat->id }}"
                 onclick="window.location.href='{{ route('seller.chat.show', $chat) }}'">
                <div class="avatar-image">
                    @if($chat->type === 'group')
                        <img src="/assets/dashboard/images/avatar/group.png" class="img-fluid" alt="Group">
                    @else
                        @php
                            $otherUser = $chat->users->firstWhere('id', '!=', auth()->id());
                        @endphp
                        <img src="{{ $otherUser->avatar ?? '/assets/dashboard/images/avatar/1.png' }}" class="img-fluid" alt="{{ $otherUser->name ?? 'User' }}">
                    @endif
                </div>
                <div class="ms-3 item-desc">
                    <div class="w-100 d-flex align-items-center justify-content-between">
                        <a href="{{ route('seller.chat.show', $chat) }}" class="hstack gap-2 me-2">
                            <span>{{ $chat->name ?? $otherUser->name ?? 'Chat' }}</span>
                            @if($chat->messages->last() && !$chat->messages->last()->is_read)
                                <div class="wd-8 ht-8 rounded-circle bg-primary"></div>
                            @endif
                            <span class="fs-10 fw-medium text-muted text-uppercase d-none d-sm-block">
                {{ $chat->messages->last() ? $chat->messages->last()->created_at->diffForHumans() : 'No messages' }}
            </span>
                        </a>
                        <div class="dropdown">
                            <a href="javascript:void(0)" class="avatar-text avatar-sm" data-bs-toggle="dropdown">
                                <i class="feather-more-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="javascript:void(0)" class="dropdown-item mark-as-read" data-chat-id="{{ $chat->id }}">
                                        <i class="feather-check-circle me-3"></i>
                                        <span>Прочитано</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"
                                       class="dropdown-item delete-chat-btn"
                                       data-chat-id="{{ $chat->id }}"
                                       title="Удалить чат">
                                        <i class="feather-trash-2 me-3"></i>
                                        <span>Удалить чат</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <p class="fs-12 fw-semibold text-dark mt-2 mb-0 text-truncate-2-line">
                        {{ $chat->messages->last() ? $chat->messages->last()->content : 'No messages yet' }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>
<a href="javascript:void(0);" class="content-sidebar-footer px-4 py-3 fs-11 text-uppercase d-block text-center">Загрузить еще</a>

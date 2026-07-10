{{-- resources/views/dashboard/support/partials/header.blade.php --}}
<div class="page-header-left hstack gap-4">
    <a href="javascript:void(0);" class="app-sidebar-open-trigger">
        <i class="feather-align-left fs-20"></i>
    </a>
    <a href="javascript:void(0);" class="d-flex align-items-center justify-content-center gap-3"
       @if($chat->type === 'buyer_support' && auth()->user()->isAdmin())
           data-bs-toggle="offcanvas" data-bs-target="#buyerInfoOffcanvas"
       @else
           data-bs-toggle="offcanvas" data-bs-target="#userProfileDetails"
        @endif>
        <div class="avatar-image">
            @if($chat->type === 'buyer_support')
                <img src="/assets/dashboard/images/avatar/2.png" class="img-fluid" alt="Покупатель">
            @elseif($chat->type === 'group')
                <img src="/assets/dashboard/images/avatar/3.png" class="img-fluid" alt="Group">
            @elseif($chat->type === 'support')
                <img src="/assets/dashboard/images/avatar/1.png" class="img-fluid" alt="Поддержка">
            @else
                @php
                    $otherUser = $chat->users->firstWhere('id', '!=', auth()->id());
                @endphp
                <img src="{{ $otherUser->avatar ?? '/assets/dashboard/images/avatar/1.png' }}" class="img-fluid" alt="{{ $otherUser->name ?? 'Собеседник' }}">
            @endif
        </div>
        <div class="d-none d-sm-block">
            <div class="fw-bold d-flex align-items-center">
                @if($chat->type === 'buyer_support')
                    {{ $chat->client_name ?? 'Покупатель' }}
                    @if($chat->status === 'waiting')
                        <span class="badge bg-warning ms-2">Ожидает</span>
                    @endif
                @else
                    {{ $chat->name ?? ($otherUser->name ?? 'Чат') }}
                @endif
            </div>
            <div class="d-flex align-items-center mt-1">
                @if($chat->type === 'buyer_support')
                    <span class="wd-7 ht-7 rounded-circle opacity-75 me-2 {{ $chat->status === 'active' ? 'bg-success' : 'bg-warning' }}"></span>
                    <span class="fs-9 text-uppercase fw-bold {{ $chat->status === 'active' ? 'text-success' : 'text-warning' }} user-status">
                        {{ $chat->status === 'active' ? 'В сети' : 'Ожидает оператора' }}
                    </span>
                @elseif($chat->type === 'support')
                    <span class="wd-7 ht-7 rounded-circle opacity-75 me-2 bg-success"></span>
                    <span class="fs-9 text-uppercase fw-bold text-success user-status">В сети</span>
                @elseif(isset($otherUser) && $otherUser)
                    <span class="wd-7 ht-7 rounded-circle opacity-75 me-2 bg-{{ $otherUser ? 'success' : 'warning' }}"></span>
                    <span class="fs-9 text-uppercase fw-bold text-{{ $otherUser ? 'success' : 'warning' }} user-status">
                        {{ $otherUser ? 'В сети' : 'Не в сети' }}
                    </span>
                @endif
            </div>
        </div>
    </a>
</div>

<div class="page-header-right ms-auto">
    <div class="d-flex align-items-center justify-content-center gap-2">
        @if($chat->type === 'buyer_support' && auth()->user()->isAdmin())
            <button type="button"
                    class="btn btn-sm btn-outline-primary transfer-chat-header-btn"
                    data-chat-id="{{ $chat->id }}"
                    data-chat-name="{{ $chat->client_name ?? 'Покупатель' }}"
                    onclick="window.transferChat('{{ $chat->id }}', '{{ $chat->client_name ?? 'Покупатель' }}')">
                <i class="feather-user-plus"></i> Передать
            </button>
        @endif
    </div>
</div>

{{-- Оффканвас с информацией о покупателе --}}
@if($chat->type === 'buyer_support' && auth()->user()->isAdmin())
    <div class="offcanvas offcanvas-end" tabindex="-1" id="buyerInfoOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Информация о покупателе</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mb-4">
                <h6>Основная информация</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>Имя:</strong> {{ $chat->client_name ?? 'Не указано' }}</li>
                    <li class="mb-2"><strong>Email:</strong> {{ $chat->client_email ?? 'Не указан' }}</li>
                    <li class="mb-2"><strong>Телефон:</strong> {{ $chat->client_phone ?? 'Не указан' }}</li>
                    @if($chat->client)
                        <li class="mb-2">
                            <strong>Аккаунт:</strong>
                            <a href="#">{{ $chat->client->name }}</a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="mb-4">
                <h6>Местоположение</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>IP:</strong> {{ $chat->ip_address ?? 'Неизвестно' }}</li>
                    <li class="mb-2"><strong>Страна:</strong> {{ $chat->country ?? 'Неизвестно' }}</li>
                    <li class="mb-2"><strong>Город:</strong> {{ $chat->city ?? 'Неизвестно' }}</li>
                    <li class="mb-2"><strong>Регион:</strong> {{ $chat->region ?? 'Неизвестно' }}</li>
                </ul>
            </div>

            <div class="mb-4">
                <h6>Информация о сессии</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <strong>Страница:</strong>
                        <a href="{{ $chat->current_page }}" target="_blank">{{ $chat->current_page ?? 'Неизвестно' }}</a>
                    </li>
                    <li class="mb-2"><strong>Браузер:</strong> {{ Str::limit($chat->user_agent, 100) ?? 'Неизвестно' }}</li>
                </ul>
            </div>

            <div class="mb-4">
                <h6>Статус чата</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <strong>Статус:</strong>
                        @if($chat->status === 'waiting')
                            <span class="badge bg-warning">Ожидает</span>
                        @elseif($chat->status === 'active')
                            <span class="badge bg-success">Активен</span>
                        @else
                            <span class="badge bg-secondary">Закрыт</span>
                        @endif
                    </li>
                    <li class="mb-2">
                        <strong>Оператор:</strong>
                        {{ $chat->assignedAdmin->name ?? 'Не назначен' }}
                    </li>
                    <li class="mb-2">
                        <strong>Создан:</strong>
                        {{ $chat->created_at->format('d.m.Y H:i') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endif

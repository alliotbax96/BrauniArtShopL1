<div class="page-header-left hstack gap-4">
    <a href="javascript:void(0);" class="app-sidebar-open-trigger">
        <i class="feather-align-left fs-20"></i>
    </a>
    <a href="javascript:void(0);" class="d-flex align-items-center justify-content-center gap-3" data-bs-toggle="offcanvas" data-bs-target="#userProfileDetails">
        <div class="avatar-image">
            @if($chat->type === 'group')
                <img src="/assets/dashboard/images/avatar/group.png" class="img-fluid" alt="Group">
            @elseif($chat->type === 'support')
                <!-- Для чата поддержки показываем специальный аватар -->
                <img src="/assets/dashboard/images/avatar/support.png" class="img-fluid" alt="Поддержка">
            @else
                @php
                    $otherUser = $chat->users->firstWhere('id', '!=', auth()->id());
                @endphp
                @if($otherUser)
                    <img src="{{ $otherUser->avatar ?? '/assets/dashboard/images/avatar/1.png' }}" class="img-fluid" alt="{{ $otherUser->name }}">
                @else
                    <!-- Если другого пользователя нет, показываем заглушку -->
                    <img src="/assets/dashboard/images/avatar/1.png" class="img-fluid" alt="Собеседник">
                @endif
            @endif
        </div>
        <div class="d-none d-sm-block">
            <div class="fw-bold d-flex align-items-center">
                {{ $chat->name ?? ($otherUser->name ?? 'Чат') }}
            </div>
            <div class="d-flex align-items-center mt-1">
                @if($chat->type === 'support')
                    <!-- Для чата поддержки всегда показываем статус "Онлайн" -->
                    <span class="wd-7 ht-7 rounded-circle opacity-75 me-2 bg-success"></span>
                    <span class="fs-9 text-uppercase fw-bold text-success user-status">
                В сети
            </span>
                @elseif($otherUser)
                    <span class="wd-7 ht-7 rounded-circle opacity-75 me-2 bg-{{ $otherUser ? 'success' : 'warning' }}"></span>
                    <span class="fs-9 text-uppercase fw-bold text-{{ $otherUser ? 'success' : 'warning' }} user-status" data-user-id="{{ $otherUser->id ?? '' }}">
                {{ $otherUser ? 'В сети' : 'Не в сети' }}
            </span>
                @else
                    <!-- Если другого пользователя нет -->
                    <span class="wd-7 ht-7 rounded-circle opacity-75 me-2 bg-warning"></span>
                    <span class="fs-9 text-uppercase fw-bold text-warning user-status">
                Не в сети
            </span>
                @endif
            </div>
        </div>
    </a>
</div>
<div class="page-header-right ms-auto">
    <div class="d-flex align-items-center justify-content-center gap-2">
{{--        <a href="javascript:void(0)" class="d-flex" data-bs-toggle="modal" data-bs-target="#voiceCallingModalScreen">--}}
{{--            <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Voice Call">--}}
{{--                <i class="feather-phone-call"></i>--}}
{{--            </div>--}}
{{--        </a>--}}
{{--        <a href="javascript:void(0)" class="d-flex d-flex" data-bs-toggle="modal" data-bs-target="#videoCallingModalScreen">--}}
{{--            <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Video Call">--}}
{{--                <i class="feather-video"></i>--}}
{{--            </div>--}}
{{--        </a>--}}
{{--        <a href="javascript:void(0)" class="ac-info-sidebar-open-trigger" data-bs-toggle="offcanvas" data-bs-target="#userProfileDetails">--}}
{{--            <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Profile Info">--}}
{{--                <i class="feather-info"></i>--}}
{{--            </div>--}}
{{--        </a>--}}
{{--        <div class="dropdown">--}}
{{--            <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="dropdown" data-bs-offset="0,22">--}}
{{--                <i class="feather-more-vertical"></i>--}}
{{--            </a>--}}
{{--            <div class="dropdown-menu">--}}
{{--                <a href="javascript:void(0);" class="dropdown-item">--}}
{{--                    <i class="feather-plus me-3"></i>--}}
{{--                    <span>Join Group</span>--}}
{{--                </a>--}}
{{--                <a href="javascript:void(0);" class="dropdown-item">--}}
{{--                    <i class="feather-user-plus me-3"></i>--}}
{{--                    <span>Invite People</span>--}}
{{--                </a>--}}
{{--                <a href="javascript:void(0);" class="dropdown-item">--}}
{{--                    <i class="feather-star me-3"></i>--}}
{{--                    <span>Add to Favorite</span>--}}
{{--                </a>--}}
{{--                <a href="javascript:void(0);" class="dropdown-item">--}}
{{--                    <i class="feather-bell-off me-3"></i>--}}
{{--                    <span>Mute Conversion</span>--}}
{{--                </a>--}}
{{--                <div class="dropdown-divider"></div>--}}
{{--                <a href="javascript:void(0);" class="dropdown-item">--}}
{{--                    <i class="feather-phone-call me-3"></i>--}}
{{--                    <span>Group Audio Call</span>--}}
{{--                </a>--}}
{{--                <a href="javascript:void(0);" class="dropdown-item">--}}
{{--                    <i class="feather-video me-3"></i>--}}
{{--                    <span>Group Video Call</span>--}}
{{--                </a>--}}
{{--                <div class="dropdown-divider"></div>--}}
{{--                <a href="javascript:void(0);" class="dropdown-item">--}}
{{--                    <i class="feather-slash me-3"></i>--}}
{{--                    <span>Block Conversion</span>--}}
{{--                </a>--}}
{{--                <a href="javascript:void(0);" class="dropdown-item">--}}
{{--                    <i class="feather-trash-2 me-3"></i>--}}
{{--                    <span>Delete Conversion</span>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>
</div>

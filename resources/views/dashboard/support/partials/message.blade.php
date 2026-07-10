@php
    $isCurrentUser = $message->user_id === auth()->id();
    $fileUrl = $message->file_path ? route('seller.chat.file.download', $message) : null;
    $imageUrl = $message->file_path ? route('seller.chat.image.show', $message) : null;

    // Определяем данные пользователя
    if ($message->user_id > 0 && $message->user) {
        $userName = $message->user->name;
        $userAvatar = $message->user->avatar ?? '/assets/dashboard/images/avatar/1.png';
    } else {
        // Для гостей используем имя из чата
        $userName = $message->chat->client_name ?? 'Гость';
        $userAvatar = '/assets/dashboard/images/avatar/2.png';
    }

    // Иконка файла
    $fileIcon = 'feather-file';
    if ($message->file_type) {
        if (str_contains($message->file_type, 'pdf')) $fileIcon = 'feather-file-text';
        elseif (str_contains($message->file_type, 'word') || str_contains($message->file_type, 'document')) $fileIcon = 'feather-file-text';
        elseif (str_contains($message->file_type, 'image')) $fileIcon = 'feather-image';
        elseif (str_contains($message->file_type, 'video')) $fileIcon = 'feather-video';
        elseif (str_contains($message->file_type, 'audio')) $fileIcon = 'feather-music';
        elseif (str_contains($message->file_type, 'zip') || str_contains($message->file_type, 'rar')) $fileIcon = 'feather-archive';
    }

    // Размер файла
    $fileSize = '';
    if ($message->file_size) {
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = $message->file_size > 0 ? floor(log($message->file_size) / log(1024)) : 0;
        $fileSize = round($message->file_size / pow(1024, $i), 2) . ' ' . $sizes[$i];
    }
@endphp

<div class="single-chat-item mb-4 {{ $isCurrentUser ? 'own-message' : '' }}" data-message-id="{{ $message->id }}">
    <div class="d-flex {{ $isCurrentUser ? 'flex-row-reverse' : '' }} align-items-end gap-2">
        {{-- Аватар --}}
        <a href="javascript:void(0)" class="avatar-image flex-shrink-0">
            <img src="{{ $userAvatar }}"
                 class="img-fluid rounded-circle"
                 alt="{{ $userName }}"
                 style="width: 36px; height: 36px;">
        </a>

        {{-- Тело сообщения --}}
        <div class="message-body {{ $isCurrentUser ? 'message-own' : 'message-other' }}">
            {{-- Заголовок --}}
            <div class="message-header mb-1">
                <span class="message-sender fw-medium">{{ $userName }}</span>
                <span class="message-time fs-11 text-muted ms-2">
                    {{ $message->created_at->format('H:i') }}
                    @if($message->is_edited)
                        <small class="edited-mark text-muted">(ред.)</small>
                    @endif
                </span>
            </div>

            {{-- Пузырь сообщения --}}
            <div class="message-bubble {{ $isCurrentUser ? 'bubble-own' : 'bubble-other' }}">
                {{-- Текст сообщения --}}
                @if($message->content)
                    <div class="message-content-wrapper">
                        <p class="message-content mb-0">{{ $message->content }}</p>
                    </div>
                @endif

                {{-- Файл --}}
                @if($message->file_path)
                    @if($message->file_type && str_starts_with($message->file_type, 'image/'))
                        <div class="message-image-wrapper {{ $message->content ? 'mt-2' : '' }}">
                            <a href="{{ $imageUrl }}" target="_blank" class="d-block">
                                <img src="{{ $imageUrl }}"
                                     class="img-fluid rounded message-image"
                                     alt="{{ $message->file_name }}"
                                     loading="lazy">
                            </a>
                            @if($message->file_name)
                                <div class="image-name p-2">
                                    <small class="{{ $isCurrentUser ? 'text-white-50' : 'text-muted' }}">
                                        {{ $message->file_name }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="message-file-wrapper p-3 {{ $message->content ? 'mt-2' : '' }}">
                            <a href="{{ $fileUrl }}" class="text-decoration-none d-flex align-items-center" download="{{ $message->file_name }}">
                                <div class="file-icon-wrapper me-3">
                                    <i class="{{ $fileIcon }} fs-2 text-primary"></i>
                                </div>
                                <div class="file-info">
                                    <div class="file-name {{ $isCurrentUser ? 'text-white' : 'text-dark' }} fw-medium">
                                        {{ $message->file_name }}
                                    </div>
                                    @if($fileSize)
                                        <small class="{{ $isCurrentUser ? 'text-white-50' : 'text-muted' }}">
                                            {{ $fileSize }}
                                        </small>
                                    @endif
                                </div>
                                <div class="ms-auto">
                                    <i class="feather-download {{ $isCurrentUser ? 'text-white-50' : 'text-muted' }}"></i>
                                </div>
                            </a>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Непрочитано --}}
            @if(!$message->is_read && $isCurrentUser)
                <span class="fs-10 text-muted mt-1 d-block no-read">Непрочитано</span>
            @endif
        </div>
    </div>
</div>

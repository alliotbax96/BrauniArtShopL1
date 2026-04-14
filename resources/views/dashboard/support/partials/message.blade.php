@if($message->user_id != auth()->id())
<div class="single-chat-item mb-5" data-message-id="{{ $message->id }}">
    <div class="d-flex align-items-center gap-3 mb-3">
        <a href="javascript:void(0)" class="avatar-image">
            <img src="{{ $message->user->avatar ?? '/assets/dashboard/images/avatar/1.png' }}" class="img-fluid rounded-circle" alt="{{ $message->user->name }}">
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="javascript:void(0);">{{ $message->user->name }}</a>
            <span class="wd-5 ht-5 bg-gray-400 rounded-circle"></span>
            <span class="fs-11 text-muted">{{ $message->created_at->format('H:i') }}</span>
        </div>
    </div>
    <div class="wd-500 p-1 rounded-5 bg-gray-200">
        <p class="py-2 px-3 rounded-5 bg-white mb-0">{{ $message->content }}</p>
        @if($message->file_path)
            <div class="mt-2">
                @if(str_starts_with($message->file_type, 'image'))
                    <img src="{{ asset($message->file_path) }}" class="img-fluid rounded" alt="{{ $message->file_name }}" style="max-height: 200px;">
                @else
                    <a href="{{ asset($message->file_path) }}" class="text-decoration-none">
                        <i class="feather-file-text me-2"></i>{{ $message->file_name }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@else
<div class="single-chat-item mb-5" data-message-id="{{ $message->id }}">
    <div class="d-flex flex-row-reverse align-items-center gap-3 mb-3">
        <a href="javascript:void(0)" class="avatar-image">
            <img src="{{ $message->user->avatar ?? '/assets/dashboard/images/avatar/1.png' }}" class="img-fluid rounded-circle" alt="{{ $message->user->name }}">
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="javascript:void(0);">{{ $message->user->name }}</a>
            <span class="wd-5 ht-5 bg-gray-400 rounded-circle"></span>
            <span class="fs-11 text-muted">{{ $message->created_at->format('H:i') }}</span>
        </div>
    </div>
    <div class="wd-500 p-1 rounded-5 bg-gray-200 ms-auto">
        <p class="py-2 px-3 rounded-5 mb-0 bg-white">{{ $message->content }}</p>
        @if($message->file_path)
            <div class="mt-2">
                @if(str_starts_with($message->file_type, 'image'))
                    <img src="{{ asset($message->file_path) }}" class="img-fluid rounded" alt="{{ $message->file_name }}" style="max-height: 200px;">
                @else
                    <a href="{{ asset($message->file_path) }}" class="text-decoration-none">
                        <i class="feather-file-text me-2"></i>{{ $message->file_name }}
                    </a>
                @endif
            </div>
        @endif
    </div>
    @if(!$message->is_read)
        <span class="fs-10 text-muted no-read">Непрочитано</span>
    @endif
</div>
@endif

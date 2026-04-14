<main class="nxl-container apps-container apps-chat">
    <div class="nxl-content without-header nxl-full-content">
        <!-- [ Main Content ] start -->
        <div class="main-content d-flex">
            <!-- [ Content Sidebar ] start -->
            <div class="content-sidebar content-sidebar-xl" data-scrollbar-target="#psScrollbarInit">
                <!-- Ваш существующий sidebar -->
                @include('dashboard.support.partials.sidebar', ['chats' => $chats])
            </div>
            <!-- [ Main Area ] start -->
            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header sticky-top">
                    <!-- Заголовок чата -->
                    @include('dashboard.support.partials.header', ['chat' => $chat])
                </div>

                <div class="content-area-body">
                    <!-- Сообщения чата -->
                    @foreach($chat->messages as $message)
                        @include('dashboard.support.partials.message', ['message' => $message])
                    @endforeach
                </div>

                <!-- Редактор сообщений -->
                @include('dashboard.support.partials.editor')
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</main>
@push('scripts')
@vite('resources/js/dashboard/Pages/chat.js')
<script type="module">
    // Глобальные переменные для JS
    window.pusherAppKey = '{{ env('VITE_PUSHER_APP_KEY') }}';
    window.pusherCluster = '{{ env('VITE_PUSHER_APP_CLUSTER') }}';

    // Инициализация чата после загрузки страницы
    document.addEventListener('DOMContentLoaded', function() {
        new ChatApp({{ $chat->id }}, {{ auth()->id() }});
    });
</script>
@endpush

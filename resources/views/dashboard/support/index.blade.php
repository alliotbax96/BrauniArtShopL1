<main class="nxl-container apps-container apps-chat">
        <div class="nxl-content without-header nxl-full-content">
            <!-- [ Main Content ] start -->
            <div class="main-content d-flex">
                <!-- [ Content Sidebar ] start -->
                <div class="content-sidebar content-sidebar-xl" data-scrollbar-target="#psScrollbarInit">
                    @include('dashboard.support.partials.sidebar', ['chats' => $chats])
                </div>
                <!-- [ Main Area ] start -->
                <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                    <div class="empty-chat-state d-flex flex-column align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="feather-message-square fs-5 text-muted" style="opacity: 0.5;"></i>
                            <h3 class="mt-4 mb-2">Выберите чат</h3>
                            <p class="text-muted">Чтобы начать общение, выберите разговор из списка.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </main>

@push('scripts')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            // Обработчик выбора чата из списка
            document.querySelectorAll('[data-chat-id]').forEach(item => {
                item.addEventListener('click', function() {
                    const chatId = this.getAttribute('data-chat-id');
                    loadChat(chatId);
                });
            });

            // Обработчик отметки чата как прочитанного
            document.querySelectorAll('.mark-as-read').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const chatId = this.getAttribute('data-chat-id');

                    fetch(`/seller/chat/${chatId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => response.json())
                        .then(() => {
                            // Убираем индикатор непрочитанных сообщений
                            this.closest('.single-item').querySelector('.bg-primary')?.remove();
                        });
                });
            });

            function loadChat(chatId) {
                // Показываем загрузку
                document.querySelector('.content-area').innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

                // Загружаем чат через AJAX
                fetch(`/seller/chat/${chatId}`)
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('.content-area').innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Ошибка загрузки чата:', error);
                        document.querySelector('.content-area').innerHTML = `
                    <div class="text-center text-danger">
                <i class="feather-alert-triangle fs-3"></i>
                <p>Не удалось загрузить чат</p>
            </div>
        `;
          });
            }
        });
    </script>
@endpush

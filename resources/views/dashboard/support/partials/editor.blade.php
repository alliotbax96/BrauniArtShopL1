<div class="d-flex align-items-center justify-content-between border-top border-gray-5 bg-white sticky-bottom">
    <div class="d-flex align-center">
{{--        <div class="dropdown border-end border-gray-5">--}}
{{--            <a href="javascript:void(0)" data-bs-toggle="dropdown">--}}
{{--                <div class="wd-60 d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Pick Template" style="height: 59px">--}}
{{--                    <i class="feather-hash"></i>--}}
{{--                </div>--}}
{{--            </a>--}}
{{--            <ul class="dropdown-menu">--}}
{{--                <li>--}}
{{--                    <a href="javascript:void(0)" class="dropdown-item">--}}
{{--                        <i class="feather-file-text me-3"></i>Нет шаблонов--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li class="dropdown-divider"></li>--}}
{{--                <li>--}}
{{--                    <a href="javascript:void(0)" class="dropdown-item">--}}
{{--                        <i class="feather-save me-3"></i>Сохранить шаблон--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            </ul>--}}
{{--        </div>--}}

        <div class="dropdown border-end border-gray-5">
            <a href="javascript:void(0)" data-bs-toggle="dropdown">
                <div class="wd-60 d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Прикрепить" style="height: 59px">
                    <i class="feather-link"></i>
                </div>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <label class="dropdown-item" style="cursor: pointer;">
                        <i class="feather-image me-3"></i>Изображение
                        <input type="file" class="d-none" id="uploadImage" accept="image/*">
                    </label>
                </li>
                <li>
                    <label class="dropdown-item" style="cursor: pointer;">
                        <i class="feather-file me-3"></i>Файл
                        <input type="file" class="d-none" id="uploadFile">
                    </label>
                </li>
            </ul>
        </div>
    </div>

    <!-- Поле ввода сообщения -->
    <div class="flex-grow-1 px-3">
    <input class="form-control border-0 emoji-picker" id="messageInput" placeholder="Ваше сообщение...">
    </div>

    <!-- Кнопка отправки -->
    <div class="border-start border-gray-5 d-flex send-message">
        <a href="javascript:void(0)" class="d-flex align-items-center justify-content-center wd-60" style="height: 59px;" id="sendMessageBtn">
            <i class="feather-send"></i>
        </a>
    </div>
</div>

<!-- Скрипт для обработки вложений -->
<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        // Обработка загрузки изображений
        document.getElementById('uploadImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('chat_id', {{ $chat->id }});

                fetch('/seller/chat/{{ $chat->id }}/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Автоматически отправляем сообщение с файлом
                            document.getElementById('messageInput').value = '';
                            sendMessageWithFile(data.file_path, data.file_name, 'image');
                        }
                    });
            }
        });

        // Обработка загрузки файлов
        document.getElementById('uploadFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('chat_id', {{ $chat->id }});

                fetch('/seller/chat/{{ $chat->id }}/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Автоматически отправляем сообщение с файлом
                            document.getElementById('messageInput').value = '';
                            sendMessageWithFile(data.file_path, data.file_name, data.file_type);
                        }
                    });
            }
        });
    });

    function sendMessageWithFile(filePath, fileName, fileType) {
        fetch('/seller/chat/{{ $chat->id }}/message', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                content: '',
                file_path: filePath,
                file_name: fileName,
                file_type: fileType
            })
        });
    }
</script>

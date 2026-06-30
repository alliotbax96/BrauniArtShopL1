 <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-top-0">
                    <div class="card-header p-0">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="envTabs" role="tablist">
                            <li class="nav-item flex-fill border-top" role="presentation">
                                <a href="javascript:void(0);"
                                   class="nav-link active"
                                   data-bs-toggle="tab"
                                   data-bs-target="#envSettingsTab"
                                   role="tab">
                                    Переменные окружения
                                </a>
                            </li>
                            <li class="nav-item flex-fill border-top" role="presentation">
                                <a href="javascript:void(0);"
                                   class="nav-link"
                                   data-bs-toggle="tab"
                                   data-bs-target="#envBackupsTab"
                                   role="tab">
                                    Бэкапы
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <!-- Вкладка переменных окружения -->
                        <div class="tab-pane fade show active" id="envSettingsTab" role="tabpanel">
                            <div class="card-body personal-info">
                                <!-- Заголовок и кнопки -->
                                <div class="mb-4 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Управление переменными окружения (.env):</span>
                                    </h5>
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('admin.env.backup') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light-brand">
                                                <i class="bi bi-archive"></i> Создать бэкап
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addVariableModal">
                                            <i class="bi bi-plus-circle"></i> Добавить переменную
                                        </button>
                                    </div>
                                </div>

                                <!-- Уведомления -->
                                <div id="alert_env">
                                    @if(session()->has('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif
                                    @if(session()->has('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ session('error') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif
                                </div>

                                <!-- Форма редактирования -->
                                <form id="envForm" method="post" action="{{ route('admin.env.update') }}">
                                    @csrf
                                    @method('PUT')

                                    @php
                                        $sensitiveKeys = ['APP_KEY', 'DB_PASSWORD', 'MAIL_PASSWORD', 'REDIS_PASSWORD', 'AWS_SECRET_ACCESS_KEY'];
                                    @endphp

                                    @foreach($envData as $category => $variables)
                                        <div class="mb-5">
                                            <h6 class="fw-bold mb-3 text-primary">
                                                <i class="bi bi-folder"></i> {{ $category }}
                                            </h6>

                                            @foreach($variables as $key => $value)
                                                <div class="row mb-4 align-items-start">
                                                    <div class="col-lg-4">
                                                        <label for="env_{{ $key }}" class="fw-semibold">
                                                            {{ $key }}
                                                            @if(in_array($key, $sensitiveKeys))
                                                                <i class="bi bi-shield-lock text-warning" title="Чувствительные данные"></i>
                                                            @endif
                                                        </label>
                                                        @if(strlen($value) > 50)
                                                            <small class="d-block text-muted">Многострочное значение</small>
                                                        @endif
                                                    </div>
                                                    <div class="col-lg-8">
                                                        @if(strlen($value) > 100)
                                                            <!-- Текстовое поле для длинных значений -->
                                                            <div class="input-group">
                                                            <textarea class="form-control"
                                                                      form="envForm"
                                                                      name="env_data[{{ $key }}]"
                                                                      id="env_{{ $key }}"
                                                                      rows="4"
                                                                      placeholder="{{ $key }}">{{ $value }}</textarea>
                                                            </div>
                                                        @elseif(in_array($key, $sensitiveKeys))
                                                            <!-- Защищенное поле для чувствительных данных -->
                                                            <div class="input-group">
                                                                <input type="password"
                                                                       class="form-control"
                                                                       form="envForm"
                                                                       name="env_data[{{ $key }}]"
                                                                       id="env_{{ $key }}"
                                                                       value="{{ $value }}"
                                                                       placeholder="{{ $key }}">
                                                                <button class="btn btn-outline-secondary toggle-password"
                                                                        type="button"
                                                                        data-target="env_{{ $key }}">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                            </div>
                                                        @elseif(in_array($key, ['APP_ENV']))
                                                            <!-- Выпадающий список для APP_ENV -->
                                                            <div class="input-group">
                                                                <select form="envForm"
                                                                        name="env_data[{{ $key }}]"
                                                                        id="env_{{ $key }}"
                                                                        class="form-control">
                                                                    <option value="local" {{ $value == 'local' ? 'selected' : '' }}>Local</option>
                                                                    <option value="development" {{ $value == 'development' ? 'selected' : '' }}>Development</option>
                                                                    <option value="staging" {{ $value == 'staging' ? 'selected' : '' }}>Staging</option>
                                                                    <option value="production" {{ $value == 'production' ? 'selected' : '' }}>Production</option>
                                                                    <option value="testing" {{ $value == 'testing' ? 'selected' : '' }}>Testing</option>
                                                                </select>
                                                            </div>
                                                        @elseif(in_array($key, ['APP_DEBUG']))
                                                            <!-- Переключатель для boolean значений -->
                                                            <div class="input-group">
                                                                <select form="envForm"
                                                                        name="env_data[{{ $key }}]"
                                                                        id="env_{{ $key }}"
                                                                        class="form-control">
                                                                    <option value="true" {{ $value == 'true' ? 'selected' : '' }}>True</option>
                                                                    <option value="false" {{ $value == 'false' ? 'selected' : '' }}>False</option>
                                                                </select>
                                                            </div>
                                                        @elseif(in_array($key, ['DB_CONNECTION', 'MAIL_MAILER', 'CACHE_DRIVER', 'SESSION_DRIVER', 'QUEUE_CONNECTION']))
                                                            <!-- Специальные селекты для драйверов -->
                                                            <div class="input-group">
                                                                <select form="envForm"
                                                                        name="env_data[{{ $key }}]"
                                                                        id="env_{{ $key }}"
                                                                        class="form-control">
                                                                    @if($key == 'DB_CONNECTION')
                                                                        <option value="mysql" {{ $value == 'mysql' ? 'selected' : '' }}>MySQL</option>
                                                                        <option value="pgsql" {{ $value == 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
                                                                        <option value="sqlite" {{ $value == 'sqlite' ? 'selected' : '' }}>SQLite</option>
                                                                        <option value="sqlsrv" {{ $value == 'sqlsrv' ? 'selected' : '' }}>SQL Server</option>
                                                                    @elseif($key == 'MAIL_MAILER')
                                                                        <option value="smtp" {{ $value == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                                                        <option value="sendmail" {{ $value == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                                                        <option value="mailgun" {{ $value == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                                                        <option value="ses" {{ $value == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                                                        <option value="postmark" {{ $value == 'postmark' ? 'selected' : '' }}>Postmark</option>
                                                                        <option value="log" {{ $value == 'log' ? 'selected' : '' }}>Log</option>
                                                                        <option value="array" {{ $value == 'array' ? 'selected' : '' }}>Array</option>
                                                                    @elseif(in_array($key, ['CACHE_DRIVER', 'SESSION_DRIVER']))
                                                                        <option value="file" {{ $value == 'file' ? 'selected' : '' }}>File</option>
                                                                        <option value="redis" {{ $value == 'redis' ? 'selected' : '' }}>Redis</option>
                                                                        <option value="memcached" {{ $value == 'memcached' ? 'selected' : '' }}>Memcached</option>
                                                                        <option value="database" {{ $value == 'database' ? 'selected' : '' }}>Database</option>
                                                                        <option value="array" {{ $value == 'array' ? 'selected' : '' }}>Array</option>
                                                                    @elseif($key == 'QUEUE_CONNECTION')
                                                                        <option value="sync" {{ $value == 'sync' ? 'selected' : '' }}>Sync</option>
                                                                        <option value="database" {{ $value == 'database' ? 'selected' : '' }}>Database</option>
                                                                        <option value="redis" {{ $value == 'redis' ? 'selected' : '' }}>Redis</option>
                                                                        <option value="beanstalkd" {{ $value == 'beanstalkd' ? 'selected' : '' }}>Beanstalkd</option>
                                                                        <option value="sqs" {{ $value == 'sqs' ? 'selected' : '' }}>Amazon SQS</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        @else
                                                            <!-- Обычное текстовое поле -->
                                                            <div class="input-group">
                                                                <input type="text"
                                                                       class="form-control"
                                                                       form="envForm"
                                                                       name="env_data[{{ $key }}]"
                                                                       id="env_{{ $key }}"
                                                                       value="{{ $value }}"
                                                                       placeholder="{{ $key }}">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </form>

                                <!-- Кнопка сохранения внизу -->
                                <div class="mt-4 text-end">
                                    <button type="submit" form="envForm" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Сохранить все изменения
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка бэкапов -->
                        <div class="tab-pane fade" id="envBackupsTab" role="tabpanel">
                            <div class="card-body personal-info">
                                <div class="mb-4 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">История бэкапов:</span>
                                    </h5>
                                </div>

                                @if(count($envBackups) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th>Файл</th>
                                                <th>Размер</th>
                                                <th>Дата создания</th>
                                                <th>Действия</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($envBackups as $backup)
                                                <tr>
                                                    <td class="fw-semibold">{{ $backup['filename'] }}</td>
                                                    <td>{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                                                    <td>{{ $backup['modified'] }}</td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <form action="{{ route('admin.env.restore', $backup['filename']) }}"
                                                                  method="POST"
                                                                  class="d-inline">
                                                                @csrf
                                                                <button type="submit"
                                                                        class="btn btn-sm btn-success"
                                                                        onclick="return confirm('Восстановить этот бэкап? Текущие настройки будут заменены.')">
                                                                    <i class="bi bi-arrow-counterclockwise"></i> Восстановить
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('admin.env.delete-backup', $backup['filename']) }}"
                                                                  method="POST"
                                                                  class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                        class="btn btn-sm btn-danger"
                                                                        onclick="return confirm('Удалить этот бэкап?')">
                                                                    <i class="bi bi-trash"></i> Удалить
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="bi bi-archive text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-3">Бэкапов пока нет</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно добавления новой переменной -->
    <div class="modal fade" id="addVariableModal" tabindex="-1" aria-labelledby="addVariableModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addVariableModalLabel">
                        <i class="bi bi-plus-circle"></i> Добавить новую переменную
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addVariableForm">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="newKey" class="fw-semibold mb-2">Ключ переменной:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newKey"
                                       placeholder="НАПРИМЕР: MY_CUSTOM_VARIABLE" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="newValue" class="fw-semibold mb-2">Значение:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newValue"
                                       placeholder="Значение переменной">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="newCategory" class="fw-semibold mb-2">Категория:</label>
                            <div class="input-group">
                                <select class="form-control" id="newCategory">
                                    <option value="Другие">Другие</option>
                                    <option value="Приложение">Приложение</option>
                                    <option value="База данных">База данных</option>
                                    <option value="Почта">Почта</option>
                                    <option value="Кэш и сессии">Кэш и сессии</option>
                                    <option value="Redis">Redis</option>
                                    <option value="AWS">AWS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-sm btn-primary">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Переключение видимости паролей
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const target = document.getElementById(this.dataset.target);
                    const icon = this.querySelector('i');

                    if (target.type === 'password') {
                        target.type = 'text';
                        icon.className = 'bi bi-eye-slash';
                    } else {
                        target.type = 'password';
                        icon.className = 'bi bi-eye';
                    }
                });
            });

            // Автоматическое скрытие алертов
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Добавление новой переменной
            document.getElementById('addVariableForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const key = document.getElementById('newKey').value.trim().toUpperCase().replace(/[^A-Z0-9_]/g, '_');
                const value = document.getElementById('newValue').value.trim();
                const category = document.getElementById('newCategory').value;

                if (!key) {
                    alert('Введите корректный ключ переменной (только латинские буквы, цифры и знак подчеркивания)');
                    return;
                }

                // Создаем новое поле в форме
                const envForm = document.getElementById('envForm');
                const existingField = document.getElementById('env_' + key);

                if (existingField) {
                    alert('Переменная с таким ключом уже существует!');
                    return;
                }

                // Создаем HTML для нового поля
                const newFieldHtml = `
            <div class="row mb-4 align-items-start">
                <div class="col-lg-4">
                    <label for="env_${key}" class="fw-semibold">${key}</label>
                </div>
                <div class="col-lg-8">
                    <div class="input-group">
                        <input type="text"
                               class="form-control"
                               form="envForm"
                               name="env_data[${key}]"
                               id="env_${key}"
                               value="${value}"
                               placeholder="${key}">
                    </div>
                </div>
            </div>
        `;

                // Находим нужную категорию или создаем новую
                let categoryHeader = Array.from(document.querySelectorAll('#envSettingsTab h6.fw-bold')).find(
                    h6 => h6.textContent.trim() === category
                );

                if (!categoryHeader) {
                    // Создаем новую категорию
                    const newCategoryHtml = `
                <div class="mb-5">
                    <h6 class="fw-bold mb-3 text-primary">
                        <i class="bi bi-folder"></i> ${category}
                    </h6>
                </div>
            `;

                    const formContainer = document.querySelector('#envSettingsTab form');
                    formContainer.insertAdjacentHTML('beforeend', newCategoryHtml);

                    // Находим только что созданную категорию
                    categoryHeader = Array.from(document.querySelectorAll('#envSettingsTab h6.fw-bold')).find(
                        h6 => h6.textContent.trim() === category
                    );
                }

                // Добавляем поле в категорию
                categoryHeader.closest('.mb-5').insertAdjacentHTML('beforeend', newFieldHtml);

                // Закрываем модальное окно
                const modal = bootstrap.Modal.getInstance(document.getElementById('addVariableModal'));
                modal.hide();
                this.reset();
            });
        });
    </script>
@endpush

<div class="main-content">
    <div class="row">
        <div class="col-xl-12">
            <div class="card invoice-container">
                <div class="card-header">
                    <h5>Создание квеста</h5>
                </div>
                <form id="quest-create">
                    @csrf
                    <div class="card-body p-0">
                        <div class="mt-2 px-4 row justify-content-between">
                            <div class="d-md-flex align-items-center justify-content-between">
                                <div>
                                    <div class="mb-5">
                                        <div id="quest-alert"></div>
                                        <p class="text-muted">
                                            Загрузите или замените фотографии квеста. Чтобы изменить порядок
                                            изображений, просто перетащите их мышкой. Главное фото — то, что стоит
                                            первым в списке.
                                        </p>
                                    </div>
                                    <div class="mb-4">
                                        <label for="choose-file" class="custom-file-upload" id="choose-file-label">Выберите
                                            файл</label>
                                        <input type="file" id="choose-file" name="file[]" style="display: none" multiple
                                               accept=".jpg,.jpeg,.png,.gif">
                                    </div>
                                    <div class="row" id="js-file-list"></div>
                                </div>
                            </div>
                        </div>
                        <hr class="border-dashed">
                        <div class="px-4 row justify-content-between">
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Название</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           placeholder="Название квеста">
                                </div>
                            </div>
                        </div>
                        <hr class="border-dashed">
                        <div class="px-4 row justify-content-between">
                            <div class="col-xl-5 mb-4 mb-sm-0">
                                <div class="form-group row mb-3">
                                    <label for="type" class="col-sm-3 col-form-label">Тип</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" name="type" id="type">
                                            <option value="">Выберите тип</option>
                                            <option value="quest">Квест</option>
                                            <option value="performance">Перформанс</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="difficulty" class="col-sm-3 col-form-label">Сложность</label>
                                    <div class="col-sm-9">
                                        <input type="number" min="1" max="3" class="form-control" id="difficulty"
                                               name="difficulty" placeholder="1–3">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="fear_level" class="col-sm-3 col-form-label">Уровень страха</label>
                                    <div class="col-sm-9">
                                        <input type="number" min="1" max="3" class="form-control" id="fear_level"
                                               name="fear_level" placeholder="1–3">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="duration" class="col-sm-3 col-form-label">
                                        Продолжительность(мин)
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="duration" name="duration"
                                               placeholder="Например: 60">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="min_age" class="col-sm-3 col-form-label">Минимальный возраст</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="min_age" name="min_age"
                                               placeholder="Например: 12">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="min_players" class="col-sm-3 col-form-label">Мин. игроков</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="min_players" name="min_players"
                                               placeholder="Например: 2">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="max_players" class="col-sm-3 col-form-label">Макс. игроков</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="max_players" name="max_players"
                                               placeholder="Например: 6">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-7 mb-4 mb-sm-0">
                                <div class="form-group row mb-3">
                                    <label for="base_price" class="col-sm-3 col-form-label">Базовая цена</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="0.01" class="form-control" id="base_price"
                                               name="base_price" placeholder="Например: 2500.00">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="base_price" class="col-sm-3 col-form-label">Количество игроков
                                        включенных в базовую цену</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="1" class="form-control" id="base_player_count"
                                               name="base_player_count" placeholder="Например: 5">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="base_price" class="col-sm-3 col-form-label">Стоимость за дополнительного
                                        игрока</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="1" class="form-control" id="additional_player_price"
                                               name="additional_player_price" placeholder="Например: 1000">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="description" class="col-sm-3 col-form-label">Описание</label>
                                    <div class="col-sm-9">
                                        <div id="description">
                                        </div>
                                        <textarea class="hidden" rows="5" id="descriptionTextArea" name="description"
                                        placeholder="Подробное описание квеста"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <hr class="border-dashed my-4">
                        <div class="px-4 justify-content-between">
                            <div class="mb-4">
                                <h6 class="fw-bold">
                                    Адрес:
                                </h6>
                                <span class="fs-12 text-muted">
                                    Напишите, где будет квест. Используйте подсказки из списка, чтобы мы поняли, какая станция метро ближе всего и где точно находится место.
                                </span>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="address" class="col-sm-3">Адрес</label>
                                <input type="text" id="address" name="address" class="form-control mb-2"
                                       placeholder="Например: Москва, Вятская, 47">
                            </div>
                            <div class="form-group row mb-3">
                                <label for="address" class="col-sm-3">Станция метро</label>
                                <input type="text" id="station" name="station" class="form-control mb-2"
                                       placeholder="Например: Дмитровская">
                            </div>
                        </div>
                        <hr class="border-dashed my-4">
                        <div class="px-4 justify-content-between">
                            <div class="mb-4">
                                <h6 class="fw-bold">
                                    Особенности:
                                </h6>
                                <span class="fs-12 text-muted">
                                    Введите описание особенности и выберите «Добавить». Вы можете указать любое число особенностей, которые будут отображаться в карточке квеста в виде списка.
                                </span>
                            </div>
                            <div class="form-group row mb-3">
                                    <div id="features-container">
                                        <!-- Здесь будут отображаться добавленные особенности -->
                                    </div>
                                    <input type="text" id="add-feature" class="form-control mb-2"
                                           placeholder="Например: смесь жанров: мистика, экшн, стелс и хоррор">
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="button" id="add-feature-btn" class="btn btn-sm btn-primary">
                                    <i class="feather feather-plus"></i>
                                    Добавить
                                </button>
                            </div>

                        </div>
                        <!-- Блок таймслотов -->
                        <hr class="border-dashed my-4">
                        <div class="px-4 justify-content-between">
                            <div class="mb-4">
                                <h6 class="fw-bold">
                                    Таймслоты:
                                </h6>
                                <span class="fs-12 text-muted">
                                    Добавьте доступные временные слоты для этого квеста
                                </span>
                            </div>
                            <div id="timeslots-container"></div>
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="button" id="add-timeslot" class="btn btn-sm btn-primary">
                                    <i class="feather feather-plus"></i>
                                    Добавить
                                </button>
                            </div>
                            <!-- Конец блока таймслотов -->
                        </div>
                        <!-- Блок дополнительных услуг -->
                        <hr class="border-dashed my-4">
                        <div class="px-4">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-bold">Дополнительные услуги:</h6>
                                    <span
                                        class="fs-12 text-muted">Например: "Дополнительный игрок", "Запись с камер"</span>
                                </div>
                                <div class="avatar-text avatar-sm" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                     title="Информация">
                                    <i class="feather feather-info"></i>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered overflow-hidden" id="tab_logic">
                                    <thead>
                                    <tr class="single-item">
                                        <th class="text-center">#</th>
                                        <th class="text-center wd-450">Наименование</th>
                                        <th class="text-center wd-150">Цена</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Убираем пустую строку addr1 -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="button" id="delete_row" class="btn btn-sm bg-soft-danger text-danger">
                                    Удалить
                                </button>
                                <button type="button" id="add_row" class="btn btn-sm btn-primary">Добавить</button>
                            </div>
                        </div>
                        <!-- Кнопки формы -->
                        <hr class="border-dashed my-4">
                        <div class="px-4 py-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{redirect()->back()}}" class="btn btn-outline-secondary">Отмена</a>
                                <button type="submit" class="btn btn-primary">Сохранить квест</button>
                            </div>
                        </div>
                    </div>
                </form> <!-- ЗАКРЫВАЮЩИЙ ТЕГ FORM -->
            </div>
        </div>
    </div>


@push('scripts')
    @vite('resources/js/dashboard/Pages/QuestCreate.js')
@endpush


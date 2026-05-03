<div class="main-content">
    <div class="row">
        <div class="col-xl-12">
            <div class="card invoice-container">
                <div class="card-header">
                    <h5>Создание квеста</h5>
                </div>
                <form id="quest-edit">
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
                                    <div class="row" id="js-file-list">
                                        @foreach($quest->getProductImages() as $image)
                                            <div class="col-sm-3">
                                                <div class="card stretch stretch-full">
                                                    <div class="card-body p-0 ht-200 position-relative">
                                                        <img
                                                            src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{$image['url']}}"
                                                            class="img-fluid ht-200" alt="">
                                                        <div class="position-absolute" style="top: 15px; right: 15px">
                                                            <a href="javascript:void(0)"
                                                               onclick="remove_img(this); return false;"
                                                               class="avatar-text avatar-sm">
                                                                <i class="feather feather-x-circle"></i>
                                                            </a>
                                                        </div>
                                                        <input type="hidden" name="images[]" value="{{$image['url']}}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="border-dashed">
                        <div class="px-4 row justify-content-between">
                            <input type="hidden" name="QuestId" value="{{$quest->id}}">
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Название</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           placeholder="Название квеста" value="{{$quest->title}}">
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
                                            <option value="quest" @if($quest->type == 'quest') selected @endif >Квест
                                            </option>
                                            <option value="performance"
                                                    @if($quest->type == 'performance') selected @endif >Перформанс
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="difficulty" class="col-sm-3 col-form-label">Сложность</label>
                                    <div class="col-sm-9">
                                        <input type="number" min="1" max="3" class="form-control" id="difficulty"
                                               name="difficulty" placeholder="1–3" value="{{$quest->difficulty}}">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="fear_level" class="col-sm-3 col-form-label">Уровень страха</label>
                                    <div class="col-sm-9">
                                        <input type="number" min="1" max="3" class="form-control" id="fear_level"
                                               name="fear_level" placeholder="1–3" value="{{$quest->fear_level}}">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="duration" class="col-sm-3 col-form-label">
                                        Продолжительность(мин)
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="duration" name="duration"
                                               placeholder="Например: 60" value="{{$quest->duration}}">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="min_age" class="col-sm-3 col-form-label">Минимальный возраст</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="min_age" name="min_age"
                                               placeholder="Например: 12" value="{{$quest->min_age}}">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="min_players" class="col-sm-3 col-form-label">Мин. игроков</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="min_players" name="min_players"
                                               placeholder="Например: 2" value="{{$quest->min_players}}">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="max_players" class="col-sm-3 col-form-label">Макс. игроков</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="max_players" name="max_players"
                                               placeholder="Например: 6" value="{{$quest->max_players}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-7 mb-4 mb-sm-0">
                                <div class="form-group row mb-3">
                                    <label for="base_price" class="col-sm-3 col-form-label">Базовая цена</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="0.01" class="form-control" id="base_price"
                                               name="base_price" placeholder="Например: 2500.00"
                                               value="{{$quest->base_price}}">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="base_player_count" class="col-sm-3 col-form-label">Количество игроков
                                        включенных в базовую цену</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="1" class="form-control" id="base_player_count"
                                               name="base_player_count" placeholder="Например: 5"
                                               value="{{$quest->base_player_count}}">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="additional_player_price" class="col-sm-3 col-form-label">Стоимость за
                                        дополнительного
                                        игрока</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="1" class="form-control" id="additional_player_price"
                                               name="additional_player_price" placeholder="Например: 1000"
                                               value="{{$quest->additional_player_price}}">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="description" class="col-sm-3 col-form-label">Описание</label>
                                    <div class="col-sm-9">
                                        <div id="description"></div>
                                        <textarea class="hidden" rows="5" id="descriptionTextArea" name="description"
                                                  placeholder="Подробное описание квеста">{{$quest->description}}</textarea>
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
                                       placeholder="Например: Москва, Вятская, 47" value="{{$quest->address}}">
                            </div>
                            <div class="form-group row mb-3">
                                <label for="address" class="col-sm-3">Станция метро</label>
                                <input type="text" id="station" name="station" class="form-control mb-2"
                                       placeholder="Например: Дмитровская" value="{{$quest->station}}">
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
                                    @foreach($quest->getFeatures() as $feature)
                                        <div class="feature-tag personal">
                                            {{$feature}}
                                            <span class="remove-btn" onclick="removeTag(this)">×</span>
                                            <input type="hidden" name="features[]" value="{{$feature}}">
                                        </div>
                                    @endforeach
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
                            <div id="timeslots-container">
                                @foreach($quest->timeslots as $key => $timeslot)
                                    <div class="timeslot-row mb-3 d-flex gap-3 align-items-end">
                                        <div class="flex-grow-1">
                                            <label class="form-label">День недели</label>
                                            <select class="form-select dey-of-week"
                                                    name="timeslots[{{$key}}][day_of_week]" required>
                                                <option value="">Выберите день</option>
                                                <option value="Monday" @if($timeslot->day_of_week == 'Monday') selected @endif >Понедельник</option>
                                                <option value="Tuesday" @if($timeslot->day_of_week == 'Tuesday') selected @endif >Вторник</option>
                                                <option value="Wednesday" @if($timeslot->day_of_week == 'Wednesday') selected @endif >Среда</option>
                                                <option value="Thursday" @if($timeslot->day_of_week == 'Thursday') selected @endif >Четверг</option>
                                                <option value="Friday" @if($timeslot->day_of_week == 'Friday') selected @endif >Пятница</option>
                                                <option value="Saturday" @if($timeslot->day_of_week == 'Saturday') selected @endif >Суббота</option>
                                                <option value="Sunday" @if($timeslot->day_of_week == 'Sunday') selected @endif >Воскресенье</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label">Время</label>
                                            <input type="time" class="form-control"
                                                   name="timeslots[{{$key}}][start_time]" value="{{date('H:i', strtotime($timeslot->start_time))}}" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Цена</label>
                                            <input type="number" step="0.01" class="form-control"
                                                   name="timeslots[{{$key}}][price]"
                                                   placeholder="0 для базовой цены" value="{{$timeslot->price}}">
                                        </div>
                                        <input type="hidden" name="timeslots[{{$key}}][id]" value="{{$timeslot->id}}">
                                        <div class="d-flex align-items-center gap-2">
                                            <!-- другие элементы -->
                                            <a href="javascript:void()" class="remove-timeslot">
                                                <i class="feather-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
                                        <th class="text-center wd-450">Наименование</th>
                                        <th class="text-center wd-150">Цена</th>
                                        <th class="wd-1"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                     @foreach($quest->AdditionalServices as $aKey => $Service)
                                         <tr id="addr{{$aKey}}">
                                             <td>
                                                 <input type="hidden" name="additional_services[{{$aKey}}][id]" value="{{$Service->id}}">
                                                 <input type="text" name="additional_services[{{$aKey}}][name]"
                                                        placeholder="Введите наименование" class="form-control" value="{{$Service->name}}">
                                             </td>
                                             <td>
                                                 <input type="number" name="additional_services[{{$aKey}}][price]"
                                                        placeholder="Введите цену" class="form-control price"
                                                        step="1.00" min="1" value="{{$Service->price}}">
                                             </td>
                                             <td>
                                                 <a href="javascript:void(0)" class="remove-row">
                                                     <i class="feather-trash"></i>
                                                 </a>
                                             </td>
                                         </tr>
                                     @endforeach
                                         <tr id="addr{{$aKey+1}}"></tr>
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
</div>
@push('scripts')
    @vite('resources/js/dashboard/Pages/QuestEdit.js')
@endpush


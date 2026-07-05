<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-top-0">

                <div class="card-header p-0">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab"
                        role="tablist">
                        <li class="nav-item flex-fill border-top" role="presentation">
                            <a href="javascript:void(0);"
                               class="nav-link {{Route::currentRouteName() == 'seller.books.show' ? 'active':''}}"
                               data-bs-toggle="tab" data-bs-target="#bookDataTab" role="tab">Основная информация</a>
                        </li>
                        @if(isset($book))
                         <li class="nav-item flex-fill border-top" role="presentation">
                             <a href="javascript:void(0);"
                                class="nav-link {{Route::currentRouteName() == 'seller.books.chapters' ? 'active':''}}"
                                data-bs-toggle="tab" data-bs-target="#bookTextTab" role="tab">Текст</a>
                         </li>
                        @endif
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade {{Route::currentRouteName() == 'seller.books.show' ? 'show active':''}}"
                         id="bookDataTab" role="tabpanel">
                        <div class="card-body personal-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Основная информация о книге:</span>
                                </h5>
                                <form id="book_data" onsubmit="return false;">
                                    @csrf
                                    @if(isset($book))
                                        @method('PUT')
                                    @endif
                                    <input form="book_data" type="submit" class="btn btn-sm btn-light-brand"
                                           value="{{ isset($book) ? 'Обновить' : 'Создать' }}">
                                </form>
                            </div>

                            <div id="alert_book">
                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                            </div>

{{--                            <!-- Продавец -->--}}
{{--                            @if($currentUser->isAdmin())--}}
{{--                                <div class="row mb-4 align-items-center">--}}
{{--                                    <div class="col-lg-4">--}}
{{--                                        <label for="productSeller" class="fw-semibold">Продавец: <span--}}
{{--                                                class="text-danger">*</span></label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-lg-8">--}}
{{--                                        <div class="input-group">--}}
{{--                                            <div class="input-group-text"><i class="feather-user"></i></div>--}}
{{--                                            <select id="productSeller" class="form-select" name="seller_id" required>--}}
{{--                                                @foreach($sellers as $item)--}}
{{--                                                    @if(isset($book))--}}
{{--                                                        <option value="{{$item->id}}" {{$book->seller_id == $item->id ? 'selected' : ''}}>{{$item->name}}</option>--}}
{{--                                                    @else--}}
{{--                                                        <option value="{{$item->id}}" {{$currentUser->getSellerId() == $item->id ? 'selected' : ''}}>{{$item->name}}</option>--}}
{{--                                                    @endif--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            @else--}}
{{--                                <input type="hidden" name="productSeller" value="{{$currentUser->getSellerId()}}">--}}
{{--                            @endif--}}

                            <!-- Тип книги -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="bookType" class="fw-semibold">Тип книги: <span
                                            class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-type"></i></div>
                                        <select class="form-select" form="book_data" name="type" id="bookType" required>
                                            <option value="">Выберите тип...</option>
                                            <option
                                                value="ebook" {{ (isset($book) && $book->type == 'ebook') ? 'selected' : '' }}>
                                                Электронная книга
                                            </option>
                                            <option
                                                value="audiobook" {{ (isset($book) && $book->type == 'audiobook') ? 'selected' : '' }}>
                                                Аудиокнига
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!--Обложка-->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Обложка: </label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="mb-4 mb-md-0 d-flex gap-4 your-brand">
                                        <div
                                            class="wd-800 ht-1200 position-relative overflow-hidden border border-gray-2 rounded"
                                            style="width: 200px; height: 300px;">
                                            <!-- Превью обложки -->
                                            <div id="coverPreview" class="w-100 h-100 position-relative">
                                                @if(isset($book) && $book->getMainImage())
                                                    <img
                                                        src="{{ $book->getMainImage() }}"
                                                        class="upload-pic img-fluid rounded h-100 w-100"
                                                        alt="Обложка книги"
                                                        style="object-fit: cover;">
                                                @else
                                                    <!-- Заглушка обложки -->
                                                    <div
                                                        class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light position-relative"
                                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                        <!-- Декоративные линии -->
                                                        <div class="position-absolute top-0 start-0 w-100 h-100"
                                                             style="opacity: 0.1;">
                                                            <div
                                                                style="position: absolute; top: 10%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
                                                            <div
                                                                style="position: absolute; top: 30%; left: 15%; width: 70%; height: 1px; background: #fff;"></div>
                                                            <div
                                                                style="position: absolute; top: 50%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
                                                            <div
                                                                style="position: absolute; top: 70%; left: 15%; width: 70%; height: 1px; background: #fff;"></div>
                                                            <div
                                                                style="position: absolute; top: 90%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
                                                        </div>

                                                        <!-- Иконка книги -->
                                                        <i class="feather-book text-white mb-3"
                                                           style="font-size: 2rem; opacity: 0.8;"></i>

                                                        <!-- Название книги -->
                                                        <div id="coverTitle" class="text-white text-center px-3 fw-bold"
                                                             style="font-size: 14px; line-height: 1.3; max-height: 60px; overflow: hidden;">
                                                            {{ $book->name ?? 'Название книги' }}
                                                        </div>

                                                        <!-- Разделитель -->
                                                        <div
                                                            style="width: 40px; height: 2px; background: rgba(255,255,255,0.5); margin: 8px 0;"></div>

                                                        <!-- Автор -->
                                                        <div id="coverAuthor" class="text-white text-center px-3"
                                                             style="font-size: 11px; opacity: 0.9; max-height: 30px; overflow: hidden;">
                                                            {{ $book->author ?? 'Автор' }}
                                                        </div>

                                                        <!-- Нижний декор -->
                                                        <div class="position-absolute bottom-0 start-0 w-100 p-2">
                                                            <div
                                                                style="width: 100%; height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px;"></div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Кнопка загрузки (поверх всего) -->
                                                <div
                                                    class="position-absolute start-50 top-50 translate-middle upload-button"
                                                    style="cursor: pointer; z-index: 10;">
                                                    <div
                                                        class="bg-white rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                                        <i class="feather-camera text-primary" aria-hidden="true"></i>
                                                    </div>
                                                </div>

                                                <input
                                                    class="file-upload position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                                    type="file"
                                                    form="book_data"
                                                    name="image"
                                                    accept="image/png, image/jpeg, image/jpg"
                                                    style="cursor: pointer;">
                                            </div>
                                        </div>

                                        <div class="d-flex flex-column gap-1">
                                            <div class="fs-11 text-gray-500 mt-2"># Загрузите обложку книги</div>
                                            <div class="fs-11 text-gray-500"># Размер обложки 800×1200 px</div>
                                            <div class="fs-11 text-gray-500"># Максимальный размер файла 5 МБ</div>
                                            <div class="fs-11 text-gray-500"># Допустимые форматы: PNG, JPG, JPEG</div>

                                            @if(isset($book) && $book->image_path)
                                                <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                                        id="removeCover">
                                                    <i class="feather-trash-2 me-1"></i> Удалить обложку
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Автор -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="authorInput" class="fw-semibold">Автор: <span
                                            class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-user"></i></div>
                                        <input type="text" class="form-control" form="book_data" name="author"
                                               id="authorInput" placeholder="Автор книги"
                                               value="{{ $book->author ?? '' }}" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Название -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="authorInput" class="fw-semibold">Название: <span
                                            class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-book"></i></div>
                                        <input type="text" class="form-control" form="book_data" name="name"
                                               id="nameInput" placeholder="Название книги"
                                               value="{{ $book->name ?? '' }}" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Жанр -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="genreSelect" class="fw-semibold">Жанр:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-list"></i></div>
                                        <select class="form-select" form="book_data" name="genre_id" id="genreSelect">
                                            <option disabled {{ !isset($book) ? 'selected' : '' }}>-- Выберете жанр --
                                            </option>
                                            @foreach($genres as $genre)
                                                <option
                                                    value="{{$genre->id}}" {{ (isset($book) && $book->genre_id == $genre->id) ? 'selected' : '' }}>{{$genre->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Издательство -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="publisherInput" class="fw-semibold">Издательство:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-briefcase"></i></div>
                                        <input type="text" class="form-control" form="book_data" name="publisher"
                                               id="publisherInput" placeholder="Издательство"
                                               value="{{ $book->publisher ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Год издания -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="yearInput" class="fw-semibold">Год издания:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <input type="number" class="form-control" form="book_data"
                                               name="publication_year"
                                               id="yearInput" placeholder="Год издания"
                                               min="1000" max="{{ date('Y') }}"
                                               value="{{ $book->publication_year ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- ISBN -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="isbnInput" class="fw-semibold">ISBN:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <input type="text" class="form-control" form="book_data" name="isbn"
                                               id="isbnInput" placeholder="ISBN (13 цифр)"
                                               pattern="[0-9]{10,13}"
                                               value="{{ $book->isbn ?? '' }}">
                                    </div>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                          Международный стандартный книжный номер (10 или 13 цифр)
                                    </span>
                                </div>
                            </div>

                            <!-- Язык -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="languageSelect" class="fw-semibold">Язык:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-globe"></i></div>
                                        <select class="form-select" form="book_data" name="language"
                                                id="languageSelect">
                                            <option
                                                value="ru" {{ (isset($book) && $book->language == 'ru') ? 'selected' : '' }}>
                                                Русский
                                            </option>
                                            <option
                                                value="en" {{ (isset($book) && $book->language == 'en') ? 'selected' : '' }}>
                                                English
                                            </option>
                                            <option
                                                value="de" {{ (isset($book) && $book->language == 'de') ? 'selected' : '' }}>
                                                Deutsch
                                            </option>
                                            <option
                                                value="fr" {{ (isset($book) && $book->language == 'fr') ? 'selected' : '' }}>
                                                Français
                                            </option>
                                            <option
                                                value="es" {{ (isset($book) && $book->language == 'es') ? 'selected' : '' }}>
                                                Español
                                            </option>
                                            <option
                                                value="it" {{ (isset($book) && $book->language == 'it') ? 'selected' : '' }}>
                                                Italiano
                                            </option>
                                            <option
                                                value="zh" {{ (isset($book) && $book->language == 'zh') ? 'selected' : '' }}>
                                                中文
                                            </option>
                                            <option
                                                value="ja" {{ (isset($book) && $book->language == 'ja') ? 'selected' : '' }}>
                                                日本語
                                            </option>
                                            <option
                                                value="other" {{ (isset($book) && $book->language == 'other') ? 'selected' : '' }}>
                                                Другой
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Чтец (для аудиокниг) -->
                            <div class="row mb-4 align-items-center audiobook-field"
                                 style="{{ (isset($book) && $book->type == 'audiobook') ? '' : 'display: none;' }}">
                                <div class="col-lg-4">
                                    <label for="narratorInput" class="fw-semibold">Чтец:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-mic"></i></div>
                                        <input type="text" class="form-control" form="book_data" name="narrator"
                                               id="narratorInput" placeholder="Имя чтеца"
                                               value="{{ $book->narrator ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Аннотация -->
                            <div class="row mb-4 align-items-start">
                                <div class="col-lg-4">
                                    <label for="annotationInput" class="fw-semibold">Аннотация:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-file-text"></i></div>
                                        <textarea class="form-control" form="book_data" name="annotation"
                                                  id="annotationInput" placeholder="Краткое описание книги"
                                                  rows="4">{{ $book->annotation ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Статус книги -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="statusSelect" class="fw-semibold">Статус книги:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-info"></i></div>
                                        <select class="form-select" form="book_data" name="status" id="statusSelect">
                                            <option
                                                value="draft" {{ (isset($book) && $book->status == 'draft') ? 'selected' : '' }}>
                                                Черновик
                                            </option>
                                            <option
                                                value="complete" {{ (isset($book) && $book->status == 'complete') ? 'selected' : '' }}>
                                                Полный текст
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Статус модерации (только для админов) -->
                            @if(auth()->user()->isAdmin())
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="moderationSelect" class="fw-semibold">Модерация:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-shield"></i></div>
                                            <select class="form-select" form="book_data" name="moderation_status"
                                                    id="moderationSelect">
                                                <option
                                                    value="pending" {{ (isset($book) && $book->moderation_status == 'pending') ? 'selected' : '' }}>
                                                    На рассмотрении
                                                </option>
                                                <option
                                                    value="approved" {{ (isset($book) && $book->moderation_status == 'approved') ? 'selected' : '' }}>
                                                    Одобрена
                                                </option>
                                                <option
                                                    value="rejected" {{ (isset($book) && $book->moderation_status == 'rejected') ? 'selected' : '' }}>
                                                    Отклонена
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Активность книги -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Активность:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" form="book_data"
                                               name="is_active" id="activeSwitch" value="1"
                                            {{ (isset($book) && $book->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="activeSwitch">
                                            Книга активна и доступна для пользователей
                                        </label>
                                    </div>
                                </div>
                            </div>

                            @if(auth()->user()->sellers()->first()->getSalesStatus() !== null)
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="price" class="fw-semibold">Стоимость:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-dollar-sign"></i></div>
                                            <input class="form-control" form="book_data" type="text" name="price" id="price" placeholder="Стоимость"
                                                   value="{{ $book->price ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                    @if(isset($book))
                     <div class="tab-pane fade {{Route::currentRouteName() == 'seller.books.chapters' ? 'show active':''}}"
                        id="bookTextTab" role="tabpanel">
                        <div class="card-body personal-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Текст книги:</span>
                                </h5>
                                <a href="{{route('seller.books.chapter', ['bookId'=>$book->id, 'chapterId'=>'create'])}}"
                                   class="btn btn-sm btn-light-brand">Добавить</a>
                            </div>
                            <div class="row">
                                @foreach($book->chapters as $item)
                                    <div class="col-lg-12">
                                        <div
                                            class="px-4 py-2 mb-4 d-flex justify-content-between align-items-center border border-dashed border-gray-3 rounded-1">
                                            <div
                                                class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 w-75">
                                                <div class="text-dark fw-bold">
                                                    {{ $item->title }}
                                                </div>
                                                <div>
                                                    {!! $item->status == 'published'
                                                        ? '<span class="badge bg-primary">опубликовано</span>'
                                                        : '<span class="badge bg-secondary">черновик</span>' !!}
                                                </div>
                                                @php
                                                    $count = $item->duration;
                                                    $word = match (true) {
                                                        $count % 10 == 1 && $count % 100 != 11 => 'символ',
                                                        in_array($count % 10, [2, 3, 4]) && !in_array($count % 100, [12, 13, 14]) => 'символа',
                                                        default => 'символов'
                                                    };
                                                @endphp
                                                <div class="text-muted">
                                                    ({{ $count }} {{ $word }})
                                                </div>
                                            </div>

                                            <div class="hstack gap-3">
                                                <a href="/seller/books/{{$book->id}}/chapters/{{$item->id}}"><i
                                                        class="feather-edit"></i></a>
                                                <a href="javascript:void(0);"><i class="feather-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@push('scripts')
    <script>
        window.bookData = {
            bookId: {{ $book->id ?? 'null' }},
            isEditMode: {{ isset($book) ? 'true' : 'false' }}
        };
    </script>
    @vite('resources/js/dashboard/Pages/booksShow.js')
@endpush

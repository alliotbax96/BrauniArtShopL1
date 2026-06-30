<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-top-0">

                <div class="card-header p-0">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab"
                        role="tablist">
                        <li class="nav-item flex-fill border-top" role="presentation">
                            <a href="/seller/books/1"
                               class="nav-link">Основная информация</a>
                        </li>
                        <li class="nav-item flex-fill border-top" role="presentation">
                            <a href="/seller/books/1/chapters"
                               class="nav-link">Текст</a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active"
                         id="bookTextTabTab" role="tabpanel">
                        <div class="card-body personal-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">{{isset($chapter) ? 'Редактирование части' : 'Создание части'}}</span>
                                </h5>
                                <form id="chapter_text" onsubmit="return false;">
                                    @csrf
                                    @if(isset($chapter))
                                        @method('PUT')
                                    @endif
                                    <input form="chapter_text" type="submit" class="btn btn-sm btn-light-brand"
                                           value="{{ isset($chapter) ? 'Обновить' : 'Создать' }}">
                                </form>
                            </div>
                            <div id="alert_chapter" class="mb-4"></div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Заголовок</label>
                                <input form="chapter_text" name="title" class="form-control" type="text" {!! isset($chapter) ? 'value="'.$chapter->title.'"' : 'placeholder="Заголовок"' !!} ">
                            </div>
                            @if(isset($order))
                            <input form="chapter_text" type="hidden" name="order" value="{{$order}}">
                            @endif
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" form="book_data"
                                           name="is_active" id="activeSwitch" value="1"
                                        {{ (isset($chapter) && $chapter->isPublished()) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activeSwitch">
                                        Опубликовать
                                    </label>
                                    <input form="chapter_text" type="hidden" name="status">
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="row mb-2 justify-content-between">
                                    <div class="col-auto">
                                        <label class="form-label fw-bold">Текст</label>
                                    </div>
                                    <div class="col-auto">
                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Счетчик символов -->
                                            <div class="character-counter">
                                                <span class="character-count" id="charCount">0</span>
                                                <input form="chapter_text" type="hidden" name="duration" value="">
                                                <span class="character-limit">/ 200 000</span>
                                            </div>
                                            <!-- Кнопка типографирования -->
                                            <button type="button" class="btn btn-outline-secondary" id="typographBtn">
                                                <i class="fas fa-text-width me-2"></i>Оттипографить
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Редактор -->
                                <div id="editor-container" style="height: 400px; border: 1px solid #dee2e6; border-radius: 0.25rem;">
                                    {!! isset($chapter) ? $chapter->content : '' !!}
                                </div>
                                <!-- Скрытое поле для сохранения данных -->
                                <textarea form="chapter_text" name="content" id="content-textarea" style="display:none;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@push('scripts')
    <script>
        window.bookId = {{ $book->id }};
        @if(isset($chapter))
            window.chapterId = {{ $chapter->id }};
        @endif
    </script>
    @vite('resources/js/dashboard/Pages/chapterShow.js')
@endpush

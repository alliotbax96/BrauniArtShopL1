<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-top-0">

                <div class="card-header p-0">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab"
                        role="tablist">
                        <li class="nav-item flex-fill border-top" role="presentation">
                            <a href="/seller/books/{{ $book->id }}"
                               class="nav-link">Основная информация</a>
                        </li>
                        <li class="nav-item flex-fill border-top" role="presentation">
                            <a href="/seller/books/{{ $book->id }}/chapters"
                               class="nav-link">Содержание</a>
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
                                <input form="chapter_text" name="title" class="form-control" type="text"
                                {!! isset($chapter) ? 'value="'.$chapter->title.'"' : 'placeholder="Заголовок"' !!} ">
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

                            {{-- Электронная книга: текстовый редактор --}}
                            @if($book->isEbook())
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
                                                    <span class="character-limit">/ 200 000</span>
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
                                {{-- Аудиокнига: загрузка MP3 --}}
                            @elseif($book->isAudiobook())
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Аудиофайл (MP3)</label>
                                    <div class="input-group mb-3">
                                        <input type="file" form="chapter_text" name="audio_file" class="form-control"
                                               accept="audio/mpeg" id="audioFileInput">
                                        @if(isset($chapter) && $chapter->audio_file_path)
                                            <button type="button" class="btn btn-outline-danger" id="removeAudioBtn"
                                                    title="Удалить аудиофайл">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @if(isset($chapter) && $chapter->audio_file_path)
                                        <div class="mb-2">
                                            <small class="text-muted">Текущий файл: {{ basename($chapter->audio_file_path) }}</small>
                                        </div>
                                        <div class="audio-preview" id="existingAudioPreview">
                                            <audio controls class="w-100" id="audioPlayer">
                                                <source src="{{ $chapter->audio_url }}" type="audio/mpeg">
                                                Ваш браузер не поддерживает аудиоплеер.
                                            </audio>
                                        </div>
                                        <input type="hidden" name="existing_audio" value="{{ $chapter->audio_file_path }}">
                                    @else
                                        <div class="audio-preview" id="audioPreviewContainer" style="display:none;">
                                            <audio controls class="w-100" id="audioPlayerNew">
                                                <source src="" type="audio/mpeg">
                                                Ваш браузер не поддерживает аудиоплеер.
                                            </audio>
                                        </div>
                                    @endif
                                    <input form="chapter_text" type="hidden" name="duration" value="{{ $chapter->duration ?? '' }}">
                                    <input form="chapter_text" type="hidden" name="remove_audio" id="removeAudioFlag" value="0">
                                </div>
                            @endif
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
        window.bookType = '{{ $book->type }}';
        @if(isset($chapter))
            window.chapterId = {{ $chapter->id }};
        @endif
    </script>
    @vite('resources/js/dashboard/Pages/chapterShow.js')
@endpush

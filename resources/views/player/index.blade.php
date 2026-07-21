<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $book->name }} — Аудиоплеер</title>
    @vite(['resources/js/player.js'])
    <script src="https://kit.fontawesome.com/24a9d28c84.js" crossorigin="anonymous"></script>
</head>
<body class="player-body">
<div class="player-app" id="playerApp">
    <!-- Фоновое размытое изображение -->
    <div class="player-background" id="playerBackground"></div>

    <!-- Основной контент -->
    <div class="player-content">
        <!-- Шапка -->
        <header class="player-header">
            <a href="{{ route('books.show', $book->id) }}" class="btn-icon" aria-label="Назад">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="header-info">
                <h1 class="book-title">{{ $book->name }}</h1>
                <p class="chapter-subtitle" id="currentChapterLabel">Загрузка...</p>
            </div>
            <div class="header-actions">
                <button class="btn-icon" id="btnBookmarks" aria-label="Закладки">
                    <i class="fas fa-bookmark"></i>
                </button>
                <button class="btn-icon" id="btnChapters" aria-label="Список глав">
                    <i class="fas fa-list-ul"></i>
                </button>
            </div>
        </header>

        <!-- Обложка и визуализация -->
        <section class="player-artwork">
            <div class="artwork-container">
                @if($book->getMainImage())
                    <img src="{{ $book->getMainImage() }}" alt="Обложка" class="artwork-img" id="artworkImg">
                @else
                    <div class="artwork-placeholder">
                        <i class="fas fa-headphones"></i>
                    </div>
                @endif
            </div>
        </section>

        <!-- Прогресс и управление -->
        <section class="player-controls">
            <div class="progress-area">
                <div class="progress-bar-custom" id="progressBar">
                    <div class="progress-fill" id="progressFill"></div>
                    <div class="progress-thumb" id="progressThumb"></div>
                </div>
                <div class="time-row">
                    <span id="currentTime">0:00</span>
                    <span id="durationTime">0:00</span>
                </div>
            </div>

            <div class="main-buttons">
                <button class="ctrl-btn" id="btnPrev" aria-label="Предыдущая глава">
                    <i class="fas fa-backward-step"></i>
                </button>
                <button class="ctrl-btn" id="btnRewind" aria-label="Назад 10 сек">
                    <i class="fas fa-rotate-left"></i>
                    <span class="rewind-label">10</span>
                </button>
                <button class="ctrl-btn play-btn" id="btnPlay" aria-label="Воспроизведение">
                    <i class="fas fa-play"></i>
                </button>
                <button class="ctrl-btn" id="btnForward" aria-label="Вперёд 10 сек">
                    <span class="rewind-label">10</span>
                    <i class="fas fa-rotate-right"></i>
                </button>
                <button class="ctrl-btn" id="btnNext" aria-label="Следующая глава">
                    <i class="fas fa-forward-step"></i>
                </button>
            </div>

            <div class="extra-controls">
                <button class="btn-icon btn-bookmark" id="btnAddBookmark" aria-label="Добавить закладку">
                    <i class="far fa-bookmark"></i>
                </button>
                <div class="speed-selector">
                    <select id="speedSelect" class="speed-dropdown">
                        <option value="0.5">0.5x</option>
                        <option value="0.75">0.75x</option>
                        <option value="1" selected>1x</option>
                        <option value="1.25">1.25x</option>
                        <option value="1.5">1.5x</option>
                        <option value="2">2x</option>
                    </select>
                </div>
            </div>
        </section>
    </div>

    <!-- Панель списка глав (слайд снизу) -->
    <div class="slide-panel" id="chaptersPanel">
        <div class="panel-header">
            <h3>Содержание</h3>
            <button class="btn-icon" id="closeChapters"><i class="fas fa-times"></i></button>
        </div>
        <div class="panel-body" id="chaptersList">
            @foreach($chapters as $chapter)
                <div class="chapter-row" data-chapter-id="{{ $chapter->id }}" data-audio-url="{{ $chapter->audio_url }}">
                    <div class="chapter-info">
                        <span class="chapter-title">{{ $chapter->title }}</span>
                        <span class="chapter-duration">{{ $chapter->getFormattedDuration() }}</span>
                    </div>
                    <div class="chapter-status" id="chapterStatus{{ $chapter->id }}"></div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Панель закладок -->
    <div class="slide-panel" id="bookmarksPanel">
        <div class="panel-header">
            <h3>Закладки</h3>
            <button class="btn-icon" id="closeBookmarks"><i class="fas fa-times"></i></button>
        </div>
        <div class="panel-body" id="bookmarksList">
            <p class="empty-message">Загрузка...</p>
        </div>
    </div>
</div>

<audio id="audioPlayer" preload="auto"></audio>

<script>
    window.bookId = {{ $book->id }};
    window.startChapterId = {{ $startChapterId ?? 'null' }};
    window.startPosition = {{ $startPosition }};
    window.playbackSpeed = {{ $playbackSpeed }};
</script>
</body>
</html>

import '../css/player.css';
import './bootstrap'

$(document).ready(function() {
    // Глобальная настройка CSRF для всех AJAX-запросов
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const audio = document.getElementById('audioPlayer');
    const app = document.getElementById('playerApp');
    const playBtn = document.getElementById('btnPlay');
    const progressBar = document.getElementById('progressBar');
    const progressFill = document.getElementById('progressFill');
    const progressThumb = document.getElementById('progressThumb');
    const currentTimeSpan = document.getElementById('currentTime');
    const durationSpan = document.getElementById('durationTime');
    const speedSelect = document.getElementById('speedSelect');
    const chapterLabel = document.getElementById('currentChapterLabel');
    const artworkImg = document.getElementById('artworkImg');
    const background = document.getElementById('playerBackground');

    let chapters = [];
    let currentChapterIndex = 0;
    let isPlaying = false;
    let saveInterval;

    // Сбор данных о главах
    $('#chaptersList .chapter-row').each(function(index) {
        chapters.push({
            id: $(this).data('chapter-id'),
            title: $(this).find('.chapter-title').text(),
            url: $(this).data('audio-url')
        });
        if (chapters[index].id == window.startChapterId) {
            currentChapterIndex = index;
        }
    });

    function loadChapter(index, startPos = 0) {
        if (index < 0 || index >= chapters.length) return;
        currentChapterIndex = index;
        const ch = chapters[index];
        audio.src = ch.url;
        audio.currentTime = startPos;
        chapterLabel.textContent = ch.title;
        updateChaptersHighlight();
        if (isPlaying) {
            audio.play().catch(e => console.log(e));
        }
    }

    function updateChaptersHighlight() {
        $('.chapter-row').removeClass('active');
        $(`.chapter-row[data-chapter-id="${chapters[currentChapterIndex].id}"]`).addClass('active');
    }

    // Инициализация
    loadChapter(currentChapterIndex, window.startPosition);
    audio.playbackRate = window.playbackSpeed;
    speedSelect.value = window.playbackSpeed;
    updateChaptersHighlight();

    // Воспроизведение/пауза
    function togglePlay() {
        if (audio.paused) {
            audio.play().catch(() => {});
        } else {
            audio.pause();
        }
    }

    playBtn.addEventListener('click', togglePlay);
    audio.addEventListener('play', () => {
        isPlaying = true;
        playBtn.innerHTML = '<i class="fas fa-pause"></i>';
        app.classList.add('is-playing');
    });
    audio.addEventListener('pause', () => {
        isPlaying = false;
        playBtn.innerHTML = '<i class="fas fa-play"></i>';
        app.classList.remove('is-playing');
    });
    audio.addEventListener('ended', () => {
        if (currentChapterIndex < chapters.length - 1) {
            loadChapter(currentChapterIndex + 1, 0);
            if (!audio.paused) audio.play().catch(() => {});
        } else {
            isPlaying = false;
            playBtn.innerHTML = '<i class="fas fa-play"></i>';
            app.classList.remove('is-playing');
        }
    });

    // Прогресс
    audio.addEventListener('timeupdate', updateProgressUI);
    audio.addEventListener('loadedmetadata', () => {
        durationSpan.textContent = formatTime(audio.duration);
        updateProgressUI();
    });

    function updateProgressUI() {
        const current = audio.currentTime || 0;
        const duration = audio.duration || 0;
        const percent = duration ? (current / duration) * 100 : 0;
        progressFill.style.width = percent + '%';
        progressThumb.style.left = percent + '%';
        currentTimeSpan.textContent = formatTime(current);
        if (duration) durationSpan.textContent = formatTime(duration);
    }

    // Перемотка по прогресс-бару
    progressBar.addEventListener('click', function(e) {
        const rect = progressBar.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const width = rect.width;
        const duration = audio.duration || 0;
        if (duration) {
            const seekTime = (clickX / width) * duration;
            audio.currentTime = seekTime;
        }
    });

    // Перемотка кнопками
    document.getElementById('btnRewind').addEventListener('click', () => {
        audio.currentTime = Math.max(0, audio.currentTime - 10);
    });
    document.getElementById('btnForward').addEventListener('click', () => {
        audio.currentTime = Math.min(audio.duration || 0, audio.currentTime + 10);
    });

    // Переключение глав
    document.getElementById('btnPrev').addEventListener('click', () => {
        if (audio.currentTime > 3) {
            audio.currentTime = 0;
        } else {
            loadChapter(currentChapterIndex - 1, 0);
            if (isPlaying) audio.play().catch(() => {});
        }
    });
    document.getElementById('btnNext').addEventListener('click', () => {
        loadChapter(currentChapterIndex + 1, 0);
        if (isPlaying) audio.play().catch(() => {});
    });

    // Клик по главе в панели
    $('#chaptersList').on('click', '.chapter-row', function() {
        const index = $(this).index();
        loadChapter(index, 0);
        if (isPlaying) audio.play().catch(() => {});
        closePanel('chaptersPanel');
    });

    // Скорость
    speedSelect.addEventListener('change', () => {
        audio.playbackRate = parseFloat(speedSelect.value);
        saveProgressNow();
    });

    // Панели (слайд)
    function openPanel(panelId) {
        closeAllPanels();
        document.getElementById(panelId).classList.add('open');
    }
    function closePanel(panelId) {
        document.getElementById(panelId).classList.remove('open');
    }
    function closeAllPanels() {
        document.querySelectorAll('.slide-panel').forEach(p => p.classList.remove('open'));
    }

    document.getElementById('btnChapters').addEventListener('click', () => openPanel('chaptersPanel'));
    document.getElementById('closeChapters').addEventListener('click', () => closePanel('chaptersPanel'));

    document.getElementById('btnBookmarks').addEventListener('click', () => {
        openPanel('bookmarksPanel');
        loadBookmarks();
    });
    document.getElementById('closeBookmarks').addEventListener('click', () => closePanel('bookmarksPanel'));

    // Закладки
    document.getElementById('btnAddBookmark').addEventListener('click', () => {
        const chId = chapters[currentChapterIndex].id;
        const pos = Math.floor(audio.currentTime);
        const note = prompt('Заметка (необязательно):');
        $.ajax({
            url: `/books/${window.bookId}/bookmarks`,
            method: 'POST',
            data: { chapter_id: chId, position: pos, note: note },
            success: function(res) {
                if (res.status === 'created') {
                    alert('Закладка добавлена');
                } else {
                    alert('Закладка удалена');
                }
                loadBookmarks();
            }
        });
    });

    function loadBookmarks() {
        $.get(`/books/${window.bookId}/bookmarks`, function(data) {
            let html = '';
            if (data.length === 0) {
                html = '<p class="empty-message">Нет закладок</p>';
            } else {
                data.forEach(bm => {
                    html += `<div class="chapter-row" data-chapter-id="${bm.chapter_id}" data-position="${bm.audio_position}">
                                <div class="chapter-info">
                                    <span class="chapter-title">${bm.chapter?.title || 'Глава'}</span>
                                    <span class="chapter-duration">${formatTime(bm.audio_position)}</span>
                                    ${bm.note ? `<small class="bookmark-note">${bm.note}</small>` : ''}
                                </div>
                             </div>`;
                });
            }
            $('#bookmarksList').html(html);
        });
    }

    $('#bookmarksList').on('click', '.chapter-row', function() {
        const chapterId = $(this).data('chapter-id');
        const pos = $(this).data('position') || 0;
        const idx = chapters.findIndex(ch => ch.id == chapterId);
        if (idx !== -1) {
            loadChapter(idx, pos);
            if (isPlaying) audio.play().catch(() => {});
        }
        closePanel('bookmarksPanel');
    });

    // Сохранение прогресса
    function saveProgressNow() {
        const chId = chapters[currentChapterIndex].id;
        const pos = Math.floor(audio.currentTime);
        const speed = audio.playbackRate;
        $.post(`/books/${window.bookId}/progress`, {
            chapter_id: chId,
            position: pos,
            playback_speed: speed
        });
    }

    saveInterval = setInterval(saveProgressNow, 5000);
    window.addEventListener('beforeunload', saveProgressNow);

    // Визуальная атмосфера (размытый фон на основе обложки)
    if (artworkImg && artworkImg.src) {
        background.style.backgroundImage = `url(${artworkImg.src})`;
        background.classList.add('active');
    }

    // Форматирование времени
    function formatTime(sec) {
        const mins = Math.floor(sec / 60);
        const secs = Math.floor(sec % 60);
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }
});

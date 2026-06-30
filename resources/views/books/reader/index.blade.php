<!-- resources/views/reader/index.blade.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Читалка - {{ $book->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --bg-color: #faf9f6;
            --text-color: #2c2c2c;
            --accent-color: #4a90e2;
            --border-color: #e0e0e0;
            --sidebar-bg: #f0efe9;
            --font-family: 'Georgia', 'Times New Roman', serif;
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #e0e0e0;
            --accent-color: #6ba4e7;
            --border-color: #333333;
            --sidebar-bg: #242424;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
            height: 100vh;
            overflow: hidden;
        }

        .reader-container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 300px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            z-index: 10;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-header h2 {
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: normal;
        }

        .sidebar-header .author {
            font-size: 14px;
            opacity: 0.7;
        }

        .chapters-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
        }

        .chapter-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: background-color 0.2s;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chapter-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        [data-theme="dark"] .chapter-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .chapter-item.active {
            background-color: var(--accent-color);
            color: white;
            border-left-color: var(--accent-color);
        }

        .chapter-item .chapter-title {
            flex: 1;
            font-size: 14px;
        }

        .chapter-item .chapter-status {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--accent-color);
            display: none;
        }

        .chapter-item.read .chapter-status {
            display: block;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .toolbar {
            padding: 10px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .toolbar-button {
            background: none;
            border: 1px solid var(--border-color);
            padding: 8px 15px;
            cursor: pointer;
            color: var(--text-color);
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .toolbar-button:hover {
            background-color: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
        }

        .toolbar-center {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .font-size-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .font-size-button {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-color);
            font-size: 18px;
            padding: 5px;
        }

        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 40px 60px;
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
        }

        .chapter-content {
            font-size: 16px;
            line-height: 1.8;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .chapter-content h1 {
            font-size: 24px;
            margin-bottom: 30px;
            font-weight: normal;
            text-align: center;
        }

        .chapter-content p {
            margin-bottom: 20px;
            text-indent: 30px;
        }

        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background-color: var(--accent-color);
            transition: width 0.3s;
            z-index: 100;
        }

        .bookmark-indicator {
            position: absolute;
            left: -20px;
            color: var(--accent-color);
            font-size: 20px;
            transition: opacity 0.3s;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 20;
            }

            .content-area {
                padding: 20px;
            }
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--accent-color);
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s;
            z-index: 1000;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
<div class="progress-bar" id="progressBar"></div>

<div class="reader-container">
    <!-- Sidebar -->
    <aside class="sidebar hidden" id="sidebar">
        <div class="sidebar-header">
            <h2>{{ $book->name }}</h2>
            <div class="author">{{ $book->author }}</div>
        </div>
        <div class="chapters-list" id="chaptersList">
            <!-- Главы будут загружены через JavaScript -->
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="toolbar">
            <button class="toolbar-button" id="toggleSidebar">☰ Содержание</button>

            <div class="toolbar-center">
                <div class="font-size-control">
                    <button class="font-size-button" id="decreaseFont">A-</button>
                    <span style="font-size: 14px;">Aa</span>
                    <button class="font-size-button" id="increaseFont">A+</button>
                </div>

                <button class="toolbar-button" id="toggleTheme">🌙 Тема</button>
                <button class="toolbar-button" id="addBookmark">🔖 Закладка</button>
            </div>

            <div style="display: flex; gap: 10px;">
                <button class="toolbar-button" id="prevChapter">← Назад</button>
                <button class="toolbar-button" id="nextChapter">Вперед →</button>
            </div>
        </div>

        <div class="content-area" id="contentArea">
            <div class="chapter-content" id="chapterContent">
                <!-- Контент главы -->
            </div>
        </div>
    </main>
</div>

<div class="toast" id="toast"></div>

<script>
    class BookReader {
        constructor() {
            this.bookId = {{ $book->id }};
            this.currentChapter = null;
            this.bookData = null;
            this.fontSize = 16;
            this.readingTimer = null;
            this.readingTime = 0;
            this.bookmark = null;

            this.init();
        }

        async init() {
            await this.loadBookData();
            this.setupEventListeners();
            this.restoreReadingPosition();
            this.startReadingTimer();
        }

        async loadBookData() {
            try {
                const response = await fetch(`/api/books/${this.bookId}/info`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Expected JSON, but got:', text.substring(0, 200));
                    throw new Error('Server returned non-JSON data');
                }

                const data = await response.json();
                this.bookData = data.book;
                this.bookmark = data.bookmark;

                this.renderChapters();

                const chapterId = this.bookmark?.chapter_id || this.bookData.chapters[0]?.id;
                if (chapterId) {
                    await this.loadChapter(chapterId);
                }
            } catch (error) {
                console.error('Error loading book data:', error);
            }
        }


        renderChapters() {
            const chaptersList = document.getElementById('chaptersList');
            chaptersList.innerHTML = this.bookData.chapters.map((chapter, index) => `
                    <div class="chapter-item ${this.currentChapter?.id === chapter.id ? 'active' : ''} ${this.bookmark?.chapter_id === chapter.id ? 'read' : ''}"
                         data-chapter-id="${chapter.id}">
                        <span class="chapter-title">${index + 1}. ${chapter.title}</span>
                        <span class="chapter-status"></span>
                    </div>
                `).join('');

            // Добавляем обработчики кликов
            chaptersList.querySelectorAll('.chapter-item').forEach(item => {
                item.addEventListener('click', () => {
                    const chapterId = item.dataset.chapterId;
                    this.loadChapter(chapterId);
                });
            });
        }

        async loadChapter(chapterId) {
            try {
                const response = await fetch(`/api/books/${this.bookId}/chapters/${chapterId}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();

                // Обновляем текущую главу
                this.currentChapter = data.chapter;

                // Отображаем контент главы
                document.getElementById('chapterContent').innerHTML = data.chapter.content;

                // Обновляем активные классы в списке глав
                this.updateChapterListActive();

                // Восстанавливаем позицию прокрутки, если есть закладка
                if (this.bookmark && this.bookmark.chapter_id === chapterId) {
                    this.restoreScrollPosition();
                }

                // Сбрасываем прогресс бар
                this.updateProgressBar();

                this.showToast(`Глава "${data.chapter.title}" загружена`);

            } catch (error) {
                console.error('Error loading chapter:', error);
                this.showToast('Ошибка загрузки главы');
            }
        }

        updateChapterListActive() {
            const chapterItems = document.querySelectorAll('.chapter-item');
            chapterItems.forEach(item => {
                item.classList.remove('active');
                if (item.dataset.chapterId == this.currentChapter.id) {
                    item.classList.add('active');
                }
            });
        }

        restoreScrollPosition() {
            const contentArea = document.getElementById('contentArea');
            if (this.bookmark.scroll_position) {
                contentArea.scrollTop = this.bookmark.scroll_position;
            } else if (this.bookmark.position) {
                const content = document.getElementById('chapterContent').textContent;
                const totalChars = content.length;
                const scrollPosition = (this.bookmark.position / totalChars) * contentArea.scrollHeight;
                contentArea.scrollTop = scrollPosition;
            }
        }



        async saveBookmark() {
            if (!this.currentChapter) return;

            const contentArea = document.getElementById('contentArea');
            const bookmark = {
                book_id: this.bookId,
                chapter_id: this.currentChapter.id,
                position: this.getCurrentPosition(),
                scroll_position: contentArea.scrollTop,
            };

            try {
                await fetch('/api/bookmarks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',

                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(bookmark)
                });
                this.showToast('Закладка сохранена');
            } catch (error) {
                console.error('Error saving bookmark:', error);
            }
        }


        getCurrentPosition() {
            const content = document.getElementById('chapterContent').textContent;
            const scrolledChars = Math.floor(
                (document.getElementById('contentArea').scrollTop /
                    document.getElementById('contentArea').scrollHeight) * content.length
            );
            return scrolledChars;
        }

        async restoreReadingPosition() {
            if (this.bookmark) {
                const chapterId = this.bookmark.chapter_id;
                if (chapterId) {
                    await this.loadChapter(chapterId);
                }
            }
        }

        startReadingTimer() {
            this.readingTimer = setInterval(() => {
                this.readingTime += 10; // Увеличиваем на 10 секунд

                // Отправляем прогресс каждые 30 секунд
                if (this.readingTime % 30 === 0) {
                    this.updateProgress();
                }
            }, 10000); // Каждые 10 секунд
        }

        async updateProgress() {
            try {
                await fetch('/api/reading-progress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',

                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        book_id: this.bookId,
                        reading_time: this.readingTime
                    })
                });
            } catch (error) {
                console.error('Error updating progress:', error);
            }
        }


        updateProgressBar() {
            const contentArea = document.getElementById('contentArea');
            const scrollPercentage = (contentArea.scrollTop / (contentArea.scrollHeight - contentArea.clientHeight)) * 100;
            document.getElementById('progressBar').style.width = `${Math.min(scrollPercentage, 100)}%`;
        }

        changeFontSize(delta) {
            this.fontSize = Math.max(12, Math.min(24, this.fontSize + delta));
            document.getElementById('chapterContent').style.fontSize = `${this.fontSize}px`;
        }

        toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? '' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            document.getElementById('toggleTheme').textContent = newTheme === 'dark' ? '☀️ Тема' : '🌙 Тема';
        }

        showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2000);
        }

        setupEventListeners() {
            // Навигация
            document.getElementById('prevChapter').addEventListener('click', () => {
                if (this.prevChapterId) this.loadChapter(this.prevChapterId);
            });

            document.getElementById('nextChapter').addEventListener('click', () => {
                if (this.nextChapterId) this.loadChapter(this.nextChapterId);
            });

            // Sidebar
            document.getElementById('toggleSidebar').addEventListener('click', () => {
                document.getElementById('sidebar').classList.toggle('hidden');
            });

            // Тема
            document.getElementById('toggleTheme').addEventListener('click', () => {
                this.toggleTheme();
            });

            // Закладка
            document.getElementById('addBookmark').addEventListener('click', () => {
                this.saveBookmark();
            });

            // Размер шрифта
            document.getElementById('increaseFont').addEventListener('click', () => {
                this.changeFontSize(1);
            });

            document.getElementById('decreaseFont').addEventListener('click', () => {
                this.changeFontSize(-1);
            });

            // Прогресс бар
            document.getElementById('contentArea').addEventListener('scroll', () => {
                this.updateProgressBar();
            });

            // Автосохранение позиции при уходе
            window.addEventListener('beforeunload', () => {
                this.saveBookmark();
                this.updateProgress();
            });

            // Клавиатурные сокращения
            document.addEventListener('keydown', (e) => {
                switch (e.key) {
                    case 'ArrowLeft':
                        if (this.prevChapterId) this.loadChapter(this.prevChapterId);
                        break;
                    case 'ArrowRight':
                        if (this.nextChapterId) this.loadChapter(this.nextChapterId);
                        break;
                    case 'b':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.saveBookmark();
                        }
                        break;
                }
            });
        }
    }

    // Инициализация читалки
    document.addEventListener('DOMContentLoaded', () => {
        new BookReader();
    });
</script>
</body>
</html>

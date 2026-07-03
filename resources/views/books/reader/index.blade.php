<!-- resources/views/reader/index.blade.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Читалка - {{ $book->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Фавиконы -->
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/icons/favicon-16x16.png">
    <link rel="manifest" href="/assets/img/icons/site.webmanifest">
    <link rel="mask-icon" href="/assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/assets/img/icons/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="/assets/img/icons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <style>
        :root {
            --bg-color: #faf9f6;
            --text-color: #2c2c2c;
            --accent-color: #4a90e2;
            --border-color: #e0e0e0;
            --sidebar-bg: #f0efe9;
            --font-family: 'Georgia', 'Times New Roman', serif;
            --sidebar-width: 300px;
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
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        .reader-container {
            display: flex;
            height: 100vh;
            position: relative;
            width: 100%;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 20;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .sidebar.closed {
            width: 0;
            min-width: 0;
            max-width: 0;
            border-right: none;
            opacity: 0;
            visibility: hidden;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
            min-width: var(--sidebar-width);
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
            overflow-x: hidden;
            padding: 10px 0;
            min-width: var(--sidebar-width);
        }

        .chapter-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: background-color 0.2s;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            white-space: nowrap;
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
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .toolbar {
            padding: 10px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
            min-height: 50px;
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
            white-space: nowrap;
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
            width: 100%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Контейнер для центрирования контента */
        .content-wrapper {
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Когда сайдбар открыт - контент слегка смещен вправо */
        .content-wrapper.sidebar-open {
            margin-left: 0;
            margin-right: auto;
        }

        /* Когда сайдбар закрыт - контент по центру */
        .content-wrapper.sidebar-closed {
            margin: 0 auto;
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
            z-index: 1000;
        }

        /* Свайп-индикатор */
        .swipe-indicator {
            display: none;
            position: fixed;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60px;
            background: var(--accent-color);
            opacity: 0.2;
            border-radius: 0 4px 4px 0;
            z-index: 15;
            transition: all 0.3s;
        }

        .sidebar.closed ~ .swipe-indicator {
            display: block;
        }

        @media (min-width: 769px) {
            .sidebar.closed ~ .swipe-indicator {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 20;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }

            .sidebar.closed {
                transform: translateX(-100%);
                opacity: 1;
                visibility: visible;
                width: var(--sidebar-width);
                min-width: var(--sidebar-width);
                max-width: var(--sidebar-width);
                border-right: 1px solid var(--border-color);
            }

            .content-area {
                padding: 20px;
            }

            .swipe-indicator {
                display: block;
            }

            .toolbar {
                padding: 10px;
                gap: 10px;
            }

            .toolbar-center {
                gap: 10px;
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
            pointer-events: none;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Оверлей для мобильной версии */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 19;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }
    </style>
</head>
<body>
<div class="progress-bar" id="progressBar"></div>
<div class="swipe-indicator" id="swipeIndicator"></div>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="reader-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>{{ $book->name }}</h2>
            <div class="author">{{ $book->author }}</div>
        </div>
        <div class="chapters-list" id="chaptersList">
            <!-- Главы будут загружены через JavaScript -->
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
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
            <div class="content-wrapper" id="contentWrapper">
                <div class="chapter-content" id="chapterContent">
                    <!-- Контент главы -->
                </div>
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
            this.isSidebarOpen = true;

            // Для отслеживания свайпа
            this.touchStartX = 0;
            this.touchStartY = 0;
            this.swipeThreshold = 50;

            this.init();
        }

        async init() {
            await this.loadBookData();
            this.setupEventListeners();
            this.setupSwipeGestures();
            this.restoreReadingPosition();
            this.startReadingTimer();

            // На мобильных устройствах по умолчанию закрываем сайдбар
            if (window.innerWidth <= 768) {
                this.toggleSidebar(false);
            }
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
                this.showToast('Ошибка загрузки данных книги');
            }
        }

        get prevChapterId() {
            if (!this.currentChapter || !this.bookData?.chapters) return null;

            const currentIndex = this.bookData.chapters.findIndex(
                chapter => chapter.id === this.currentChapter.id
            );

            if (currentIndex > 0) {
                return this.bookData.chapters[currentIndex - 1].id;
            }
            return null;
        }

        get nextChapterId() {
            if (!this.currentChapter || !this.bookData?.chapters) return null;

            const currentIndex = this.bookData.chapters.findIndex(
                chapter => chapter.id === this.currentChapter.id
            );

            if (currentIndex < this.bookData.chapters.length - 1) {
                return this.bookData.chapters[currentIndex + 1].id;
            }
            return null;
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

            chaptersList.querySelectorAll('.chapter-item').forEach(item => {
                item.addEventListener('click', () => {
                    const chapterId = item.dataset.chapterId;
                    this.loadChapter(chapterId);

                    // На мобильных устройствах закрываем сайдбар после выбора главы
                    if (window.innerWidth <= 768) {
                        this.toggleSidebar(false);
                    }
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

                this.currentChapter = data.chapter;

                document.getElementById('chapterContent').innerHTML = data.chapter.content;

                this.updateChapterListActive();

                if (this.bookmark && this.bookmark.chapter_id === chapterId) {
                    setTimeout(() => this.restoreScrollPosition(), 100);
                }

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
            const contentArea = document.getElementById('contentArea');
            if (content.length === 0) return 0;

            const scrolledChars = Math.floor(
                (contentArea.scrollTop / (contentArea.scrollHeight - contentArea.clientHeight)) * content.length
            );
            return Math.max(0, scrolledChars);
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
                this.readingTime += 10;

                if (this.readingTime % 30 === 0) {
                    this.updateProgress();
                }
            }, 10000);
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

        toggleSidebar(show = null) {
            const sidebar = document.getElementById('sidebar');
            const contentWrapper = document.getElementById('contentWrapper');
            const overlay = document.getElementById('sidebarOverlay');

            if (show === null) {
                // Переключаем
                this.isSidebarOpen = !this.isSidebarOpen;
            } else {
                this.isSidebarOpen = show;
            }

            if (this.isSidebarOpen) {
                sidebar.classList.remove('closed');
                contentWrapper.classList.remove('sidebar-closed');
                contentWrapper.classList.add('sidebar-open');
                if (window.innerWidth <= 768) {
                    overlay.classList.add('active');
                }
            } else {
                sidebar.classList.add('closed');
                contentWrapper.classList.remove('sidebar-open');
                contentWrapper.classList.add('sidebar-closed');
                overlay.classList.remove('active');
            }

            // Обновляем кнопку
            document.getElementById('toggleSidebar').textContent =
                this.isSidebarOpen ? '☰ Содержание' : '☰ Показать';
        }

        showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2000);
        }

        setupSwipeGestures() {
            // Свайп на основной области контента
            const mainContent = document.getElementById('mainContent');

            mainContent.addEventListener('touchstart', (e) => {
                this.touchStartX = e.touches[0].clientX;
                this.touchStartY = e.touches[0].clientY;
            }, { passive: true });

            mainContent.addEventListener('touchend', (e) => {
                const touchEndX = e.changedTouches[0].clientX;
                const touchEndY = e.changedTouches[0].clientY;

                const deltaX = touchEndX - this.touchStartX;
                const deltaY = touchEndY - this.touchStartY;

                // Проверяем, что это горизонтальный свайп
                if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > this.swipeThreshold) {
                    if (deltaX > 0 && !this.isSidebarOpen) {
                        // Свайп вправо - открываем сайдбар
                        this.toggleSidebar(true);
                        this.showToast('Содержание открыто');
                    } else if (deltaX < 0 && this.isSidebarOpen) {
                        // Свайп влево - закрываем сайдбар
                        this.toggleSidebar(false);
                        this.showToast('Содержание скрыто');
                    }
                }
            });

            // Закрытие сайдбара по клику на оверлей
            document.getElementById('sidebarOverlay').addEventListener('click', () => {
                this.toggleSidebar(false);
            });
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
                this.toggleSidebar();
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
                    case 'm':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.toggleSidebar();
                        }
                        break;
                }
            });

            // Адаптация при изменении размера окна
            window.addEventListener('resize', () => {
                if (window.innerWidth <= 768 && this.isSidebarOpen) {
                    this.toggleSidebar(false);
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

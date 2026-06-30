import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import '../../../css/dashboard/customQuill.css';
import { handleChapterFormSubmit } from '../scripts/books/formChapterSubmitHelper.js';

// Максимальное количество символов
const MAX_CHARS = 200000;

// Функция типографирования текста
function typographText(text) {
    if (!text) return text;

    let result = text;

    // Замена кавычек
    result = result.replace(/(^|\s)"(\S)/g, '$1«$2');
    result = result.replace(/(\S)"(\s|$)/g, '$1»$2');

    // Замена двойного минуса на тире
    result = result.replace(/--/g, '—');

    // Замена одиночного минуса между пробелами на тире
    result = result.replace(/\s-\s/g, ' — ');

    // Неразрывные пробелы с предлогами
    const shortWords = ['в', 'и', 'к', 'с', 'у', 'а', 'о', 'из', 'за', 'до', 'по', 'на', 'не', 'ни', 'но', 'от', 'то', 'же', 'бы', 'ли'];
    shortWords.forEach(word => {
        const regex = new RegExp(`(\\s+)(${word}\\s+)`, 'gi');
        result = result.replace(regex, '$1$2\u00A0');
    });

    // Замена троеточия
    result = result.replace(/\.{3,}/g, '…');

    // Удаление двойных пробелов
    result = result.replace(/\s{2,}/g, ' ');

    return result;
}

// Функция подсчёта символов (без учёта HTML тегов)
function countCharacters(editor) {
    const text = editor.getText();
    // Удаляем все пробельные символы в начале и конце, а также лишние переносы строк
    return text.replace(/\s+/g, ' ').trim().length;
}

// Функция обновления счётчика с использованием jQuery
function updateCharacterCount(editor, counterElement) {
    const charCount = countCharacters(editor);

    // Обновляем текст счётчика с форматированием чисел для русского языка
    $(counterElement).text(charCount.toLocaleString('ru-RU'));
    $('input[name="duration"]').val(charCount);

    // Получаем контейнер счётчика через jQuery
    const $counterContainer = $(counterElement).closest('.character-counter');

    // Добавляем класс предупреждения при приближении к лимиту
    if (charCount > MAX_CHARS * 0.9) {
        $counterContainer.addClass('text-warning');
        if (charCount >= MAX_CHARS) {
            $counterContainer.addClass('text-danger');
            $counterContainer.removeClass('text-warning');
        }
    } else {
        $counterContainer.removeClass('text-warning text-danger');
    }
}


// Инициализация редактора
document.addEventListener('DOMContentLoaded', function() {
    const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'script': 'sub'}, { 'script': 'super' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'font': [] }],
        [{ 'align': [] }],
        ['clean']
    ];

    const quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: toolbarOptions
        },
        placeholder: 'Введите текст главы...'
    });

    // Получаем элемент счетчика
    const charCountElement = document.getElementById('charCount');

    // Установка начального содержимого
    const initialContent = document.querySelector('#editor-container').innerHTML;
    if (initialContent && initialContent.trim()) {
        quill.clipboard.dangerouslyPasteHTML(initialContent);
    }

    // Обновляем счетчик после загрузки контента
    updateCharacterCount(quill, charCountElement);

    // Функция типографирования
    document.getElementById('typographBtn').addEventListener('click', function() {
        const currentContent = quill.getText();
        const selection = quill.getSelection();

        if (currentContent.trim()) {
            // Если есть выделенный текст - типографируем только его
            if (selection && selection.length > 0) {
                const selectedText = quill.getText(selection.index, selection.length);
                const typographedText = typographText(selectedText);

                // Удаляем выделенный текст и вставляем оттипографированный
                quill.deleteText(selection.index, selection.length);
                quill.insertText(selection.index, typographedText);
            } else {
                // Типографируем весь текст
                const typographedText = typographText(currentContent);
                quill.setText(typographedText);
            }
        }
    });

    // Обработчик изменений в редакторе
    quill.on('text-change', function(delta, oldDelta, source) {
        // Обновляем счетчик символов
        updateCharacterCount(quill, charCountElement);

        // Сохраняем HTML контент в textarea при изменении
        const html = quill.root.innerHTML;
        const textarea = document.getElementById('content-textarea');
        if (textarea) {
            textarea.value = html;
        }

        // Блокируем ввод при превышении лимита
        const currentCharCount = countCharacters(quill);
        if (currentCharCount >= MAX_CHARS && source === 'user') {
            // Отменяем последнее изменение если превышен лимит
            quill.history.undo();
        }
    });

    // Начальная синхронизация
    const html = quill.root.innerHTML;
    const textarea = document.getElementById('content-textarea');
    if (textarea) {
        textarea.value = html;
    }
});

$(document).ready(function() {
    const $form = $('#chapter_text');
    const $submitBtn = $form.find('input[type="submit"]');
    const originalBtnText = $submitBtn.val();

    // Данные о текущей главе (заполняются на основе PHP‑переменных)
    const chapterData = {
        isEditMode: !!window.chapterId, // true, если редактируем существующую главу
        bookId: window.bookId, // ID книги (передаётся из Blade)
        chapterId: window.chapterId // ID главы (если редактируем)
    };

    // Привязываем обработчик к форме
    $form.on('submit', function(e) {
        handleChapterFormSubmit(
            e,
            chapterData,
            $form,
            $submitBtn,
            originalBtnText
        );
    });
});

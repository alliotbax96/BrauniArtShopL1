import { handleFormSubmit } from './formSubmitHandler.js';
import { createDefaultCoverHTML, updateCoverPreview, addRemoveCoverButton } from './coverPreview.js';
import {getErrorMessages, showAlert} from './utils.js';


$(document).ready(function() {
    const bookData = window.bookData || {
        bookId: null,
        isEditMode: false
    };

    // Кэширование DOM‑элементов
    const $bookType = $('#bookType');
    const $audiobookFields = $('.audiobook-field');
    const $alertBook = $('#alert_book');
    const $form = $('#book_data');
    const $submitBtn = $form.find('input[type="submit"]');
    const $authorInput = $('#authorInput');
    const $nameInput = $('#nameInput');
    const $coverTitle = $('#coverTitle');
    const $coverAuthor = $('#coverAuthor');
    const $fileUpload = $('.file-upload');
    const $coverPreview = $('#coverPreview');
    const $removeCoverBtn = $('#removeCover');

    // Константы
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 МБ
    const ALLOWED_TYPES = ['image/png', 'image/jpeg', 'image/jpg'];

    const originalBtnText = $submitBtn.val();
    let formChanged = false;

    $('input[name="is_active"]').on('change', function(){
        if($(this).is(':checked')){
            $('input[name="is_active"]').val(true);
        } else {
            $('input[name="is_active"]').val(false);
        }
    });

    $("#moderationSelect").on('change', function(){
        $submitBtn
            .prop('disabled', true)
            .val('Сохранение...')
            .addClass('btn-loading');

        var Data = new FormData();
        Data.append('moderation_status', $(this).val());
        $.ajax({
            url: '/seller/books/'+window.bookData.bookId+'/moderation',
            method: 'PATCH',
            data: Data,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(data) {
                showAlert('Статус модерации успешно обновлен!', 'success');
            },
            error: function(xhr) {
                const errorMsg = getErrorMessages(xhr);
                showAlert(errorMsg, 'danger');
            },
            complete: function() {
                $submitBtn
                    .prop('disabled', false)
                    .val(originalBtnText)
                    .removeClass('btn-loading');
            }
        });
    });

    // Инициализация
    initForm();

    function initForm() {
        toggleAudiobookFields();
        updateCoverText();
        setupEventListeners();
        $("#bookSeller, #genreSelect").select2({
            theme: 'bootstrap-5'
        });
    }

    function setupEventListeners() {
        $bookType.on('change', toggleAudiobookFields);
        $authorInput.on('input', updateCoverText);
        $nameInput.on('input', updateCoverText);
        $fileUpload.on('change', function(e) {
            handleFileUpload(e, $fileUpload, $coverPreview, MAX_FILE_SIZE, ALLOWED_TYPES);
        });
        $(document).on('click', '#removeCover', function() {
            removeCover($fileUpload, $coverPreview, $removeCoverBtn, createDefaultCoverHTML, $nameInput, $authorInput);
        });
        $form.on('submit', function(e) {
            handleFormSubmit(e, bookData, $form, $submitBtn, originalBtnText);
        });
        $form.on('input change', 'input, select, textarea', () => formChanged = true);
        $(window).on('beforeunload', function() {
            if (formChanged) {
                return 'У вас есть несохранённые изменения. Вы уверены, что хотите уйти?';
            }
        });
    }

    function toggleAudiobookFields() {
        const isAudiobook = $bookType.val() === 'audiobook';
        $audiobookFields[isAudiobook ? 'slideDown' : 'slideUp'](300);
    }

    function updateCoverText() {
        const author = $authorInput.val() || 'Автор';
        const title = $nameInput.val() || 'Название книги';

        $coverTitle.text(title);
        $coverAuthor.text(author);

        $coverTitle.css('font-size', title.length > 30 ? '12px' : '14px');
    }

    function handleFileUpload(e, $fileUpload, $coverPreview, maxSize, allowedTypes) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > maxSize) {
            showAlert('Размер файла не должен превышать 5 МБ', 'danger');
            $fileUpload.val('');
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            showAlert('Допустимые форматы: PNG, JPG, JPEG', 'danger');
            $fileUpload.val('');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            updateCoverPreview($coverPreview, e.target.result);
            $("#remove_image").remove();
            if ($removeCoverBtn.length === 0) {
                addRemoveCoverButton($coverPreview);
            }
        };
        reader.readAsDataURL(file);
    }

    function removeCover($fileUpload, $coverPreview, $removeCoverBtn, createDefaultCoverHTML, $nameInput, $authorInput) {
        $fileUpload.val('');
        $coverPreview.html(createDefaultCoverHTML($nameInput, $authorInput));
        $("#coverPreview").append('<input form="book_data" type="hidden" value="true" id="remove_image" name="remove_image">');
        $removeCoverBtn.remove();
    }
});

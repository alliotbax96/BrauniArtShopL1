import { showAlert } from './utils.js';
export function createDefaultCoverHTML($nameInput, $authorInput) {
    return `
<div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light position-relative"
style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="opacity: 0.1;">
        <div style="position: absolute; top: 10%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
        <div style="position: absolute; top: 30%; left: 15%; width: 70%; height: 1px; background: #fff;"></div>
        <div style="position: absolute; top: 50%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
        <div style="position: absolute; top: 70%; left: 15%; width: 70%; height: 1px; background: #fff;"></div>
        <div style="position: absolute; top: 90%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
    </div>

    <i class="feather-book text-white mb-3" style="font-size: 2rem; opacity: 0.8;"></i>
    <div id="coverTitle" class="text-white text-center px-3 fw-bold"
         style="font-size: 14px; line-height: 1.3; max-height: 60px; overflow: hidden;">
        ${$nameInput.val() || 'Название книги'}
    </div>
    <div style="width: 40px; height: 2px; background: rgba(255,255,255,0.5); margin: 8px 0;"></div>
    <div id="coverAuthor" class="text-white text-center px-3"
         style="font-size: 11px; opacity: 0.9; max-height: 30px; overflow: hidden;">
        ${$authorInput.val() || 'Автор'}
    </div>
    <div class="position-absolute bottom-0 start-0 w-100 p-2">
        <div style="width: 100%; height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px;"></div>
    </div>
</div>
<div class="position-absolute start-50 top-50 translate-middle upload-button"
     style="cursor: pointer; z-index: 10;">
    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center"
         style="width: 40px; height: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
        <i class="feather-camera text-primary" aria-hidden="true"></i>
    </div>
</div>`;
}

export function updateCoverPreview($coverPreview, imageSrc) {
    // Ищем img внутри превью (или создаём, если нет)
    let $img = $coverPreview.find('img');
    if (!$img.length) {
        $img = $('<img>');
        // Вставляем img перед кнопкой загрузки, чтобы кнопка осталась сверху
        $coverPreview.prepend($img);
    }

    $img.attr('src', imageSrc)
        .addClass('img-fluid rounded h-100 w-100')
        .css('object-fit', 'cover')
        .attr('alt', 'Предпросмотр обложки');

    // Важно: НЕ удаляем и не перезаписываем весь HTML, чтобы input остался
}


export function addRemoveCoverButton($coverPreview) {
    const $container = $coverPreview.closest('.d-flex').find('.d-flex.flex-column');
    $container.append(`
        <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeCover">
            <i class="feather-trash-2 me-1"></i> Удалить обложку
        </button>
    `);
}

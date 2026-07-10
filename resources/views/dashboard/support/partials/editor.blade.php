<div class="d-flex align-items-center justify-content-between border-top border-gray-5 bg-white sticky-bottom">
    <div class="d-flex align-center">
        <div class="dropdown border-end border-gray-5">
            <a href="javascript:void(0)" data-bs-toggle="dropdown">
                <div class="wd-60 d-flex align-items-center justify-content-center"
                     data-bs-toggle="tooltip" data-bs-trigger="hover" title="Прикрепить" style="height: 59px">
                    <i class="feather-link"></i>
                </div>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <label class="dropdown-item" style="cursor: pointer;">
                        <i class="feather-image me-3"></i>Изображение
                        <input type="file" class="d-none" id="uploadImage" accept="image/*">
                    </label>
                </li>
                <li>
                    <label class="dropdown-item" style="cursor: pointer;">
                        <i class="feather-file me-3"></i>Файл
                        <input type="file" class="d-none" id="uploadFile">
                    </label>
                </li>
            </ul>
        </div>
    </div>

    <div class="flex-grow-1 px-3">
        <input class="form-control border-0 emoji-picker"
               id="messageInput"
               placeholder="Ваше сообщение..."
               autocomplete="off">
    </div>

    <div class="border-start border-gray-5 d-flex send-message">
        <a href="javascript:void(0)"
           class="d-flex align-items-center justify-content-center wd-60"
           style="height: 59px;"
           id="sendMessageBtn">
            <i class="feather-send"></i>
        </a>
    </div>
</div>

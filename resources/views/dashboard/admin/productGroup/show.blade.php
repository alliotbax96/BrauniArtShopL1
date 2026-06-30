<div class="main-content">
    <div class="row">
        <div class="col-g-12">
            <div class="card border-top-0">
                <div class="card-body personal-info">
                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0 me-4">
                            <span class="d-block mb-2">{{ isset($category) ? 'Редактирование категории' : 'Создание категории' }}</span>
                        </h5>
                        <form id="CategoryForm" method="post"
                              action="{{ isset($category) ? route('admin.productGroups.update', $category->id) : route('admin.productGroups.store') }}"
                              enctype="multipart/form-data">
                            @csrf
                            @if(isset($category))
                                @method('PUT')
                            @endif
                            <button type="submit" class="btn btn-sm btn-light-brand">
                                Сохранить
                            </button>
                        </form>
                    </div>

                    <div id="alert_CategoryForm">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(session()->has('message'))
                            <div class="alert alert-danger">{{ session('message') }}</div>
                        @endif
                        @if(session()->has('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                    </div>

                    <div class="row mb-4 align-items-center">
                        <div class="col-lg-4">
                            <label class="fw-semibold">Изображение: </label>
                            <small class="text-muted d-block">(только для корневых категорий)</small>
                        </div>
                        <div class="col-lg-8">
                            <div class="mb-4 mb-md-0 d-flex gap-4 your-brand">
                                <div class="wd-800 ht-800 position-relative overflow-hidden border border-gray-2 rounded"
                                     style="width: 200px; height: 200px;">
                                    <div id="coverPreview" class="w-100 h-100 position-relative">
                                        <img src="{{ isset($category) && $category->image ? asset($category->image) : '#' }}"
                                             class="upload-pic img-fluid rounded h-100 w-100"
                                             alt="Обложка категории"
                                             style="object-fit: cover; {{ isset($category) && $category->image ? '' : 'display: none;' }}">
                                        <div class="position-absolute start-50 top-50 translate-middle upload-button"
                                             style="cursor: pointer; z-index: 10;">
                                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                                <i class="feather-camera text-primary" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                        <input class="file-upload position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                               type="file"
                                               form="CategoryForm"
                                               name="image"
                                               accept="image/png, image/jpeg, image/jpg"
                                               style="cursor: pointer;">
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    <div class="fs-11 text-gray-500 mt-2"># Загрузите изображение категории</div>
                                    <div class="fs-11 text-gray-500"># Рекомендуемый размер 800×800 px</div>
                                    <div class="fs-11 text-gray-500"># Максимальный размер файла 5 МБ</div>
                                    <div class="fs-11 text-gray-500"># Допустимые форматы: PNG, JPG, JPEG</div>
                                    @if(isset($category) && $category->image)
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeCover">
                                            <i class="feather-trash-2 me-1"></i> Удалить изображение
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center">
                        <div class="col-lg-4">
                            <label for="name" class="fw-semibold">Наименование *</label>
                        </div>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <input type="text" class="form-control"
                                       form="CategoryForm"
                                       name="name"
                                       id="name"
                                       value="{{ isset($category) ? $category->name : '' }}"
                                       placeholder="Наименование"
                                       required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <div class="col-lg-4">
                            <label for="ShopMode" class="fw-semibold">ShopMode</label>
                        </div>
                        <div class="col-lg-8">
                            <select name="ShopMode" id="ShopMode" class="form-control" form="CategoryForm">
                                @foreach($ShopModes as $item)
                                    <option value="{{$item->id}}" {{isset($category) && $category->ShopModeInfo->id == $item->id ? 'selected' : ''}}>{{$item->ShopModeName}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <div class="col-lg-4">
                            <label for="parent_id" class="fw-semibold">Родительская категория</label>
                        </div>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <select class="form-control" form="CategoryForm" name="parent_id" id="parent_id">
                                    <option value="">-- Корневая категория --</option>
                                    @foreach($productGroups as $item)
                                        @php
                                            $selected = (isset($category) && $category->parent_id == $item->id) ? 'selected' : '';
                                            // Не разрешаем выбрать себя или дочерние категории как родителя
                                            $disabled = (isset($category) && ($item->id == $category->id || $category->children->contains($item->id))) ? 'disabled' : '';
                                        @endphp
                                        <option value="{{ $item->id }}" {{ $selected }} {{ $disabled }}>
                                            {{ $item->name }}
                                            @if($disabled && isset($category))
                                                (недоступно)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="text-muted">Если выбрана родительская категория, изображение не требуется</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Превью изображения
        document.querySelector('.file-upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = document.querySelector('#coverPreview .upload-pic');
                    img.src = event.target.result;
                    img.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Удаление изображения
        const removeBtn = document.getElementById('removeCover');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (confirm('Вы уверены, что хотите удалить изображение?')) {
                    // Добавляем скрытое поле для удаления изображения
                    const form = document.getElementById('CategoryForm');
                    const removeField = document.createElement('input');
                    removeField.type = 'hidden';
                    removeField.name = 'remove_image';
                    removeField.value = '1';
                    form.appendChild(removeField);

                    // Скрываем изображение
                    const img = document.querySelector('#coverPreview .upload-pic');
                    img.src = '#';
                    img.style.display = 'none';

                    // Удаляем кнопку
                    removeBtn.remove();
                }
            });
        }

        // При выборе родительской категории делаем поле изображения необязательным
        const parentSelect = document.getElementById('parent_id');
        const fileInput = document.querySelector('.file-upload');

        if (parentSelect) {
            parentSelect.addEventListener('change', function() {
                if (this.value !== '') {
                    fileInput.required = false;
                } else {
                    fileInput.required = {{ isset($category) && !$category->image ? 'true' : 'false' }};
                }
            });
        }
    </script>
@endpush

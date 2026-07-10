<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-top-0">
                <div class="card-body p-0" id="project-create-steps">
                    <div class="step-title border-top">Детали</div>
                    <section class="step-body mt-4">
                        <form id="project-details">
                            <fieldset>
                                <div class="mb-5">
                                    <h2 class="fs-16 fw-bold">Основная информация</h2>
                                    <p class="text-muted"></p>
                                </div>
                                <fieldset>
                                    <input type="hidden" name="product_id" value="{{$product->getProductId()}}">
                                    @if($currentUser->isAdmin())
                                        <div class="mb-4">
                                            <label for="productSeller" class="form-label">Продавец <span
                                                    class="text-danger">*</span></label>
                                            <select id="productSeller" class="form-select" name="productSeller" required>
                                                @foreach($sellers as $item)
                                                    <option value="{{$item->id}}" {{$item->id == $product->productSeller ? 'selected' : ''}}>{{$item->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <input type="hidden" name="productSeller" value="{{$currentUser->getSellerId()}}">
                                    @endif
                                    <div class="mb-4">
                                        <label for="ProductName" class="form-label">Наименование<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="ProductName"
                                               name="productName" placeholder="Наименование"
                                               value="{{$product->getProductName()}}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="ProductArticle" class="form-label">Артикул <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="ProductArticle"
                                               name="productCode" placeholder="Артикул"
                                               value="{{$product->productCode}}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="ProductWeight" class="form-label">Вес в килограммах <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="ProductWeight"
                                               name="productWeight" placeholder="Вес"
                                               value="{{$product->productWeight}}" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="ProductDescription" class="form-label">Описание <span class="text-danger">*</span></label>
                                        <div id="ProductDescription" class="form-control"></div>
                                        <textarea class="hidden" name="ProductDescription" id="ProductDescriptionTextArea" cols="30" rows="10">{{$product->getProductDescription()}}</textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label for="productGroup" class="form-label">Категория товара <span
                                                class="text-danger">*</span></label>
                                        <select id="productGroup" class="form-select" name="productGroupId" required>
                                            @foreach($productGroups as $productGroup)
                                            <option value="{{$productGroup->id}}" @if($product->productGroupId == $productGroup->id) selected @endif>{{$productGroup->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </fieldset>
                            </fieldset>
                        </form>
                    </section>
                    <div class="step-title border-top">Изображения</div>
                    <section class="step-body mt-4">
                        <form id="project-images">
                            <div>
                                <div class="mb-5">
                                    <h2 class="fs-16 fw-bold">Изображения товаров</h2>
                                    <p class="text-muted">Измените фотографии товара. Вы также можете поменять местами
                                        уже загруженные изображения, просто перетаскивая их! (Первая фотография в списке
                                        автоматически становится главной.)</p>
                                </div>
                                <div class="mb-4">
                                    <label for="choose-file" class="custom-file-upload" id="choose-file-label">Выберете
                                        файл</label>
                                    <input type="file" id="choose-file" name="file[]" style="display: none" multiple
                                           accept=".jpg,.jpeg,.png,.gif">
                                </div>
                                <div class="row" id="js-file-list">
                                    @foreach($product->getProductImages() as $productImage)
                                    <div class="col-sm-6">
                                        <div class="card stretch stretch-full">
                                            <div class="card-body p-0 ht-200 position-relative">
                                                <img src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{$productImage->url}}" class="img-fluid ht-200" alt="">
                                                <div class="position-absolute" style="top: 15px; right: 15px">
                                                    <a href="javascript:void(0)"
                                                       onclick="delete_photo(this, {{$product->getProductId()}}); return false;"
                                                       class="avatar-text avatar-sm">
                                                        <i class="feather feather-x-circle"></i>
                                                    </a>
                                                </div>
                                                <input type="hidden" name="images[]" value="{{$productImage->url}}">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </section>
                    <div class="step-title border-top">Цены</div>
                    <section class="step-body mt-4">
                        <form id="project-price">
                            <fieldset>
                                <div class="mb-5">
                                    <h2 class="fs-16 fw-bold">Цены</h2>
                                    <p class="text-muted" id="param_tab_desc"></p>
                                </div>
                                <fieldset>
                                    <div class="mb-4" id="price_form">
                                        <h6 class="fs-13 fw-semibold pb-3 mb-3 border-bottom">Цены</h6>
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-none d-sm-block"></div>
                                                <div class="w-75">
                                                    <span class="text-truncate-1-line">Розничная цена</span>
                                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">Укажите стоимость для розничных покупателей</span>
                                                </div>
                                            </div>
                                            <div class="wd-150">
                                                <input class="form-control price_input"
                                                       placeholder="Цена"
                                                       name="prices[0][price]"
                                                       value="{{$product->getProductPrice()}}"/>
                                                <input type="hidden"
                                                       name="prices[0][priceType]"
                                                       value="Розничная цена"/>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-none d-sm-block"></div>
                                                <div class="w-75">
                                                    <span class="text-truncate-1-line">Оптовая цена</span>
                                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">Укажите стоимость для оптовых покупателей</span>
                                                </div>
                                            </div>
                                            <div class="wd-150">
                                                <input class="form-control price_input"
                                                       placeholder="Цена"
                                                       name="prices[1][price]"
                                                       value="{{$product->getProductPrice('Оптовая цена')}}"/>
                                                <input type="hidden"
                                                       name="prices[1][priceType]"
                                                       value="Оптовая цена"/>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </fieldset>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/dashboard/Pages/product.js')
@endpush

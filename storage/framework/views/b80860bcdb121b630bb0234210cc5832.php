<!-- main-area -->
<main>
    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Каталог</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Каталог</h1>
    </div>
    <!-- breadcrumb-area-end -->

    <!-- shop-area -->
    <div class="shop-area gray-bg pb-100">
        <div class="custom-container-two">
            <div class="shop-top-meta">
                <p class="show-result"></p>
                <div class="shop-meta-right">
                    <form id="filters" action="/products" method="get">
                        <select class="custom-select perPageSelect" name="perPage">
                            <option value="8" <?php if($products['perPage'] == 8): ?> selected <?php endif; ?>>8</option>
                            <option value="12" <?php if($products['perPage'] == 12): ?> selected <?php endif; ?>>12</option>
                            <option value="16" <?php if($products['perPage'] == 16): ?> selected <?php endif; ?>>16</option>
                            <option value="20" <?php if($products['perPage'] == 20): ?> selected <?php endif; ?>>20</option>
                            <option value="24" <?php if($products['perPage'] == 24): ?> selected <?php endif; ?>>24</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8 order-2 order-lg-0">
                    <aside class="shop-sidebar">
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Категории</h6>
                            </div>

                            <div class="shop-cat-list">
                                <ul class="treeview">
                                    <?php $__currentLoopData = $ProductGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($item->parent_id == 0): ?>
                                            <li class="has-children <?php if(isset($FilterGroup) && !empty($FilterGroup->parent_id) && $item->id == $FilterGroup->parent_id): ?> expanded <?php endif; ?>">
                                                <label class="category-toggle">
                                                    <input form="filters" type="radio" name="rootCategory" value="<?php echo e($item->id); ?>" <?php if(isset($FilterGroup) && !empty($FilterGroup->parent_id) && $item->id == $FilterGroup->parent_id): ?> checked <?php endif; ?>>
                                                    <span class="category-name"><?php echo e($item->name); ?></span>
                                                    <span class="toggle-icon"><?php if($item->children->isNotEmpty()): ?>▶<?php endif; ?></span>
                                                </label>
                                                <!-- Вложенные категории -->
                                                <?php if($item->children->isNotEmpty()): ?>
                                                    <ul class="children">
                                                        <?php $__currentLoopData = $item->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li>
                                                                <label>
                                                                    <input form="filters" type="radio" name="category" value="<?php echo e($child->id); ?>" <?php if(isset($FilterGroup) && !empty($FilterGroup->parent_id) && $child->id == $FilterGroup->id): ?> checked <?php endif; ?>>
                                                                    <span class="category-name"><?php echo e($child->name); ?></span>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>


                        </div>
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Цена</h6>
                            </div>
                            <div class="price_filter">
                                <div id="slider-range"></div>
                                <div class="price_slider_amount">
                                    <span>Цена :</span>
                                    <input type="text" id="amount" name="price" placeholder="Add Your Price" />
                                    <input form="filters" type="hidden" name="min_price" value="<?php echo e($products['filters']['min_price']); ?>">
                                    <input form="filters" type="hidden" name="max_price" value="<?php echo e($products['filters']['max_price']); ?>">
                                </div>
                            </div>
                            <br>
                            <input form="filters" type="submit" class="btn" value="Показать">
                        </div>
                    </aside>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="row list">
                        <?php $__currentLoopData = $products['products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-xl-3 col-lg-6 col-md-4 col-sm-6">
                                <div class="exclusive-item exclusive-item-three text-center mb-50 card-item">
                                    <div class="exclusive-item-thumb">
                                        <a href="/products/<?php echo e($product->id); ?>">
                                            <img src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/<?php echo e($product->getMainImage()); ?>" alt="" style="object-fit: contain; height: 300px;">
                                        </a>
                                    </div>
                                    <div class="exclusive-item-content">
                                        <h5 style="font-size: 12px;"><a href="/products/<?php echo e($product->id); ?>"><?php echo e($product->getProductName()); ?></a></h5>
                                        <p></p>
                                        <div class="exclusive--item--price">
                                            <span class="new-price"><?php echo e($product->getProductPrice()); ?> руб.</span>
                                        </div>
                                        <div class="rating">
                                            <?php for($i = 0; $i < 5; $i++): ?>
                                                <i class="fas fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="cart_buttons_block">

                                            <?php if (isset($component)) { $__componentOriginal0ebc6ef07b571ddf6bdd9d88111343c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc6ef07b571ddf6bdd9d88111343c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.add-to-cart-button','data' => ['productId' => $product->getProductId(),'sellerId' => $product->getProductSellerId(),'quantity' => '1','buttonText' => 'В корзину','linkClass' => 'btn-primary adtocart','options' => []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('add-to-cart-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->getProductId()),'seller-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->getProductSellerId()),'quantity' => '1','button-text' => 'В корзину','link-class' => 'btn-primary adtocart','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0ebc6ef07b571ddf6bdd9d88111343c0)): ?>
<?php $attributes = $__attributesOriginal0ebc6ef07b571ddf6bdd9d88111343c0; ?>
<?php unset($__attributesOriginal0ebc6ef07b571ddf6bdd9d88111343c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0ebc6ef07b571ddf6bdd9d88111343c0)): ?>
<?php $component = $__componentOriginal0ebc6ef07b571ddf6bdd9d88111343c0; ?>
<?php unset($__componentOriginal0ebc6ef07b571ddf6bdd9d88111343c0); ?>
<?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="pagination-wrap"><?php echo e($products['products']->withQueryString()->links('pagination::bootstrap-5')); ?></div>
                </div>
            </div>
        </div>
    </div>
    <!-- shop-area-end -->
</main>
<!-- main-area-end -->
<?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/Pages/products.js'); ?>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/artemnegladenko/Documents/dev/sites/LaravelProjects/BrauniartShopL1/resources/views/products/products.blade.php ENDPATH**/ ?>
<!-- main-area -->
    <main>

        <!-- slider-area -->









        <!-- slider-area-end -->

        <!-- exclusive-collection-area -->
        <section class="exclusive-collection pt-10 pb-10">
            <div class="custom-container-two">
                <div class="row justify-content-center">
                </div>
                <div id="showmore-list">
                    <div>
                        <div class="row prod-list">
                            <?php $__currentLoopData = $products['products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-xl-3 col-lg-6 col-md-4 col-sm-6 {if ($i++)<=8}cat-one{/if}">
                                    <div class="exclusive-item exclusive-item-three text-center mb-50 card-item">
                                        <div class="exclusive-item-thumb">
                                            <a href="/products/<?php echo e($product->getProductId()); ?>">
                                                <img src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/<?php echo e($product->getMainImage()); ?>" alt="" style="object-fit: contain; height: 300px;">
                                            </a>
                                        </div>
                                        <h5 class="exclusive-item-content"><a href="/products/<?php echo e($product->getProductId()); ?>"><?php echo e($product->getProductName()); ?></a></h5>
                                        <p></p>
                                        <div class="exclusive--item--price"><span class="new-price"><?php echo e($product->getProductPrice()); ?> руб.</span></div>
                                        <div class="rating">
                                            <?php for($i = 0; $i < 5; $i++): ?>
                                                <i class="fas fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="cart_buttons_block">
                                            
                                            <?php if (isset($component)) { $__componentOriginal0ebc6ef07b571ddf6bdd9d88111343c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc6ef07b571ddf6bdd9d88111343c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.add-to-cart-button','data' => ['productId' => $product->getProductId(),'sellerId' => $product->getProductSellerId(),'quantity' => '1','buttonText' => 'В корзину','linkClass' => 'btn-primary AddToCartHome','options' => []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('add-to-cart-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->getProductId()),'seller-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->getProductSellerId()),'quantity' => '1','button-text' => 'В корзину','link-class' => 'btn-primary AddToCartHome','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([])]); ?>
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
                                
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="showmore-triger" data-page="1" data-max="{$amt}">
                <img src="https://snipp.ru/demo/693/ajax-loader.gif" alt="">
            </div>
        </section>
        <!-- exclusive-collection-area-end -->
    </main>
    <!-- main-area-end -->

<?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/Pages/home.js'); ?>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/artemnegladenko/Documents/dev/sites/LaravelProjects/BrauniartShopL1/resources/views/pages/home.blade.php ENDPATH**/ ?>
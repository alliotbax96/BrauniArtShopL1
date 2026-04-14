<?php if(count($products['products']) < 1): ?>
    0
<?php else: ?>
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
                <div class="exclusive--item--price"><span class="new-price">
                                        <?php echo e($product->getProductPrice()); ?> руб.</span></div>
                <div class="rating">
                    <?php for($i = 0; $i < 5; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                </div>
                <div class="cart_buttons_block"><a href="javascript:void(0)" class="btn-primary AddToCartHome">В корзину</a></div>
            </div>
        </div>
        
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /Users/artemnegladenko/Documents/dev/sites/LaravelProjects/BrauniartShopL1/resources/views/elements/ajaxHome.blade.php ENDPATH**/ ?>
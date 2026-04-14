<ul class="category-menu" style="display: none;">

    <?php $__currentLoopData = $ProductGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($item->parent_id == 0): ?>
            <li class="has-dropdown">
                <a href="/products?category=<?php echo e($item->id); ?>">
                    <div class="cat-menu-img">
                        <img style="width: 38px; heght: 38px;" src="https://id.brauniart.shop/<?php echo e($item->image); ?>" alt="">
                    </div>
                    <?php echo e($item->name); ?>

                </a>
                <?php if($item->children->isNotEmpty()): ?>
                    <ul class="mega-menu">
                        <li>
                            <ul>
                                <li class="dropdown-title"><?php echo e($item->name); ?></li>
                                <?php $__currentLoopData = $item->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($childItem->parent_id == $item->id): ?>
                                        <li><a href="/products?category=<?php echo e($childItem->id); ?>"><?php echo e($childItem->name); ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php /**PATH /Users/artemnegladenko/Documents/dev/sites/LaravelProjects/BrauniartShopL1/resources/views/elements/miniCatalog.blade.php ENDPATH**/ ?>
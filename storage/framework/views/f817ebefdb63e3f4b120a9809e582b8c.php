<main>
    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="/cart">Корзина</a></li>
                <li class="breadcrumb-item active" aria-current="page">Оформление заказа</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Оформление заказа</h1>
    </div>
    <!-- breadcrumb-area-end -->

    <section class="shop-cart-area wishlist-area mb-5">
        <?php if(count($cartItems) <= 0): ?>
            <div class="custom-container-two my-5">
                <div class="position-relative p-5 text-center text-muted bg-body border border-dashed rounded-5">
                    <h1 class="text-body-emphasis">Ой! Кажется корзина пуста!</h1>
                    <p class="col-lg-6 mx-auto mb-4">
                        Добавьте в корзину товары из каталога и возвращайтесь.
                    </p>
                    <a class="btn btn-primary px-5 mb-5" href="/products">
                        В каталог
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="custom-container-two">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="table-responsive-xl">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th class="product-thumbnail" scope="col"></th>
                                    <th class="product-name" scope="col">Наименование</th>
                                    <th class="product-price" scope="col">Цена</th>
                                    <th class="product-quantity" scope="col">Количество</th>
                                    <th class="product-subtotal" scope="col">Сумма</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr data-item-id="<?php echo e($item->id); ?>">
                                        <input form="checkout" type="hidden" name="selected_items[]"
                                               value="<?php echo e($item->id); ?>">
                                        <td>
                                            <a href="<?php echo e(route('products.show', $item->getProduct()->id)); ?>">
                                                <img
                                                    src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/<?php echo e($item->getProduct()->getMainImage()); ?>"
                                                    alt="<?php echo e($item->getProduct()->getProductName()); ?>"
                                                    style="object-fit: contain; height: 129px; width: 103px;">
                                            </a>
                                        </td>
                                        <td class="product-name">
                                            <h4>
                                                <a href="<?php echo e(route('products.show', $item->getProduct()->id)); ?>">
                                                    <?php echo e($item->getProduct()->getProductName()); ?>

                                                </a>
                                            </h4>
                                        </td>
                                        <td class="product-price">
                                          <span id="price<?php echo e($item->id); ?>">
                                            <?php echo e(number_format($item->getProduct()->getProductPrice(), 2, ',', ' ')); ?>

                                          </span> руб.
                                        </td>
                                        <td class="product-quantity"><?php echo e($item->quantity); ?> шт</td>
                                        <td class="product-subtotal">
                                         <span id="sum<?php echo e($item->id); ?>">
                                           <?php echo e(number_format($item->getProduct()->getProductPrice() * $item->quantity, 2, ',', ' ')); ?>

                                         </span> руб.
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="shop-cart-bottom mt-4">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="cart-coupon">
                                        <!-- Здесь можно добавить форму для купонов -->
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="continue-shopping">
                                        <a href="/products" class="btn">Продолжить покупки</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-8">
                        <aside class="shop-cart-sidebar checkout-sidebar">
                            <div class="shop-cart-widget">
                                <h6 class="title">Итог</h6>
                                <div id="alert">
                                    <div class="alert alert-danger hidden" role="alert"></div>
                                </div>

                                <!-- Основная форма оформления заказа -->
                                <form action="<?php echo e(route('checkout.process')); ?>" method="POST" id="checkout"
                                      class="checkout-form">
                                    <?php echo csrf_field(); ?>

                                    <ul class="checkout-summary">
                                        <li><span>Товары</span> <?php echo e($subtotal); ?> руб.</li>
                                        <li><span>Доставка</span> <?php echo e($deliveryCost); ?> руб.</li>
                                        <li class="cart-total-amount">
                                            <span>Итого:</span>
                                            <span class="amount"><?php echo e($totalAmount); ?> руб.</span>
                                        </li>
                                    </ul>

                                    <!-- Способы оплаты -->
                                    <div class="payment-methods">
                                        <div class="bank-transfer">
                                            <div class="form-check">
                                                <input type="radio" name="pay_type" class="form-check-input"
                                                       id="customCheck3" value="invoice" required <?php echo e($legalDetailCheck ? '' : 'disabled'); ?>>
                                                <label class="form-check-label" for="customCheck3">Оплата по счёту</label>
                                                <p class="text-muted small">Доступно только для ИП и Юридических лиц</p>
                                            </div>
                                        </div>

                                        <div class="paypal-method">
                                            <div class="form-check">
                                                <input type="radio" name="pay_type" class="form-check-input"
                                                       id="customCheck6" value="card" checked required>
                                                <label class="form-check-label" for="customCheck6">Онлайн платёж</label>

                                                <!-- Выбор карты -->
                                                <div class="bankcard-selection mt-3">
                                                    <?php $__currentLoopData = $Cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="bankcard-option user-card mb-2">
                                                            <input type="radio" name="payment-card" id="card-<?php echo e($k+1); ?>"
                                                                   data-pan="<?php echo e(strtok($card['Pan'], '*')); ?>"
                                                                   value="<?php echo e($card['CardId']); ?>">
                                                            <label for="card-<?php echo e($k+1); ?>" class="bankcard-label">
                                                                <div class="bankcard-logo">
                                                                    <img src="#" alt="">
                                                                </div>
                                                                <div
                                                                    class="bankcard-number"><?php echo e(substr($card['Pan'], -4)); ?></div>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                    <!-- Новая карта -->
                                                    <div class="bankcard-option">
                                                        <input type="radio" name="payment-card" id="card-<?php echo e($k+2); ?>"
                                                               data-pan="" value="0">
                                                        <label for="card-<?php echo e($k+2); ?>" class="bankcard-label">
                                                            <div class="bankcard-logo">
                                                                <img src="/assets/img/plus.png" alt="Новая карта">
                                                            </div>
                                                            <div class="bankcard-number">Новая карта</div>
                                                        </label>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Кнопка отправки внутри формы -->
                                    <input type="submit" value="Оформить заказ" class="btn btn-primary btn-block mt-4">
                                </form>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/Pages/checkout.js'); ?>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/artemnegladenko/Documents/dev/sites/LaravelProjects/BrauniartShopL1/resources/views/pages/checkout.blade.php ENDPATH**/ ?>
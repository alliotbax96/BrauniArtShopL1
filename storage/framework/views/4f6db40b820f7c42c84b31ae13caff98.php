<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'productId' => null,
    'sellerId' => null,
    'quantity' => 1,
    'productType' => 'App\\Models\\Product',
    'options' => [],
    'buttonText' => 'В корзину',
    'linkClass' => ''
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'productId' => null,
    'sellerId' => null,
    'quantity' => 1,
    'productType' => 'App\\Models\\Product',
    'options' => [],
    'buttonText' => 'В корзину',
    'linkClass' => ''
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<a href="javascript:void(0)"
   class="add-to-cart-link <?php echo e($linkClass); ?>"
   data-product-id="<?php echo e($productId); ?>"
   data-seller-id="<?php echo e($sellerId); ?>"
   data-quantity="<?php echo e($quantity); ?>"
   data-product-type="<?php echo e($productType); ?>"
   title="Добавить в корзину">
    <?php echo e($buttonText); ?>

</a>
<?php /**PATH /Users/artemnegladenko/Documents/dev/sites/LaravelProjects/BrauniartShopL1/resources/views/components/add-to-cart-button.blade.php ENDPATH**/ ?>
// Обработчик изменения рейтинга
$(".rising-rating").on("change", function(ev, data) {
    $("#form_estimation").val(data.to);
});

// Добавляем кнопки управления количеством
$(".p_product-plus-minus").append('<div class="dec qtybutton">-</div><div class="inc qtybutton">+</div>');

// Единый обработчик для кнопок + и -
$(".qtybutton").on("click", function() {
    const $button = $(this);
    const $input = $button.closest(".p_product-plus-minus").find("input");
    const max = parseFloat($input.attr("max")) || Infinity;
    const currentValue = parseFloat($input.val()) || 0;
    let newValue;

    if ($button.text() === "+") {
        newValue = Math.min(currentValue + 1, max);
    } else {
        newValue = Math.max(currentValue - 1, 1);
    }

    $input.val(newValue).attr("value", newValue);
    $(".add-to-cart-link").attr('data-quantity', newValue);
});

// Обработчик ручного ввода количества
$(".p_product-input").on("change", function() {
    const $input = $(this);
    let value = parseFloat($input.val()) || 0;
    const max = parseFloat($input.attr("max")) || Infinity;

    value = Math.max(1, Math.min(value, max));
    $input.val(value).attr("value", value);
    $(".add-to-cart-link").attr('data-quantity', value);
});

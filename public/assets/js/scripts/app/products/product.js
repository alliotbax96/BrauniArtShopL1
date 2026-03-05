$(".rising-rating").on("change", function(ev, data){
    $("#form_estimation").val(data.to);
});

$("#form_review").on("submit", function(){
    $.ajax({
        url: '/catalog/add_review/',
        method: 'post',
        dataType: 'html',
        data: $(this).serialize(),
        success: function(data){
            $("#form_add_review_result").html(data);
        }
    });
    return false;
});

$(".p_product-plus-minus").append('<div class="dec qtybutton">-</div><div class="inc qtybutton">+</div>');
$(".qtybutton").on("click", function () {
    var $button = $(this);
    var oldValue = $button.parent().find("input").val();

    if ($button.text() == "+") {
        var newVal = parseFloat(oldValue) + 1;
        if(newVal > $button.parent().find("input").attr('max')){
            newVal = $button.parent().find("input").attr('max');
        }
    } else {
        // Don't allow decrementing below zero
        if (oldValue > 1) {
            var newVal = parseFloat(oldValue) - 1;

        } else {
            newVal = 1;
        }
    }
    $button.parent().find("input").val(newVal);
    $button.parent().find("input").attr('value', newVal);
});

$(".p_product-input").on('change', function(){
    var $input = $(".p_product-input");
    var Value = $input.val();
    var newVal = Value;
    if(Number(Value) >= Number($input.attr('max'))){
        newVal = $input.attr('max');
    }
    if(Value < 1){
        newVal = 1;
    }
    $input.val(newVal);
    $input.attr('value', newVal);
});

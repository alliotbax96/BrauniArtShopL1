
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

$(".ad_to_cart").on("click", function(){
    var button = this;
// $( "body" ).prepend('<div id="preloader"><div id="ctn-preloader" class="ctn-preloader"><div class="animation-preloader"><div class="spinner"></div>                 <div class="txt-loading">                     <span data-text-preloader="B" class="letters-loading">                         B                     </span>                     <span data-text-preloader="R" class="letters-loading">                         R                     </span>                     <span data-text-preloader="A" class="letters-loading">                         A                     </span>                     <span data-text-preloader="U" class="letters-loading">                         U                     </span>                     <span data-text-preloader="N" class="letters-loading">                         N                     </span>                     <span data-text-preloader="I" class="letters-loading">                         I                     </span>                     <span data-text-preloader="A" class="letters-loading">                         A                     </span>                     <span data-text-preloader="R" class="letters-loading">                         R                     </span>                     <span data-text-preloader="T" class="letters-loading">                         T                     </span>                 </div>             </div>             <div class="loader">                 <div class="row">                     <div class="col-3 loader-section section-left">                         <div class="bg"></div>                     </div>                     <div class="col-3 loader-section section-left">                         <div class="bg"></div>                     </div>                     <div class="col-3 loader-section section-right">                         <div class="bg"></div>                     </div>                     <div class="col-3 loader-section section-right">                         <div class="bg"></div>                     </div>                 </div>             </div>         </div>     </div>');
    $.ajax({
        url: '/catalog/ad_to_cart',
        method: 'post',
        dataType: 'html',
        data: $("#adtocart").serialize(),
        success: function(data){
            // console.log(data);
            var res = JSON.parse(data);
            if(res.add_to_cart){
                $.post('/catalog/adtocart/cart_gen', {cart_id: res.cart_id}, function(data){

                    var res = JSON.parse(data);
                    $("#minicart").html(res.html);
                    $(".cart-count").html(res.count);
                    $(".cart-total-price").html(res.itog+" руб.");
                    $(button).removeClass('btn-primary');
                    $(button).addClass('btn-success');
                    $(button).html('В Корзине');
                });
            } else {
                alert(res.error);
            }
        }
    });
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



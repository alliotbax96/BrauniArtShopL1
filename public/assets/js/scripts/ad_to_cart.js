$(".adtocart").on('click', function(){
	var button = $(this);
	$.post('/catalog/ad_to_cart', {tov_id: $(this).attr('data-id'), seller: $(this).attr('data-seller_id'), warehouse: $(this).attr('data-warehouse-id')}, function(data){
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
	});
});

function adtocart(button) {
    $.post('/catalog/ad_to_cart', {
        tov_id: $(button).attr('data-id'),
        seller: $(button).attr('data-seller_id'),
        warehouse: $(button).attr('data-warehouse-id')
    }, function (data) {
        // console.log(data);
        var res = JSON.parse(data);
        if (res.add_to_cart) {
            $.post('/catalog/adtocart/cart_gen', {cart_id: res.cart_id}, function (data) {
                var res = JSON.parse(data);
                $("#minicart").html(res.html);
                $(".cart-count").html(res.count);
                $(".cart-total-price").html(res.itog + " руб.");
                $(button).removeClass('btn-primary');
                $(button).addClass('btn-success');
                $(button).html('В Корзине');
            });
        } else {
            alert(res.error);
        }
    });
}
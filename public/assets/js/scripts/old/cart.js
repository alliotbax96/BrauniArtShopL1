
$(document).ready(function () {
    (function(w) {
        function startWidget() {
            w.YaDelivery.createWidget({
                containerId: 'pvz_map',         // Идентификатор HTML-элемента (контейнера), в котором будет отображаться виджет
                params: {
                    city: "Москва",                     // Город, отображаемый на карте при запуске
                    size:{                              // Размеры виджета
                        "height": "450px",              // Высота
                        "width": "100%"                 // Ширина
                    },
                    // source_platform_station: "05e809bb-4521-42d9-a936-0fb0744c0fb3",  // Станция отгрузки
                    physical_dims_weight_gross: 10000,  // Вес отправления
                    delivery_price: "от 100",           // Стоимость доставки
                    delivery_term: "от 1 дня",          // Срок доставки
                    show_select_button: true,           // Отображение кнопки выбора ПВЗ (false — скрыть кнопку, true — показать кнопку)
                    filter: {
                        type: [                         // Тип способа доставки
                            "pickup_point",             // Пункт выдачи заказа
                            "terminal"                  // Постамат
                        ],
                        is_yandex_branded: false,       // Тип пункта выдачи заказа (false — Партнерские ПВЗ, true — ПВЗ Яндекса)
                        payment_methods: [              // Способ оплаты
                            "already_paid",             // Доступен для доставки предоплаченных заказов
                            "card_on_receipt"           // Доступна оплата картой при получении
                        ],
                        payment_methods_filter: "or"    // Фильтр по типам оплаты
                    }
                },
            });
        }
        w.YaDelivery
            ? startWidget()
            : document.addEventListener('YaNddWidgetLoad', startWidget);
    })(window);

    // Подписка на событие

	document.addEventListener('YaNddWidgetPointSelected', function (data) {
		$.post('/add_pvz', { client_pvz: data.detail.id, pvz_name: data.detail.address.full_address, pvz_kode: null}, function (data) {
		$('.collapse').hide();
		console.log(data);
		$("#pvz").html(data);
		});
	});
});



$("#pvz").on('change', function () {
	pvz = $(this).val();
});

/*=============================================
	=    		 Cart Active  	         =
=============================================*/
$(".cart-plus-minus").append('<div class="dec qtybutton">-</div><div class="inc qtybutton">+</div>');
$(".qtybutton").on("click", function () {

	var $button = $(this);
	var oldValue = $button.parent().find("input").val();
	var dataid = $button.parent().find("input").attr('data-id');
	var max = $button.parent().find("input").attr('max');
	var price = "#price" + dataid;
	var sum = "#sum" + dataid;

	if ($button.text() == "+") {
		var newVal = parseFloat(oldValue) + 1;
		var itog = newVal * $(price).html();
		if (newVal > max) {
			$(".main_alert").append('<div class="alert alert-danger alert-dismissible fade show" role="alert">Выбранного количества нет в наличии у продавца!</div>');
			newVal = $button.parent().find("input").attr('max');
			window.scrollTo(0, 0);
			$button.parent().find("input").val(newVal);
			$.post('/cart/count_change/', { id: dataid, count: newVal }, function (data) {
				// // ajax_del_price_update($("#pvz").val());
			});
			return;
		}
		//   $("#del_price").html("<img src=\"/templates/assets/img/loading.gif\" style=\"height: 15px;\">");
		$("#itog").html("<img src=\"/templates/assets/img/loading.gif\"  style=\"height: 15px;\">");
		$(sum).html(itog);
		$("#pitog").html(Number($("#pitog").html()) + Number($(price).html()));
	} else {
		// Don't allow decrementing below zero
		// $("#del_price").html("<img src=\"/templates/assets/img/loading.gif\" style=\"height: 15px;\">");
		$("#itog").html("<img src=\"/templates/assets/img/loading.gif\"  style=\"height: 15px;\">");
		var newVal = parseFloat(oldValue) - 1;
		if (newVal > 1) {
			var itog = newVal * $(price).html();
			$(sum).html(itog);
			$("#pitog").html(Number($("#pitog").html()) - Number($(price).html()));
		} else {
			newVal = 1;
			//return;
		}
	}
	$button.parent().find("input").val(newVal);
	$.post('/cart/count_change/', { id: dataid, count: newVal }, function (data) {
		// // ajax_del_price_update($("#pvz").val());
	});
});

$(".item_count").on('change', function () {
	var $input = $(".p_product-input");
	var Value = $input.val();
	var newVal = Value;
	var itog = newVal * $(price).html();
	var old_sum = Number($(sum).html());

	if (Number(Value) >= Number($input.attr('max'))) {
		newVal = $input.attr('max');
		$(".main_alert").append('<div class="alert alert-danger alert-dismissible fade show" role="alert">Выбранного количества нет в наличии у продавца!</div>');
	}
	if (Value < 1) {
		newVal = 1;
	}
	$input.val(newVal);
	$input.attr('value', newVal);

	$(sum).html(itog);

	$("#pitog").html(Number($("#pitog").html()) - old_sum + itog);

	$.post('/cart/count_change/', { id: dataid, count: newVal }, function (data) {
		// // // ajax_del_price_update($("#pvz").val());
	});
});

$(".cart_item_del").click(function () {
	if (confirm('Вы уверены?')) {
		$("body").prepend('<div id="preloader"><div id="ctn-preloader" class="ctn-preloader"><div class="animation-preloader"><div class="spinner"></div> <div class="txt-loading"> <span data-text-preloader="B" class="letters-loading"> B </span> <span data-text-preloader="R" class="letters-loading"> R </span> <span data-text-preloader="A" class="letters-loading"> A </span> <span data-text-preloader="U" class="letters-loading"> U </span> <span data-text-preloader="N" class="letters-loading"> N </span> <span data-text-preloader="I" class="letters-loading"> I </span> <span data-text-preloader="A" class="letters-loading"> A </span> <span data-text-preloader="R" class="letters-loading"> R </span> <span data-text-preloader="T" class="letters-loading"> T </span> </div> </div> <div class="loader"> <div class="row"> <div class="col-3 loader-section section-left"> <div class="bg"></div> </div> <div class="col-3 loader-section section-left"> <div class="bg"></div> </div> <div class="col-3 loader-section section-right"> <div class="bg"></div> </div> <div class="col-3 loader-section section-right"> <div class="bg"></div></div></div></div></div></div>');
		var id = $(this).attr('data-id');
		$.post('/cart/del/', { id: id }, function (data) {
			location.reload();
		});
	}
});

$('.table').checkboxTable();

$("#cart_form").on('submit', function () {
	$("#checkout_button").html('<img src="https://snipp.ru/demo/693/ajax-loader.gif" alt="">');
	$("#checkout_button").attr('disabled', true);
	// console.log($(this).serialize());
	$.post('/cart/checkout/', $(this).serialize(), function (data) {
		// console.log(data);
		if (data == 'error') {
			alert("Выберете хотя бы один товар!");
		} else {
			window.location.href = data;
		}
	});
});
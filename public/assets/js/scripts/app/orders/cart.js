
$(document).ready(function () {
    (function(w) {
        function startWidget() {
            w.YaDelivery.createWidget({
                containerId: 'pvz_map',         // Идентификатор HTML-элемента (контейнера), в котором будет отображаться виджет
                params: {
                    city: "Москва",                     // Город, отображаемый на карте при запуске
                    size:{                              // Размеры виджета
                        "height": "800px",              // Высота
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
        // $.post('/add_pvz', { client_pvz: data.detail.id, pvz_name: data.detail.address.full_address, pvz_kode: null}, function (data) {
            $('.collapse').hide();
        //     console.log(data);
        //     $("#pvz").html(data);
        // });
    });
});


/*=============================================
	=    		 Cart Active  	         =
=============================================*/

$(document).ready(function() {
    $('.point-select').select2({theme: 'bootstrap-5'});
});

// Инициализация кнопок +/- и обработчика изменений
$(".cart-plus-minus").append('<div class="dec qtybutton">-</div><div class="inc qtybutton">+</div>');

$(".qtybutton").on("click", function () {
    var $button = $(this);
    var $input = $button.parent().find("input");
    var oldValue = parseFloat($input.val());
    var newVal;

    // Определяем новое значение количества
    if ($button.text() == "+") {
        newVal = oldValue + 1;
    } else {
        // Не позволяем уменьшать ниже нуля
        if (oldValue > 0) {
            newVal = oldValue - 1;
        } else {
            newVal = 0;
        }
    }

    // Обновляем значение в поле ввода
    $input.val(newVal);

    // Вызываем функцию пересчёта суммы для этого товара
    updateItemTotal($input);
});

// Функция для пересчёта суммы конкретного товара
function updateItemTotal($input) {
    // Получаем родительский контейнер товара
    var $itemContainer = $input.closest(".cart-product-item");

    // Извлекаем цену за единицу (удаляем "руб." и пробелы)
    var priceText = $itemContainer.find(".price-label").text();
    var price = parseFloat(priceText.replace("Цена: ", "").replace(" руб.", "").trim());

    // Получаем текущее количество
    var quantity = parseFloat($input.val());

    // Считаем новую сумму
    var total = price * quantity;

    // Обновляем отображение суммы (округляем до 2 знаков после запятой)
    $itemContainer.find(".item-total span").text(total.toFixed(0));
}

// Дополнительно: обрабатываем прямое изменение значения в input (если пользователь вводит вручную)
$(".item_count").on("change", function() {
    updateItemTotal($(this));
});

$(".cart-root-checkbox").click(function(){
    var $this = this;
    $(".cart-root-checkbox-group").children().removeClass('active');
    $(".cart-root-checkbox-group").children().prop('disabled', false);
    $($this).addClass('active');
    $($this).prop('disabled', true);
    if($($this).attr('data-action') == 'check'){
      $(".cart-checkbox").prop('checked', true);
    } else {
        $(".cart-checkbox").prop('checked',false);
    }
});

$(".cart-checkbox").click(function(){
    var $checked = Number($('.cart-checkbox:checked').length);
    var cart_count = $(".cart-count").html();
    if($checked == Number(cart_count)){
       $(".cart-root-checkbox-group").children().removeClass('active');
       $(".cart-root-checkbox-group").children().prop('disabled', false);
       $('button[data-action="check"]').addClass('active');
       $('button[data-action="check"]').prop('disabled', true);
    } else if($checked == 0){
        $(".cart-root-checkbox-group").children().removeClass('active');
        $(".cart-root-checkbox-group").children().prop('disabled', false);
        $('button[data-action="uncheck"]').addClass('active');
        $('button[data-action="uncheck"]').prop('disabled', true);
    } else {
        $(".cart-root-checkbox-group").children().removeClass('active');
        $(".cart-root-checkbox-group").children().prop('disabled', false);
    }
});
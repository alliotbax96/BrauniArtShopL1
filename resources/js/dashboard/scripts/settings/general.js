// Константы
const API_TOKEN = "1a95c0aa5a5f5afd90ffd22d22cd9715288d0c23";
const SELECTORS = {
    alertAddress: "#alert_address",
    userAddress: "#user_address",
    pointMap: "#point_map_collapse",
    PhoneInput: ".mask-phone",
    pvzInput: "#pvzInput"
};

// Инициализация виджетов и событий
function initWidgets() {
    (function (w) {
        function startWidget() {
            w.YaDelivery.createWidget({
                containerId: 'point_map',
                params: {
                    city: "Москва, Бульварное кольцо",
                    size: {
                        "height": "450px",
                        "width": "100%"
                    },
                    // physical_dims_weight_gross: 1000,
                    delivery_price: "от 100",
                    delivery_term: "от 1 дня",
                    show_select_button: true,
                    apiKey: 'ddf6f3c5-7470-4e2c-b2a9-359a8c35e699',
                    filter: {
                        type: ["pickup_point", "terminal"],
                        // is_yandex_branded: false,
                        // payment_methods: ["already_paid", "card_on_receipt"],
                        // payment_methods_filter: "or"
                    }
                }
            });
        }

        $("#AddPvz").click(function() {
            $(SELECTORS.pointMap).show();
            w.YaDelivery ? startWidget() : document.addEventListener('YaNddWidgetLoad', startWidget);
        });

    })(window);

    document.addEventListener('YaNddWidgetPointSelected', function (event) {
        const data = event.detail;
        $.post('/seller/settings/pvz', {
            client_pvz: data.id,
            pvz_name: data.address.full_address,
            pvz_kode: null,
            _token: $('meta[name="csrf-token"]').attr('content') // добавляем CSRF‑токен
        }, function(response) {
            const parsedData = typeof response === 'string' ? jQuery.parseJSON(response) : response;
            if (parsedData.result) {
                $(SELECTORS.pointMap).hide();
                $(SELECTORS.pvzInput).val(parsedData.pvz);
            } else {
                alert(parsedData.error);
                console.log(parsedData);
            }
        });
    });
}
$(document).ready(function() {
    initWidgets();
});

$(document).ready(function() {
    $('#seller_general').on('submit', function(e) {
        e.preventDefault(); // Отменяем стандартную отправку формы

        // Очищаем предыдущее сообщение об ошибке
        $('#alert_seller_general').empty();

        // Собираем данные формы
        const formData = $(this).serialize();

        $.ajax({
            url: '/seller/settings', // URL для отправки
            type: 'POST', // Метод запроса
            data: formData, // Данные формы
            dataType: 'json', // Ожидаемый тип ответа
            success: function(response) {
                console.log(response);
                if (response.success === true) {
                    // Если запрос успешен, показываем сообщение об успехе
                    $('#alert_seller_general').html(
                        '<div class="alert alert-success">Данные успешно сохранены!</div>'
                    );
                } else if (response.errors) {
                    // Если в ответе есть ошибка, показываем её
                    $(".form-control").removeClass('is-invalid');
                    var error = response.message;
                    $.each(response.errors, function(key, value) {
                        error += "<br>"+value;
                        $("[name=\""+key+"\"]").addClass('is-invalid');
                    });
                    $('#alert_seller_general').html(
                        '<div class="alert alert-danger">' + error + '</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                // Обработка ошибок AJAX-запроса (сетевые ошибки и т. д.)
                let errorMessage = 'Произошла ошибка при отправке данных.';

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                $('#alert_seller_general').html(
                    '<div class="alert alert-danger">' + errorMessage + '</div>'
                );
            }
        });
    });

    var cookieHelper = {
        set: function(name, value, days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                var expires = "; expires=" + date.toUTCString();
            } else {
                var expires = "";
            }
            document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
        },
        get: function(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
            return null;
        }
    };

    // Обработчик изменения значения в select
    $('#SellerSelect').on('change', function() {
        // Сохраняем выбранное значение в куки (до закрытия браузера — без указания дней)
        cookieHelper.set('selectedSellerId', $(this).val());
        location.reload();
    });
});


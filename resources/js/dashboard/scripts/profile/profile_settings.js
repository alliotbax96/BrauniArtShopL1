// Константы
const API_TOKEN = "1a95c0aa5a5f5afd90ffd22d22cd9715288d0c23";
const SELECTORS = {
    addressInput: "#addressInput",
    companyName: "#company_name",
    companyAddress: "#company_address",
    zipCodeInput: "#zipCodeInput_1",
    deliveryForm: "#delivery_form",
    userData: "#user_data",
    alertProfile: "#alert_profile",
    alertAddress: "#alert_address",
    userAddress: "#user_address",
    pointMap: "#point_map_collapse",
    requisites: "#requisites",
    requisitesAlert: "#requisitesAlert",
    bankName: "#bank_name",
    userLegals: "#user_legals",
    CardAlert: "#CardAlert"
};

document.addEventListener('DOMContentLoaded', function(){
    CardInfo.setDefaultOptions({
        banksLogosPath: '/assets/img/cardLogo/banks-logos/',
        brandsLogosPath: '/assets/img/cardLogo/brands-logos/'
    })

    $(function() {
        $(".bankCard").each(function(){
            var cardInfo = new CardInfo($(this).find('.pan').html().split('*')[0].trim());
            console.log($(this).find('.pan').html().split('*')[0].trim())
            console.log(cardInfo);
            $(this).children('div').css('background', cardInfo.backgroundGradient);
            // $(this).find('.bankcard-number').css('color', cardInfo.textColor);
            $(this).find('img').prop('src', cardInfo.bankLogo);
        });
    })
});

// Инициализация подсказок
function initSuggestions() {
    // Адресная подсказка
    $(SELECTORS.addressInput).suggestions({
        token: API_TOKEN,
        type: "ADDRESS",
        onSelect: handleAddressSelect
    });

    // Подсказка для компаний
    $(SELECTORS.companyName).suggestions({
        token: API_TOKEN,
        type: "PARTY",
        onSelect: handleCompanySelect
    });

    // Подсказка для адреса компании
    $(SELECTORS.companyAddress).suggestions({
        token: API_TOKEN,
        type: "ADDRESS"
    });

    $(SELECTORS.bankName).suggestions({
        token: API_TOKEN,
        type: "BANK",
        onSelect: handleBankSelect
    });
}

// Обработчики выбора подсказок
function handleAddressSelect(suggestion) {
    $("input[name='city']").val(suggestion.data.city).addClass('is-valid');
    $("input[name='country']").val(suggestion.data.country).addClass('is-valid');
    $("input[name='zip_code']").val(suggestion.data.postal_code).addClass('is-valid');
    $(SELECTORS.addressInput).addClass('is-valid');
}
function handleBankSelect(suggestion){
    $("input[name='bik']").val(suggestion.data.bic).addClass('is-valid');
    $("input[name='correspondent_account']").val(suggestion.data.correspondent_account).addClass('is-valid');
    $(SELECTORS.bankName).addClass('is-valid');
}
function handleCompanySelect(suggestion) {
    const isLegalEntity = suggestion.data.inn.length === 10;

    $('#ipinput').prop('checked', !isLegalEntity);

    if (isLegalEntity) {
        $('.ul').removeClass('hidden');
        $("#company_kpp").val(suggestion.data.kpp);
        $("#company_manager").val(suggestion.data.management?.name || '');
    } else {
        $('.ul').addClass('hidden');
        $("#company_inn").attr('maxlength', 13);
    }

    $("#company_inn").val(suggestion.data.inn);
    $("#company_ogrn").val(suggestion.data.ogrn);
}
// Общие функции для AJAX-запросов
function handleFormSubmit(url, form, successCallback, errorCallback) {
    $.post(url, $(form).serialize(), function(data) {
        const response = typeof data === 'string' ? jQuery.parseJSON(data) : data;
        if (response.result) {
            successCallback(response);
        } else {
            errorCallback(response);
        }
    });
}
// Инициализация обработчиков форм
function initFormHandlers() {
    // Форма доставки
    $(SELECTORS.deliveryForm).on("submit", function(e) {
        e.preventDefault();
        handleFormSubmit('/profile/settings/delivery/add_address', this,
            function(data) {
                $(SELECTORS.userAddress).prepend(data.html);
                $(SELECTORS.alertAddress).html('');
            },
            function(data) {
                $(SELECTORS.alertProfile).html(`<div class="alert alert-danger" role="alert">${data.error}</div>`);
            }
        );
    });

    // Профиль пользователя
    $(SELECTORS.userData).on('submit', function(e) {
        e.preventDefault();

        const form = this;
        const $alertContainer = $(SELECTORS.alertProfile);

        $alertContainer.html('');
        $('input').removeClass('is-invalid');

        $.ajax({
            url: '/profile',
            method: 'POST',
            data: $(form).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.result === true) {
                    $alertContainer.html(
                        '<div class="alert alert-success" role="alert">Информация успешно обновлена!</div>'
                    );
                    $('input').removeClass('is-invalid');
                } else {
                    if (response.errors) {
                        let errorHtml = '';
                        Object.keys(response.errors).forEach(field => {
                            const $input = $(`#${field}`);
                            if ($input.length) {
                                $input.addClass('is-invalid');
                            }
                            response.errors[field].forEach(errorMsg => {
                                errorHtml += `${errorMsg}<br>`;
                            });
                        });
                        $alertContainer.html(
                            `<div class="alert alert-danger" role="alert">${errorHtml}</div>`
                        );
                    } else if (response.message) {
                        $alertContainer.html(
                            `<div class="alert alert-danger" role="alert">${response.message}</div>`
                        );
                    }
                }
            },
            error: function(xhr) {
                let message = 'Произошла непредвиденная ошибка';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                $alertContainer.html(
                    `<div class="alert alert-danger" role="alert">${message}</div>`
                );
            }
        });
    });


    // Реквизиты
    $(SELECTORS.requisites).on("submit", function(e) {
        e.preventDefault();
        handleFormSubmit('/profile/pay/legal', this,
            function(data) {
                $(SELECTORS.requisitesAlert).html('<div class="alert alert-success" role="alert">'+data.message+'</div>');
                if(data.id){
                    $(".legal_data[data-id=\""+data.id+"\"]").html(data.html);
                } else {
                    $(SELECTORS.userLegals).append(data.html);
                    $(SELECTORS.requisites).trigger("reset");
                }
                $(".block-null").remove();
            },
            function(data) {
                console.log(SELECTORS.requisitesAlert);
                $(SELECTORS.requisitesAlert).html('<div class="alert alert-danger" role="alert">'+data.message+'</div>');
            }
        );
    });
}
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
        $.post('/profile/delivery', {
            client_pvz: data.id,
            pvz_name: data.address.full_address,
            pvz_kode: null,
            _token: $('meta[name="csrf-token"]').attr('content') // добавляем CSRF‑токен
        }, function(response) {
            const parsedData = typeof response === 'string' ? jQuery.parseJSON(response) : response;
            if (parsedData.result) {
                $(SELECTORS.pointMap).hide();
                $(SELECTORS.userAddress).append(parsedData.html);
                $(".block-null").remove();
            } else {
                alert(parsedData.error);
                console.log(parsedData);
            }
        });
    });
}
// Вспомогательные функции
function initEventHandlers() {
    // Отключение OpenID
    $(".openid_off").click(function() {
        const element = $(this);
        if (confirm('Вы уверены, что хотите отключить сервис?')) {
            if (!element.is(':checked')) {
                const url = `/profile/detach/${element.attr('data-service')}`;
                $.post(url, {
                    code: '00',
                    _token: $('meta[name="csrf-token"]').attr('content') // получаем CSRF из мета‑тега
                }, function(data) {
                    console.log(data);
                    if (data.result) {
                        location.reload();
                    } else {
                        $("#alert_profile").html('<div class="alert alert-danger" role="alert">'+data.message+'</div>');
                    }
                });
            }
        }
    });

    // Удаление адреса доставки
    $(".del_address").click(function() {
        const element = $(this);
        if (confirm('Вы уверены, что хотите удалить адрес доставки?')) {
            $.ajax({
                url: '/profile/delivery/' + element.attr('data-addressid'),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data === true || data.success === true) {
                        element.closest('.block-div').remove();
                    } else {
                        alert('Не удалось удалить адрес. Попробуйте ещё раз.');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Ошибка удаления адреса:', textStatus, errorThrown);
                    alert('Произошла ошибка при удалении адреса.');
                }
            });
        }
    });

    // Удаление реквизитов
    $(".del_legal").click(function() {
        const element = $(this);
        if (confirm('Вы уверены, что хотите удалить реквизиты?')) {
            $.ajax({
                url: '/profile/pay/legal/' + element.attr('data-id'),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data === true || data.success === true) {
                        $(SELECTORS.requisitesAlert).html('<div class="alert alert-success" role="alert">'+data.message+'</div>');
                        element.closest('.block-div').remove();
                    } else {
                        $(SELECTORS.requisitesAlert).html('<div class="alert alert-danger" role="alert">Не удалось удалить адрес. Попробуйте ещё раз.</div>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $(SELECTORS.requisitesAlert).html('<div class="alert alert-danger" role="alert">'+data.message+'</div>');
                }
            });
        }
    });

    $(".del_card").click(function (){
        const element = $(this);
        if (confirm('Вы уверены, что хотите отвязать карту?')) {
            $.ajax({
                url: '/profile/pay/RemoveCard/' + element.attr('data-id'),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data === true || data.success === true) {
                        $(SELECTORS.CardAlert).html('<div class="alert alert-success" role="alert">'+data.message+'</div>');
                        location.reload();
                    } else {
                        $(SELECTORS.CardAlert).html('<div class="alert alert-danger" role="alert">'+data.message+'</div>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $(SELECTORS.CardAlert).html('<div class="alert alert-danger" role="alert">'+data.message+'</div>');
                }
            });
        }
    })
}
// Инициализация при готовности документа
$(document).ready(function() {
    initSuggestions();
    initFormHandlers();
    initWidgets();
    initEventHandlers();

    // Маска для почтового индекса
    $(SELECTORS.zipCodeInput).mask("999999");
});
// Глобальная функция для карт
$(document).ready(function() {
    // Делегирование событий для динамически добавляемых элементов
    $(document).on('click', '.edit_legal', function(e) {
        e.preventDefault();
        const $button = $(this);
        const legalId = $button.data('id');

        editLegalEntity(legalId);
    });
});
function editLegalEntity(legalId) {
    fetch(`/profile/pay/legal/${legalId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data) {
                fillLegalForm(data.data);
                // Прокручиваем к форме реквизитов
                const formSection = document.querySelector('#subscriptionTab');
                if (formSection) {
                    formSection.scrollIntoView({ behavior: 'smooth' });
                }
            } else {
                showError('Не удалось загрузить данные организации');
            }
        })
        .catch(error => {
            console.error('Ошибка при загрузке данных:', error);
            showError('Произошла ошибка при загрузке данных. Попробуйте ещё раз.');
        });
}
function fillLegalForm(legalData) {
    const formId = 'requisites';
    const $form = $(`#${formId}`);

    if (!$form.length) {
        console.error('Форма с id="' + formId + '" не найдена');
        return;
    }

    const fieldMappings = {
        'legal_name': 'legal_name',
        'inn': 'inn',
        'kpp': 'kpp',
        'ogrn': 'ogrn',
        'director_name': 'director_name',
        'is_vat_payer': 'is_vat_payer',
        'bank_name': 'bank_name',
        'bik': 'bik',
        'correspondent_account': 'correspondent_account',
        'account_number': 'account_number'
    };

    $.each(fieldMappings, function(dataKey, formField) {
        // Ищем поле ввода
        const $input = $(`input[form="${formId}"][name="${formField}"]`);

        if ($input.length) {
            let value = legalData[dataKey];
            const shouldHide = value === null || value === undefined || value === '' || value.toString().trim() === '';

            // Заполняем поле
            if ($input.attr('type') === 'checkbox') {
                $input.prop('checked', !!value);
            } else {
                $input.val(value || '');
            }

            // Определяем ID блока для скрытия на основе структуры вёрстки
            let $containerToHide = null;

            if (formField === 'kpp') {
                // Для КПП используем ID блока из вёрстки
                $containerToHide = $('#company_kpp_block');
            } else if (formField === 'director_name') {
                // Для директора предполагаем похожий шаблон ID
                $containerToHide = $('#director_name_block');
            }

            // Применяем скрытие/показ
            if ($containerToHide && $containerToHide.length) {
                if (shouldHide) {
                    $containerToHide.hide();
                } else {
                    $containerToHide.show();
                }
            } else if (formField === 'kpp' || formField === 'director_name') {
                // Резервный вариант: скрываем ближайший .row родительский блок
                const $fallbackContainer = $input.closest('.row');
                if ($fallbackContainer.length) {
                    if (shouldHide) {
                        $fallbackContainer.hide();
                    } else {
                        $fallbackContainer.show();
                    }
                }
            }
        }
    });

    // Обрабатываем поле ID
    const $idInput = $(`[name="id"]`);
    if ($idInput.length) {
        $idInput.val(legalData.id || '');
    } else {
        const $newInput = $('<input>', {
            type: 'hidden',
            name: 'id',
            value: legalData.id || '',
            form: formId
        });
        $('body').append($newInput);
    }

    toggleKppField(legalData.isIndividualEntrepreneur);
}
function toggleKppField(isIndividualEntrepreneur) {
    const kppBlock = document.getElementById('company_kpp_block');
    const kppInput = document.getElementById('company_kpp');

    if (kppBlock && kppInput) {
        if (isIndividualEntrepreneur) {
            kppBlock.style.display = 'none';
            kppInput.value = '';
        } else {
            kppBlock.style.display = 'flex';
        }
    }
}
function showError(message) {
    const alertDiv = document.getElementById('requisitesAlert');
    if (alertDiv) {
        alertDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    } else {
        alert(message);
    }
}

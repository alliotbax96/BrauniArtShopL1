$("#company_name").suggestions({
    token: "1a95c0aa5a5f5afd90ffd22d22cd9715288d0c23",
    type: "PARTY",
    /* Вызывается, когда пользователь выбирает одну из подсказок */
    onSelect: function (suggestion) {
        if (suggestion.data.inn.length == 10) {
            $('#ipinput').prop('checked', false);
            $('.ul').removeClass('hidden');
            //   $('.ul').attr('disabled', false);
            $("#company_kpp").val(suggestion.data.kpp);
            $("#company_manager").val(suggestion.data.management.name);
        } else {
            $('#ipinput').prop('checked', true);
            $("#company_inn").attr('maxlength', 13);
            $('.ul').addClass('hidden');
        }
        $("#company_inn").val(suggestion.data.inn);
        $("#company_ogrn").val(suggestion.data.ogrn);
        $("#company_address").val(suggestion.data.address.value);
    }
});

$("#ipinput").on("change", function(){
    if ($('#ipinput').is(':checked')){
        $('.ul').addClass('hidden');
    } else {
        $('.ul').removeClass('hidden');
    }
});
$('#company_inn').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
$('#company_kpp').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
})
$('#company_ogrn').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
$('#company_manager').on('input', function () {
    this.value = this.value.replace(/[^a-zа-яё\s]/gi, '');
});
$('#bank_bik').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
$('#bank_cor_account').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
$('#bbank_account').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$("#bank_name").suggestions({
    token: "1a95c0aa5a5f5afd90ffd22d22cd9715288d0c23",
    type: "BANK",
    /* Вызывается, когда пользователь выбирает одну из подсказок */
    onSelect: function (suggestion) {
        $("#bank_bik").val(suggestion.data.bic);
        $("#bank_cor_account").val(suggestion.data.correspondent_account);
    }
});

$("#company_address").suggestions({
    token: "1a95c0aa5a5f5afd90ffd22d22cd9715288d0c23",
    type: "ADDRESS",
    hint: false,
    bounds: "city-settlement"
});

$("#payment_partner_data").on('submit', function(){
    $.post('/partner_program/partner_settings/submit/', $("#payment_partner_data").serialize(), function (data) {
        var result = jQuery.parseJSON(data);
        if (result.result === true) {
            $('input[form="payment_partner_data"]').prop("disabled", true);
            $('select[form="constructor_url"]').prop("disabled", false);
            $("#alert_payment_partner_data").html(result.message);
            $("#shop_href").val('https://brauniart.shop/?partner='+result.partner_kode);
            $("#seller_href").val('https://id.brauniart.shop/?type=seller&partner='+result.partner_kode);
            $('input[name="partner_kode"]').val(result.partner_kode);
        } else {
         $("#alert_payment_partner_data").html(result.message);
        }
    });
});

$("#product_constructor_url").on("change", function (){
var base_url = 'https://brauniart.shop/products/';
var partner_kode = $('input[name="partner_kode"]').val();
var product_id = $("#product_constructor_url").val();
$("#shop_href").val(base_url+product_id+"/?partner="+partner_kode);
});
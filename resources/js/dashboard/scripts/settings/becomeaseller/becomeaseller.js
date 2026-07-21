
$('.mask-phone').mask('+7 (999) 999-99-99');

$("#company_name").suggestions({
    token: "1a95c0aa5a5f5afd90ffd22d22cd9715288d0c23",
    type: "PARTY",
    /* Вызывается, когда пользователь выбирает одну из подсказок */
    onSelect: function(suggestion) {
        if(suggestion.data.inn.length == 10){
          $('#ipinput').prop('checked', false);
          $('.ul').removeClass('hidden');
          $("#company_inn").attr('maxlength', 10);
          $("#company_kpp").val(suggestion.data.kpp);
          $("#company_manager").val(suggestion.data.management.name);
        } else {
          $('#ipinput').prop('checked', true);
          $("#company_inn").attr('maxlength', 13);
          $('.ul').addClass('hidden');
        //   $('.ul').attr('disabled', true);
        }
        $("#company_inn").val(suggestion.data.inn);
        $("#company_ogrn").val(suggestion.data.ogrn);
        $("#company_address").val(suggestion.data.address.value);
    }
});

$("#bank_name").suggestions({
  token: "1a95c0aa5a5f5afd90ffd22d22cd9715288d0c23",
  type: "BANK",
  /* Вызывается, когда пользователь выбирает одну из подсказок */
  onSelect: function(suggestion) {
      $("#bank_bik").val(suggestion.data.bic);
      $("#bank_cor_account").val(suggestion.data.correspondent_account);
  }
});

$("#seller_general").on('submit', function(){
  $.post('/become_a_seller/submit/', $(this).serialize(), function(data){
    var res = JSON.parse(data);
    if(res.result){
    window.location.href = '/';
    } else {
    $("#alert_settings").html(res.message);
    window.scrollTo(0, 0);
    }
});
});

$("#ipinput").on("change", function(){
if ($('#ipinput').is(':checked')){
	$('.ul').addClass('hidden');
} else {
    $('.ul').removeClass('hidden');
}
});

$('#company_inn').on('input', function(){
	this.value = this.value.replace(/[^0-9]/g, '');
});
$('#company_kpp').on('input', function(){
	this.value = this.value.replace(/[^0-9]/g, '');
});
$('#company_ogrn').on('input', function(){
	this.value = this.value.replace(/[^0-9]/g, '');
});
$('#company_manager').on('input', function(){
	this.value = this.value.replace(/[^a-zа-яё\s]/gi, '');
});
$('#bank_bik').on('input', function(){
	this.value = this.value.replace(/[^0-9]/g, '');
});
$('#bank_cor_account').on('input', function(){
	this.value = this.value.replace(/[^0-9]/g, '');
});
$('#bank_account').on('input', function(){
	this.value = this.value.replace(/[^0-9]/g, '');
});


function timer(num) {
    var highestTimeoutId = setTimeout(";");
    for (var i = 0; i < highestTimeoutId; i++) {
        clearTimeout(i);
    }
    var timerBlock = $('.seconds');
    var index = num;
    var timerId = setInterval(function () {
        timerBlock.html(--index);
    }, 1000);

    setTimeout(function () {
        clearInterval(timerId);
        $("#get_sms").parent().html('<a href="#" id="get_sms" class="fs-11 text-primary">Получить код повторно</a>');
        $("#get_sms").click(function () {
            $.post('/sensmskode', { phone: $(".mask-phone").val() }, function (data) {
                if (data == true) {
                    alert('Вам отправлено СМС с кодом!');
                    $("#get_sms").parent().html('<span id="get_sms" class="fs-11 text-primary">Получить код повторно через <span class="seconds">120</span> сек</span>');
                    timer(60);
                } else {
                    alert(data);
                }
            });

        });
    }, num * 1000);

}

$.mask.definitions['h'] = "[0|1|3|4|5|6|7|9]"
$(".mask-phone").mask("+7 (h99) 999-99-99", {
    completed: function () {
        // removeclass("#" + $(this).attr('id'));
        $.post('/signup', { phone: $(this).val(), type: 'authcode' }, function (data) {
            // console.log(data);
            var result = jQuery.parseJSON(data);
            if (result.result === true) {
                $("input[name=\"phone\"]").addClass('is-valid');
                $("#kode").removeClass('hidden');
                $("#alert").html('<div class="alert alert-warning" role="alert">Сейчас вам позвонит наш робот, введите последние четыре цифры номер с которого поступил звонок!</div');
                $("#get_sms").html('Получить код повторно через <span class="seconds">60</span> сек</a>');
                timer(60);
            }
        });
    }
});

$("input[name=\"firstname\"]").on('change', function(){
    $("input[name=\"firstname\"]").removeClass('is-invalid');
    $("input[name=\"firstname\"]").addClass('is-valid');
});
$("input[name=\"lastname\"]").on('change', function(){
    $("input[name=\"lastname\"]").removeClass('is-invalid');
    $("input[name=\"lastname\"]").addClass('is-valid');
});
$("input[name=\"email\"]").on('change', function(){
    $("input[name=\"email\"]").removeClass('is-invalid');
    $("input[name=\"email\"]").addClass('is-valid');
});

$(".kode").mask("9999", {
    completed: function () {
        // removeclass("#" + $(this).attr('id'));
        $.post('/signup', { code: $(this).val(), phone: $('.mask-phone').val(), type: 'checkcode' }, function (data) {
            var result = jQuery.parseJSON(data);
            if (result.result === true) {
                $("#get_sms").html('');
                $(".kode").addClass('is-valid');
                $(".kode").removeClass('is-invalid');
                $("#alert").html('');
            } else {
                $("#alert").html('<div class="alert alert-danger" role="alert">'+result.error+'</div>');
                $("input[name=\""+result.input+"\"]").addClass('is-invalid');
            }
        });
    }
});

$("#auth_signup").on('submit', function(){
 var check_input = $('.is-valid').length;
 if(check_input >= 5 && $("input[name=\"termsCondition\"]").is(':checked')){
    $.post('/signup/submit', $(this).serialize(), function(data){

        var result = jQuery.parseJSON(data);
        if (result.result === true) {
            window.location.href = "/";
        } else {
            $("#alert").html('<div class="alert alert-danger" role="alert">'+result.error+'</div>');
            $('input[name="'+result.input+'"]').addClass('is-invalid');
            $('input[name="'+result.input+'"]').removeClass('is-valid');
        }
    });
 } else {
    $("#alert").html('<div class="alert alert-danger" role="alert">Проверьте правильность заполнения полей!</div>');
 }
});
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
            $.post('/SendSmsCode', { phone: $("input[name=\"phone\"]").val() }, function (data) {
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

$(".mask_phone").mask("+7 (999) 999-99-99", {
    onComplete: function () {
        $.post('/auth/code', $("#login").serialize(), function (result) {
            if (result.result === true) {
                $("input[name=\"phone\"]").removeClass('is-invalid');
                $("input[name=\"phone\"]").addClass('is-valid');
                $("input[name=\"code\"]").parent().removeClass('hidden');
                $("#alert").html('<div class="alert alert-warning" role="alert">Сейчас вам позвонит наш робот, введите последние четыре цифры номер с которого поступил звонок!</div');
                $("#get_sms").html('Получить код повторно через <span class="seconds">60</span> сек</a>');
                timer(60);
            }
        });
    }
});
$(".code").mask("9999", {
    onComplete: function () {

        $.post('/auth/checkCode', $("#login").serialize(), function (result) {
            if (result.result === true) {
                $("input[name=\"code\"]").addClass('is-valid');
                $("input[name=\"code\"]").removeClass('is-invalid');
                $("#get_sms").html('');
                $("#alert").html('');
            } else {
                $("input[name=\"code\"]").removeClass('is-valid');
                $("input[name=\"code\"]").addClass('is-invalid');
                $("#alert").html('<div class="alert alert-danger" role="alert">'+result.error+'</div>');
            }
        });
    }
});

$("#login").on("submit", function (){
    $.post('/auth/login', $(this).serialize(), function (result){
        if(result.result === true){
            window.location.href = "/";
        } else {
            $("input[name=\"code\"]").removeClass('is-valid');
            $("input[name=\"code\"]").addClass('is-invalid');
            $("input[name=\"phone\"]").removeClass('is-valid');
            $("input[name=\"phone\"]").addClass('is-invalid');
            $("#alert").html('<div class="alert alert-danger" role="alert">'+result.error+'</div>');
        }
    });
});

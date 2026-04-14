document.addEventListener('DOMContentLoaded', function(){
    CardInfo.setDefaultOptions({
        banksLogosPath: '/assets/img/cardLogo/banks-logos/',
        brandsLogosPath: '/assets/img/cardLogo/brands-logos/'
    })

    $(function() {
        $(".user-card").each(function(){
            var cardInfo = new CardInfo($(this).children('input').attr('data-pan').split('*')[0]);
            $(this).find('label').css('background', cardInfo.backgroundGradient);
            $(this).find('.bankcard-number').css('color', cardInfo.textColor);
            $(this).find('img').prop('src', cardInfo.bankLogo);
        });
    })
});

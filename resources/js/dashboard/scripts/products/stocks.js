
$("#save").on('click', function(){
    $.ajax({
        url: '/products/stoks/update',
        method: 'post',
        dataType: 'html',
        data: $("#stocks").serialize(),
        success: function(data){
            console.log(data);
            $('input[form="stocks"]').addClass('is-valid');
        }
    });

})
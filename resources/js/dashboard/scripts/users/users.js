$(".user_group_edit").on("change", function(){
    var $this = $(this);
    $.post('/users/users/edit_group', {id: $this.val(), user_id: $this.attr('data-id')}, function (data) {
        var result = jQuery.parseJSON(data);
        if(result.result){
            $this.addClass('is-valid');
        } else {
            $this.addClass('is-invalid');s
            alert(result.message);
        }
        // console.log(data);
    });
});

$(".delete_user").on('click', function(){
    if(confirm("Вы уверены, что хотите отключить пользователя от вашего магазина?")) {
        $.post('/users/users/delete', {id: $(this).attr('data-id')}, function (data) {
            var result = jQuery.parseJSON(data);
            if (result.result) {
                // console.log(data);
                location.reload();
            } else {
                alert(result.message);
            }
        });
    }
});

$("#add_user").on("submit", function(){
    $.post('/users/users/add_invitation', $(this).serialize(), function (data){
        var result = jQuery.parseJSON(data);
        if (result.result) {
            $("#add_user_form").html(result.form);
            $("#addUserAlert").html(result.error);
            $('#submit_button').html("ОК");
            $('#cancel').remove();
            $('#submit_button').attr('type', 'button');
            $('#submit_button').attr('data-bs-dismiss', "modal");
        } else {
            $("#addUserAlert").html(result.error);
        }
    });
});
$("#users_group_edit").on("submit", function(){
    $.post('/users/users_group/edit/submit', $("#users_group_edit").serialize(), function (data) {
        var result = jQuery.parseJSON(data);
        if(result.result){
            window.location.href = "/users/users_group/";
        } else {
            $("#alert_users_group_edit").html(result.message);
        }
        // console.log(data);
    });
});
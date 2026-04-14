$("#users_group_create").on("submit", function(){
    $.post('/users/users_group/create/submit', $("#users_group_create").serialize(), function (data) {
        var result = jQuery.parseJSON(data);
        if(result.result){
           window.location.href = "/users/users_group/";
        } else {
            $("#alert_users_group_create").html(result.message);
        }
        // console.log(data);
    });
});
function del_group(id){
    if (confirm('Вы уверенны, что хотите удалить роль?')) {
        $.post('/users/users_group/delete', {id: id}, function (data) {
            var result = jQuery.parseJSON(data);
            if(result.result){
                alert(result.message);
                location.reload();
            } else {
                alert(result.message);
            }
            // console.log(data);
        });
    }
}
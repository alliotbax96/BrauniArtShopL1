const addModal = new bootstrap.Modal('#addModal', {
    keyboard: false
})
$("#add_category").on('submit', function (){
    $.post('/admin_groups/create', $(this).serialize(), function (data) {
        if (data == 'true') {
            addModal.hide();
            location.reload();
        } else {
            $("addGroupAlert").html('<div class="alert alert-danger" role="alert">'+data+'</div>');
        }
    });
});

function group_delete(item_id){
    $.post('/admin_groups/delete',{id: item_id}, function (data) {
        if (confirm('Вы уверены, что хотите удалить категорию?')) {
            if (data == 'true') {
                location.reload();
            } else {
                alert(data);
            }
        }
    });
}
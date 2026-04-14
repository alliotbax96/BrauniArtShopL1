function assembly(id, warehouse){
    $.post('/orders_admin/admin_orders/assembling/', { order_id: id, warehouse: warehouse}, function (data) {
        window.location.href = "/orders_admin/assembled";
    });
}

function go_new(id){
    $.post('/orders_admin/admin_orders/go_new/', { order_id: id}, function (data) {
        window.location.href = "/orders_admin/admin_orders";
    });
}

function ship(id, warehouse){
    $.post('/orders_admin/assembled/ship/', { order_id: id, warehouse: warehouse}, function (data) {
        // console.log(data);
        window.location.href = "/orders_admin/in_delivery";
    });
}
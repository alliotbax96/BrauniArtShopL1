$(".payment_status").on('change', function () {
    var $this = $(this);
    $.post('/finance_admin/payments/change_status', {id: $this.attr('data-id') , status: $this.val()}, function (data) {});
});
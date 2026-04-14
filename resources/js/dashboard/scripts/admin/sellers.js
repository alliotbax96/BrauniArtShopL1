$(".contract_status").on('change', function () {
    var $this = $(this);
    $.post('/sellers/change_status', {id: $this.attr('data-id') , status: $this.val()}, function (data) {});
});
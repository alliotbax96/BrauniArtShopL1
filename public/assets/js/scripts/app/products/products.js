$("#sort_select").on("input", function(e) {
    e.preventDefault();
    var sort = $(this).val().split("#");
    var url = new URL(window.location.href);
    url.searchParams.set('sort', sort[0]); // param=value
    url.searchParams.set('sortDir', sort[1]); // param=value
    window.location.href = url.toString();
});

$(".pagination_click").on("click", function(e) {
    e.preventDefault();
    var url = new URL(window.location.href);
    url.searchParams.set('page', $(this).attr('data-page')); // param=value
    window.location.href = url.toString();
});

$("#filter_apply").click(function(e){
    var prices = $("#amount").val().split(" ");
    e.preventDefault();
    var url = new URL(window.location.href);
    url.searchParams.set('filter', true); // param=value
    url.searchParams.set('minPrice', prices[0]); // param=value
    url.searchParams.set('maxPrice', prices[2]); // param=value
    window.location.href = url.toString();
});
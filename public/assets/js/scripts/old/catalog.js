$("#sort_select").on("input", function(){
$.post('/url_gen/'+window.location.search, {sort: $(this).val()}, function(data){
  var url = window.location.pathname+"?"+data;
  window.location.href = url;
});
});

$(function(){	
	$('.list').each(function(){
		var column = 0;
		$(this).children().each(function(){
			h = $(this).height();
			if (h > column) {
				column = h;
			}
		});
		$(this).children().height(column);
	});
});
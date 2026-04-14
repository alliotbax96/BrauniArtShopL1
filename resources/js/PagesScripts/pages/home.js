var block_show = false;

function scrollMore(){
    var $target = $('#showmore-triger');

    if (block_show) {
        return false;
    }

    var wt = $(window).scrollTop();
    var wh = $(window).height();
    var et = $target.offset().top;
    var eh = $target.outerHeight();
    var dh = $(document).height();

    if (wt + wh >= et || wh + wt == dh || eh + et < wh){
        var page = $target.attr('data-page');
        page++;
        block_show = true;

        $.ajax({
            url: '/home/ajax?page=' + page,
            dataType: 'html',
            success: function(data){
                if(data == 0){
                    page = 1;
                    $.ajax({
                        url: '/home/ajax?page=' + page,
                        dataType: 'html',
                        success: function(data){
                            $('#showmore-list .prod-list').append(data);
                            block_show = false;
                        }
                    });
                } else {
                    $('#showmore-list .prod-list').append(data);
                    block_show = false;
                }
            }
        });
        $target.attr('data-page', page);
    }
}

$(window).scroll(function(){
    scrollMore();
});

$(document).ready(function(){
    scrollMore();
});


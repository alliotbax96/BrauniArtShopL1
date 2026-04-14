function prod_type() {
    if ($("#project-type").children('fieldset').length > 1) {
        $("#project-type").children('fieldset').last().remove();
        $(".scroll_point").remove();
    }
    var value = $('input[name="project-type"]:checked').val()
    $.post('/admin_products/admin_manage_products/create/select_type', { type: value }, function (data) {
        var result = jQuery.parseJSON(data);
        if (result.type == 'sub_type') {
            $("#project-type").append('<hr class="mb-5 scroll_point"></hr>');
            $("#project-type").append(result.form);
            $('html, body').animate({
                scrollTop: $('.scroll_point').offset().top
            }, 'slow');
        }
    });
};

function prod_subtype() {
    var h = $("ul[role=\"tablist\"]").children().length;
    for (let i = 3; i <= h; i++) {// выводит 0, затем 1, затем 2     
        $('#project-create-steps').steps('remove', 3);
    }
    var value = $('input[name="product_subtype"]:checked').val();
    $.post('/admin_products/admin_manage_products/create/select_subtype', { type: value }, function (data) {
        var result = jQuery.parseJSON(data);
        $('#project-create-steps').steps('insert', 3, {
            title: 'Цены',
            content: result.price,
        });
        if (result.params.status) {
            $('#project-create-steps').steps('insert', 4, {
                title: 'Параметры',
                content: result.params.form,
            });
            $("#productParamSelect").select2({
                theme: "bootstrap-5",
                templateResult: bgformat,
                templateSelection: bgformat
            })
            $('#productParamSelect').on('select2:select', function (e) {
                var data = e.params.data;
                $.post('/admin_products/admin_manage_products/create/select_params', { id: data.id, name: data.text }, function (result) {
                    $("#params_form").append(result);
                });
            });
            $('#productParamSelect').on('select2:unselect', function (e) {
                var data = e.params.data;
                $(".param" + data.id).remove();
            });
        }
        if (result.type.form) {
            var tab = 4;
            var form_arr = jQuery.parseJSON(result.type.form);
            $.each(form_arr, function (index, value) {
                tab++;
                // console.log(tab);
                $('#project-create-steps').steps('insert', tab, {
                    title: value.name,
                    content: value.form
                });
            });
            $('#c').change(function () {
                ajax_form();
            });
        }
    });
};


$(document).ready(function () {
    $(".file-upload").on("change", function () {
        var e, t;
        (e = this).files && e.files[0] && ((t = new FileReader).onload = function (e) {
            $(".upload-pic").attr("src", e.target.result)
        }, t.readAsDataURL(e.files[0]))
    }), $(".upload-button").on("click", function () {
        $(".file-upload").click()
    })
}), $(document).ready(function () {
    var e = document.getElementById("dateofBirth");
    new Datepicker(e, {
        clearBtn: !0,
        allowOneSidedRange: !0
    })
});

function weight_Input(id) {
    var elm = $("#" + id);
    elm.val() = elm.val().replace(/[^0-9.]/, '');
};

// $('#Inputprice').on('input', function () {
// 	var dom = $(this);
// 	$.post('/products/form/get_comission/', { id: dom.attr('data-group-id') }, function (data) {
// 		var data = dom.val() / 100 * (100 - Number(data));
// 		$("#comission_none").html(data);
// 	});
// 	this.value = this.value.replace(/[^0-9]/, '');
// });

function initWizard() {
    $("#wizard-4").steps({
        headerTag: "div",
        bodyTag: "section",
        enableAllSteps: true,
        enablePagination: false
    });
}

function ajax_form() {
    $('#js-form')[0].reset();
}

$(".delete_product").on('click', function(){
if (confirm('Вы точно хотите удалить товар?')) {
    $.post('/admin_products/admin_manage_products/delete/',{product_id: $(this).attr('data-id')},function(data){
     alert('Товар удален!');
     location.reload();
    });
}
});

function remove_img(target){
    $(target).parent().parent().parent().parent().remove();
}
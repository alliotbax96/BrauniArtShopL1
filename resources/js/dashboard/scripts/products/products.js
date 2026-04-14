function prod_type() {
    if ($("#project-type").children('fieldset').length > 1) {
        $("#project-type").children('fieldset').last().remove();
        $(".scroll_point").remove();
    }
    var value = $('input[name="project-type"]:checked').val()
    $.post('/products/manage_products/create/select_type', { type: value }, function (data) {
        var result = jQuery.parseJSON(data);
        if (result.type == 'sub_type') {
            var parametrs = jQuery.parseJSON(result.parametrs);
            if(parametrs.files){
              $("#ProductWeight").attr('required', false);
              $("#ProductWeight").attr('disabled', true);
              $("#ProductWeight_required").addClass("hidden");
            } else {
                $("#ProductWeight").attr('required', true);
                $("#ProductWeight").attr('disabled', false);
                $("#ProductWeight_required").removeClass("hidden");
            }
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
    $.post('/products/manage_products/create/select_subtype', { type: value }, function (data) {
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
                $.post('/products/manage_products/create/select_params', { id: data.id, name: data.text }, function (result) {
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

            $( "#js-digital-file-list" ).sortable();

            $("#choose-digital-product-files").change(function(){
                $(this).prev("label").clone();
                var e = $("#choose-digital-product-files")[0].files[0].name;
                $(this).prev("label").text(e)
                // alert("Все сработало");
                if (window.FormData === undefined) {
                    alert('В вашем браузере загрузка файлов не поддерживается');
                } else {
                    var formData = new FormData();
                    $.each($("#choose-digital-product-files")[0].files, function(key, input){
                        formData.append('file[]', input);
                    });

                    $.ajax({
                        type: 'POST',
                        url: '/products/manage_products/create/temp_uploads_files',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        dataType : 'json',
                        success: function(msg){
                            msg.forEach(function(row) {
                                if (row.error == '') {
                                    $('#js-digital-file-list').append(row.data);
                                    mejs.i18n.language('ru');
                                    $('audio').mediaelementplayer({
                                        iconSprite: '/templates/assets/vendors/MediaElement/mejs-controls.svg',
                                        features: ['playpause']
                                    });
                                } else {
                                    alert(row.error);
                                }
                            });
                            $("#choose-digital-product-files").val('');
                            $("#choose-digital-product-files-label").html('Выберете файл');
                        }
                    });
                }
            });
        }
    });
};

function weight_Input(id) {
    var elm = $("#" + id);
    elm.val(elm.val().replace(/[^0-9.]/, ''));
};

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

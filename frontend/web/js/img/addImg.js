$(document).ready(function() {
    var addImgSending = false;

    $(document).on('submit', '#imgAddForm', function(event) {
        event.preventDefault();
        if (addImgSending) {
            // alert('Идет загрузка..');
            return false;
        }
        var $form = $(this);
        addImgSending = true;
        $form.find('.loading-info').removeClass('hide');
        $form.find('#btnImgUpload').addClass('hide');
        var url = $form.attr('action');
        var data = new FormData();
        $.each($('#imgUpload')[0].files, function(i, file) {
            data.append($('#imgUpload').attr('name'), file);
        });
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
                // Удачно загрузилось
                $form.find('.loading-info').addClass('hide');
                $form.find('#btnImgUpload').removeClass('hide');
                addImgSending = false;
                $('#imgAddForm').trigger('successUpload', data).trigger('reset');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $form.find('.loading-info').addClass('hide');
                $form.find('#btnImgUpload').removeClass('hide');
                addImgSending = false;
                $('#imgAddForm').trigger('errorUpload').trigger('reset');
            }
        });

        return false;
    });

    $(document).on('click', '#btnImgUpload', function() {
        $('#imgUpload').click();
    });

    $(document).on('change', '#imgUpload', function() {
        $(this).closest('form').submit();
    });

});

$(document).ready(function() {
    var addMusicSending = false;

    $(document).on('submit', '#musicAddForm', function(event) {
        event.preventDefault();
        if (addMusicSending) {
            // alert('Идет загрузка..');
            return false;
        }
        var $form = $(this);
        addMusicSending = true;
        $form.find('.loading-info').removeClass('hide');
        $form.find('#btnSoundUpload').addClass('hide');
        var url = $form.attr('action');
        var data = new FormData();
        $.each($('#soundUpload')[0].files, function(i, file) {
            data.append('Music[musicFile]', file);
        });
        data.append('Music[artist]', '');
        data.append('Music[title]', '');
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
                $form.find('#btnSoundUpload').removeClass('hide');
                addMusicSending = false;
                $('#musicAddForm').trigger('successUpload', data).trigger('reset');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $form.find('.loading-info').addClass('hide');
                $form.find('#btnSoundUpload').removeClass('hide');
                addMusicSending = false;
                $('#musicAddForm').trigger('errorUpload').trigger('reset');
            }
        });

        return false;
    });

    $(document).on('click', '#btnShare', function() {
        $('.pluso').show();
    });

    $(document).on('click', '#btnSoundUpload', function() {
        $('#soundUpload').click();
    });

    $(document).on('change', '#soundUpload', function() {
        $(this).closest('form').submit();
    });

});

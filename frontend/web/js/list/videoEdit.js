$(document).ready(function() {
    $(document).on('click', '#addVideoButton', function() {
        var $blockVideos = $('#blockVideos');
        var maxVideo = $(this).data('max-video');
        var $blockVideo = $('<div class="input-group margin-bottom"></div>').append([
            $('<input type="text" name="videos[]" class="form-control" />'),
            $('<span class="input-group-addon btn btn-default"><i class="glyphicon glyphicon-remove"></i></span>')
        ]);
        if ($blockVideos.find('div.input-group').length >= maxVideo) {
        } else {
            $blockVideos.append($blockVideo);
        }

        return false;
    });

    $('#blockVideos').on('click', '.input-group-addon', function() {
        var $this = $(this);
        $this.closest('div').remove();
    });
});
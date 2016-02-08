$(document).ready(function() {
    $(document).on('click', '#addVideoButton', function() {
        var $blockVideos = $('#blockVideos');
        var $blockVideo = $('<div class="input-group margin-bottom"></div>').append([
            $('<input type="text" name="videos[]" class="form-control" />'),
            $('<span type="submit" class=" input-group-addon btn btn-default">X</span>')
        ]);

        $blockVideos.append($blockVideo);

        return false;
    });

    $('#blockVideos').on('click', '.input-group-addon', function() {
        var $this = $(this);
        $this.closest('div').remove();
    });
});
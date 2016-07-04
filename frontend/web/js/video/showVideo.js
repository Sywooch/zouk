function showModalVideo($alink) {
    var $modalShowVideo = $('.modal-show-video');

    loadPlayer($alink.data('video-id'));

    $('.block-video-list ul').empty();
    var videoList = [];
    $alink.closest('.row').find('.video-link').each(function() {
        var $this = $(this);
        var alink = $('<li></li>').append($('<a href="javascript: void(0);">' + $this.data('title') + '</a>').data('video-id', $this.data('video-id')).click(function() {
            loadPlayer($this.data('video-id'));
        }));
        $('.block-video-list ul').append(alink);
        videoList.push($this.data('video-id'));
    });
    addList(videoList);

    $modalShowVideo.modal('show');
}

$(document).ready(function() {
    $(document).on('click', '.video-link', function() {
        showModalVideo($(this));
        return false;
    }).on('hide.bs.modal', function() {
        stopPlay();
    });
});
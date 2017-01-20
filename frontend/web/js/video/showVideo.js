$(document).ready(function () {
    var modalVideoPlayer = new videoPlayer();

    function showModalVideo($alink) {
        var $modalShowVideo = $('.modal-show-video');

        modalVideoPlayer.loadPlayer($alink.data('video-id'));

        $('.block-video-list').show();
        var $ulBlockViewsList = $('.block-video-list ul');
        $ulBlockViewsList.empty();
        var videoList = [];
        $alink.closest('.row').find('.video-link').each(function () {
            var $this = $(this);
            if (videoList.indexOf($this.data('video-id')) < 0) {
                var alink = $('<li></li>').append($('<a href="javascript: void(0);">' + $this.data('title') + '</a>').data('video-id', $this.data('video-id')).click(function () {
                    modalVideoPlayer.loadPlayer($this.data('video-id'));
                }));
                $ulBlockViewsList.append(alink);
                videoList.push($this.data('video-id'));
            }
        });
        modalVideoPlayer.addList(videoList);

        $modalShowVideo.modal('show');
    }

    function showModalRandomVideo($alink) {
        var $modalShowVideo = $('.modal-show-video');

        modalVideoPlayer.loadPlayer($alink.data('video-id'));
        $('.block-video-list').hide();

        $.ajax({
            url: $alink.data('random-video-url'),
            type: "POST",
            data: {
                exclude: [modalVideoPlayer.videoId]
            },
            dataType: 'json',
            success: function (data) {
                var videoList = [data['videoId']];
                modalVideoPlayer.addList(videoList);
            }
        });
        modalVideoPlayer.addListener('afterPlayerStateChange', function(event) {
            if (event.data == 0) {
                $.ajax({
                    url: $alink.data('random-video-url'),
                    type: "POST",
                    data: {
                        exclude: [modalVideoPlayer.videoId]
                    },
                    dataType: 'json',
                    success: function (data) {
                        var videoList = [data['videoId']];
                        modalVideoPlayer.addList(videoList);
                    }
                });
            }
        });

        $modalShowVideo.modal('show');
    }

    var $autoPlayVideo = $('.video-link.auto-play-video');
    if ($autoPlayVideo.length > 0) {
        var whileNotPlayerReady = function () {
            if (playerReady) {
                showModalVideo($($autoPlayVideo.get(0)));
            } else {
                setTimeout(whileNotPlayerReady, 30);
            }
        };
        whileNotPlayerReady();
    }

    $(document)
        .on('click', '.video-link', function () {
            showModalVideo($(this));
            return false;
        })
        .on('hide.bs.modal', function () {
            if (playerReady) {
                if (!modalVideoPlayer.stopPlay()) {
                    setTimeout(function() {
                        modalVideoPlayer.stopPlay();
                    }, 1500);
                }
            }
        })
        .on('click', '.video-random-link', function () {
            showModalRandomVideo($(this));

            return false;
        });
});
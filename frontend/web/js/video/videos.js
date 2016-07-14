
var playerReady = false;
function onYouTubeIframeAPIReady() {
    playerReady = true;
}

function videoPlayer() {
    var selfVideoPlayer = this;

    this.playerReady = false;
    this.player;
    this.playerList = [];
    this.videoId = '';

    var onYouTubeIframeAPIReady = function() {
        selfVideoPlayer.playerReady = true;
        $.each(listener['afterPlayerStateChange'], function() {
            this();
        });
    };

    var listener = {
        'afterPlayerStateChange': [],
        'onPlayerReady': []
    };

    this.addListener = function(event, callback) {
        listener[event].push(callback);
    };

    this.loadPlayer = function(videoId) {
        if (typeof selfVideoPlayer.player == "undefined") {
            selfVideoPlayer.player = new YT.Player('ytplayer', {
                height: '300px',
                width: '100%',
                videoId: videoId,
                playerVars:
                {
                    "enablejsapi":1,
                    "origin":document.domain,
                    "rel":0
                },
                events:
                {
                    "onReady": onPlayerReady,
                    "onError": onPlayerError,
                    "onStateChange": onPlayerStateChange
                }
            });
        } else {
            selfVideoPlayer.player.loadVideoById(videoId, 0, "large")
        }
        this.videoId = videoId;
    };

    this.addList = function(list) {
        selfVideoPlayer.playerList = list;
    };

    this.stopPlay = function() {
        if (selfVideoPlayer.player) {
            selfVideoPlayer.player.pauseVideo();
        }
    };

    var onPlayerReady = function() {
        selfVideoPlayer.player.playVideo();
    };

    var onPlayerStateChange = function(event) {
        if (event.data == 0) {
            if (selfVideoPlayer.playerList.length > 0) {
                var videoId = selfVideoPlayer.player.getVideoData()['video_id'];
                var ind = selfVideoPlayer.playerList.indexOf(videoId);
                if (ind >= 0) {
                    ind++;
                    if (ind >= selfVideoPlayer.playerList.length) {
                        ind = 0;
                    }
                } else {
                    ind = 0;
                }
                videoId = selfVideoPlayer.playerList[ind];
                selfVideoPlayer.loadPlayer(videoId);
            }
        }

        $.each(listener['afterPlayerStateChange'], function() {
            this(event);
        });
    };

    var onPlayerError = function(event) {

    };
}
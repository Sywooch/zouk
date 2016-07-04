var playerReady = false;
var player;
var playerList = [];

function onYouTubeIframeAPIReady() {
    playerReady = true;
}

function loadPlayer(videoId) {
    if (typeof player == "undefined") {
        player = new YT.Player('ytplayer', {
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
        player.loadVideoById(videoId, 0, "large")
    }
}

function addList(list) {
    playerList = list;
}

function stopPlay() {
    if (player) {
        player.pauseVideo();
    }
}

function onPlayerReady(event) {
    player.playVideo();
}

function onPlayerStateChange(event) {
    if (event.data == 0) {
        if (playerList.length > 0) {
            var videoId = player.getVideoData()['video_id'];
            var ind = playerList.indexOf(videoId);
            if (ind >= 0) {
                ind++;
                if (ind >= playerList.length) {
                    ind = 0;
                }
            }
            videoId = playerList[ind];
            loadPlayer(videoId);
        }
    }
}

function onPlayerError(event)
{

}
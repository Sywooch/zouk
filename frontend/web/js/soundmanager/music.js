$(document).ready(function () {

    var soundSelected = false;
    var $soundSelected = false;
    var mouseDownD = 0;
    var mouseDownSound = false;
    var mouseDownVolume = false;
    var $selectorMouseMove = false;
    var sounds = {};

    soundManager.setup({
        url: '../../swf/'
    });

    $(document).on('click', '.sound-item .sound-title', function (event) {
        event.preventDefault();
        var musicId = $(this).closest('.sound-item').data('music-id');
        if (soundSelected != false && soundSelected == musicId) {
            soundPause(soundSelected);
            soundSelected = false;
        } else {
            soundStop(soundSelected);
            soundSelected = musicId;
            if ($soundSelected && soundSelected != $soundSelected.data('music-id')) {
                soundAllStop();
            }
            $soundSelected = $(this).closest('.sound-item');
            soundPlay(soundSelected);
        }
        return false;
    });

    function pauseEvent(e){
        if(e.stopPropagation) e.stopPropagation();
        if(e.preventDefault) e.preventDefault();
        e.cancelBubble=true;
        e.returnValue=false;
        return false;
    }

    $(document).on('mousedown', '.audio-back-line,.audio-load-line,.audio-progress-line,.audio-white-line', function (e) {
        e=e || window.event;
        pauseEvent(e);
        var $whiteBar = $(this).closest('.audio-pr').find('.audio-white-line');
        var parentOffset = $(this).parent().offset();
        var relX = e.pageX - parentOffset.left;
        var d = (relX) / ($whiteBar.width());
        if (soundSelected || $soundSelected) {
            mouseDownD = d;
            mouseDownSound = true;
            $selectorMouseMove = $(this);
        }
    });

    $(document).on('mousedown', '.audio-volume-back-line,.audio-volume-load-line,.audio-volume-progress-line,.audio-volume-white-line', function (e) {
        e=e || window.event;
        pauseEvent(e);
        var $whiteBar = $(this).closest('.audio-pr').find('.audio-volume-white-line');
        var parentOffset = $(this).parent().offset();
        var relX = e.pageX - parentOffset.left;
        var d = (relX) / ($whiteBar.width());
        if (soundSelected || $soundSelected) {
            mouseDownD = d;
            mouseDownVolume = true;
            $selectorMouseMove = $(this);
        }
    });

    $(document).mousemove(function(e) {
        var $whiteBar;
        var parentOffset;
        var relX;
        var d;
        var $selector;
        var sound;
        var soundId = soundSelected || $soundSelected ? $soundSelected.data('music-id') : false;
        if (mouseDownSound) {
            $whiteBar = $selectorMouseMove.closest('.audio-pr').find('.audio-white-line');
            parentOffset = $whiteBar.parent().offset();
            relX = e.pageX - parentOffset.left;
            d = relX / ($whiteBar.width());
            if (d > 1) {
                d = 1;
            }
            if (soundId) {
                $selector = sounds[soundId].selector;
                mouseDownD = d;
                sound = sounds[soundId];
                var position = d * sound.duration;
                if (position < 0) {
                    position = 0;
                }
                $selector.find('.audio-progress-line').css('width', (d*100) + '%');
                $selector.find('.time-info .time-play').html(getTimeFormat(position));
            }
        }

        if (mouseDownVolume) {
            $whiteBar = $selectorMouseMove.closest('.audio-pr').find('.audio-volume-white-line');
            parentOffset = $whiteBar.parent().offset();
            relX = e.pageX - parentOffset.left;
            d = relX / ($whiteBar.width());
            if (d > 1) {
                d = 1;
            }
            if (soundId) {
                $selector = sounds[soundId].selector;
                mouseDownD = d;
                sound = sounds[soundId];
                var smo = soundManager.getSoundById(soundId);
                d = Math.floor(mouseDownD * 100);
                sounds[soundId].volume = d;
                smo.setVolume(d);
                if (d < 1) {
                    $selector.find('.glyphicon-volume-down').removeClass('glyphicon-volume-down').addClass('glyphicon-volume-off');
                } else {
                    $selector.find('.glyphicon-volume-off').removeClass('glyphicon-volume-off').addClass('glyphicon-volume-down');
                }
                $selector.find('.audio-volume-progress-line').css('width', d + '%');
            }
        }
    });

    $(document).mouseup(function() {
        var sound;
        var smo;
        var soundId = soundSelected || $soundSelected ? $soundSelected.data('music-id') : false;
        if (mouseDownSound) {
            if (soundId) {
                sound = sounds[soundId];
                smo = soundManager.getSoundById(soundId);
                smo.setPosition(sound.duration * mouseDownD);
                var position = mouseDownD * sound.duration;
                if (position < 0) {
                    position = 0;
                }
                sound.selector.find('.audio-progress-line').css('width', (mouseDownD*100) + '%');
                sound.selector.find('.time-info .time-play').html(getTimeFormat(position));
            }
            mouseDownSound = false;
        }
        if (mouseDownVolume) {
            if (soundId) {
                sound = sounds[soundId];
                smo = soundManager.getSoundById(soundId);
                var d = Math.floor(mouseDownD * 100);
                sounds[soundId].volume = d;
                smo.setVolume(d);
                var $selector = sound.selector;
                if (d < 1) {
                    $selector.find('.glyphicon-volume-down').removeClass('glyphicon-volume-down').addClass('glyphicon-volume-off');
                } else {
                    $selector.find('.glyphicon-volume-off').removeClass('glyphicon-volume-off').addClass('glyphicon-volume-down');
                }
                $selector.find('.audio-volume-progress-line').css('width', d + '%');
            }
            mouseDownVolume = false;
        }
    });

    $(document).on('click', '.sound-item .glyphicon-volume-down', function() {
        if (soundSelected) {
            var sound = sounds[soundSelected];
            var smo = soundManager.getSoundById(sound.id);
            smo.setVolume(0);
            var $selector = sound.selector;
            $selector.find('.audio-volume-progress-line').css('width', '0%');
            $(this).removeClass('glyphicon-volume-down').addClass('glyphicon-volume-off');
        }
    });

    $(document).on('click', '.sound-item .glyphicon-volume-off', function() {
        if (soundSelected) {
            var sound = sounds[soundSelected];
            var smo = soundManager.getSoundById(sound.id);
            smo.setVolume(sound.volume);
            var $selector = sound.selector;
            $selector.find('.audio-volume-progress-line').css('width', sound.volume + '%');
            $(this).removeClass('glyphicon-volume-off').addClass('glyphicon-volume-down');
        }
    });

    function getTimeFormat(timeMSec) {
        var sec = Math.round(parseFloat(timeMSec) / 1000);
        var m = parseInt(sec / 60);
        var s = sec % 60;
        if (s < 10) {
            s = '0' + s;
        }
        return m + ':' + s;
    }

    function getSounds(musicId, update) {
        if (!musicId) {
            return false;
        }
        if (typeof update == "undefined") {
            update = false;
        }
        var selector;
        if (typeof sounds[musicId] == "undefined") {
            selector = $('[data-music-id="' + musicId + '"]');
            sounds[musicId] = {
                id: musicId,
                state: 'stop',
                selector: selector,
                duration: selector.data('duration'),
                volume: 50
            };
            update = false;
        }
        if (typeof sounds[musicId].selector == "undefined" || update) {
            selector = $('[data-music-id="' + musicId + '"]');
            sounds[musicId].selector = selector;
        } else {
            selector = sounds[musicId].selector;
        }
        if ($soundSelected && $soundSelected.data('music-id') == musicId) {
            return $soundSelected;
        }
        return selector;
    }

    function showPlay($selector) {
        if (!$selector) {
            return false;
        }
        $selector.find('.sound-title .background .play').removeClass('hide');
        $selector.find('.sound-title .background .pause').addClass('hide');
    }

    function showPause($selector) {
        if (!$selector) {
            return false;
        }
        $selector.find('.sound-title .background .play').addClass('hide');
        $selector.find('.sound-title .background .pause').removeClass('hide');
    }

    function soundPlay(musicId) {
        var $selector = getSounds(musicId, true);
        if (!$selector) {
            return false;
        }

        soundManager.createSound({
            id: musicId,
            url: $selector.attr('href'),
            volume: sounds[musicId].volume
        });
        $selector.find('.audio-volume-progress-line').css('width', sounds[musicId].volume + '%');

        soundManager.play(
            musicId,
            {
                whileloading: function () {
                    var pr = this.bytesLoaded * 100;
                    $selector.find('.audio-load-line').css('width', pr + '%');
                },
                whileplaying: function () {
                    if (!mouseDownSound) {
                        var pr = this.position / this.duration * 100;
                        $selector.find('.audio-progress-line').css('width', pr + '%');
                        $selector.find('.time-info .time-play').html(getTimeFormat(this.position));
                    }
                },
                onfinish: function() {
                    soundManager.setPosition(this.id, 0);
                    if (!$soundSelected || $soundSelected.data('music-id')) {
                        // что-то не так, ну да ладно. Идем дальше
                    }
                    var $parent;
                    var $nextSound = $soundSelected.next('.sound-item').first();

                    if ($nextSound.length == 0 && $soundSelected.closest('.block-item-summary').length > 0) {
                        // Поиск из списка записей
                        $parent = $soundSelected.closest('.block-item-summary');
                        $parent.nextAll().each(function() {
                            var $nextFind = $(this).find('.sound-item');
                            if ($nextFind.length > 0) {
                                $nextSound = $nextFind.first();
                                return false;
                            }
                        });
                        if ($nextSound.length == 0) {
                            $nextSound = $('#blockList').find('.sound-item').first();
                        }
                    }
                    if ($nextSound.length == 0 && $soundSelected.closest('.audio-list').length > 0) {
                        // Поиск из списка добавления
                        $parent = $soundSelected.closest('tr.audio-list-item');
                        $parent.nextAll().each(function() {
                            var $nextFind = $(this).find('.sound-item');
                            if ($nextFind.length > 0) {
                                $nextSound = $nextFind.first();
                                return false;
                            }
                        });
                        if ($nextSound.length == 0) {
                            $nextSound = $soundSelected.closest('.audio-list').find('.sound-item').first();
                        }
                    }
                    if ($nextSound.length == 0 && $soundSelected.closest('.block-item-view').length > 0) {
                        // Поиск в самой записи
                        $nextSound = $('.block-item-view .sound-item').first();
                    }
                    if ($nextSound.length == 0 && $soundSelected.closest('#blockSounds').length > 0) {
                        // Поиск в записи при редактировании
                        $parent = $soundSelected.closest('tr.audio-list-item');
                        $parent.nextAll().each(function() {
                            var $nextFind = $(this).find('.sound-item');
                            if ($nextFind.length > 0) {
                                $nextSound = $nextFind.first();
                                return false;
                            }
                        });
                        if ($nextSound.length == 0) {
                            $nextSound = $soundSelected.closest('#blockSounds').find('.sound-item').first();
                        }
                    }

                    if ($nextSound.length > 0) {
                        soundStop($soundSelected.data('music-id'));
                        $soundSelected = $nextSound;
                        soundSelected = $soundSelected.data('music-id');
                        soundPlay(soundSelected);
                    }
                }
            }
        );

        sounds[musicId].state = 'play';
        showPause($selector);
        $selector.find('.table-progress').removeClass('hide');
    }

    function soundPause(musicId) {
        var $selector = getSounds(musicId, true);
        if (!$selector) {
            return false;
        }
        showPlay($selector);
        sounds[musicId].state = 'pause';

        soundManager.pause(musicId);
    }

    function soundStop(musicId) {
        var $selector = getSounds(musicId);
        if (!$selector) {
            return false;
        }
        showPlay($selector);
        sounds[musicId].state = 'stop';
        soundManager.stop(sounds[musicId].id);
        $selector.find('.table-progress').addClass('hide');
        $selector.find('.time-info .time-play').html(getTimeFormat(sounds[musicId].duration));
    }

    function soundAllStop() {
        for (var i in sounds) {
            if (sounds[i].state != 'stop') {
                soundStop(sounds[i].id);
            }
        }
    }

});
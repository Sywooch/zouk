$(document).ready(function() {

    var lastKeypress = Date.now();
    var lastSearchValue = "";

    $(document).on('click', '.btn-show-add-music', function() {
        $('.modal-add-music').modal('show');
        return false;
    });

    $(document).on('successUpload', '#musicAddForm', function(event, data) {
        addSound(data['id'], data['url']);
        $('.modal-add-music').modal('hide');
    });

    $(document).on('errorUpload', '#musicAddForm', function(event, data) {
        $('.modal-add-music').modal('hide');
    });

    $(document).on('click', '.btn-add-sound-form', function() {
        $('.block-sound-user-list').addClass('hide');
        $('.block-add-sound').removeClass('hide');
        $('.btn-add-from-list').removeClass('hide');
    });

    $(document).on('click', '.btn-add-from-list', function() {
        $('.block-sound-user-list').removeClass('hide');
        $('.block-add-sound').addClass('hide');
        $('.btn-add-from-list').addClass('hide');
    });

    $('.modal-add-music').on('hidden.bs.modal', function (e) {
        $('.btn-add-from-list').click();
    });

    function addSound(musicId, url) {
        var $blockSounds = $('#blockSounds');
        $.ajax({
            type: "GET",
            url: url,
            dataType: 'html',
            success: function(data) {
                var $blockSound = $('<tr class="audio-list-item"></tr>').append([
                    $('<td></td>').append(data),
                    $('<td></td>').append([
                        $('<input type="hidden" name="sounds[]" class="form-control" value="' + musicId + '" />'),
                        $('<span class="btn btn-link btn-edit-sound-link glyphicon glyphicon-pencil" data-toggle="modal" data-target=".modal-edit-music"></span>'),
                        $('<span class="btn btn-link btn-delete-sound-link glyphicon glyphicon-remove"></span>')
                    ])
                ]);
                $blockSounds.append($blockSound);
            }
        });
    }

    $(document).on('click', '.btn-music-add', function() {
        var $this = $(this);
        addSound($this.data('music-id'), $this.data('url'));
        $('.modal-add-music').modal('hide');
        return false;
    });

    $(document).on('click', '.btn-delete-sound-link', function() {
        var $tr = $(this).closest('tr');
        $tr.hide('slow', function(){ $tr.remove(); });
    });


    function searchAudio(url, value) {
        if (value == lastSearchValue) {
            return false;
        }
        lastSearchValue = value;
        $.ajax({
            type: "POST",
            url: url,
            data: {
                value: value
            },
            dataType: 'json',
            success: function(data) {
                var $tableAudioList = $('table.audio-list');
                $tableAudioList.empty();
                for (var i in data) {
                    if (typeof data[i]['musicHtml']) {
                        $tableAudioList.append([
                            $('<tr class="audio-list-item"></tr>').append([
                                '<td>' + data[i]['musicHtml'] + '</td>',
                                '<td><button type="button" class="btn btn-link btn-music-add no-focus" data-music-id="' + data[i]['musicId'] + '" data-url="' + data[i]['musicUrl'] + '">Добавить</button></td>'
                            ])
                        ]);
                    }
                }
            }
        });
    }

    function searchAudioInputKeypress() {
        var nowKeypress = Date.now();
        if (nowKeypress - lastKeypress > 500) {
            var $this = $('.input-group-search-audio input');

            var $block = $this.closest('.input-group-search-audio');
            var value = $this.val();
            var url = $block.data('url');

            searchAudio(url, value);
        } else {
        }

    }

    $(document).on('keydown', '.input-group-search-audio input', function(e) {
        if (e.keyCode == 13) {
            var $this = $(this);
            var $block = $this.closest('.input-group-search-audio');
            var value = $this.val();
            var url = $block.data('url');
            lastKeypress = Date.now() + 500;
            searchAudio(url, value);
        } else {
            lastKeypress = Date.now();
            setTimeout(searchAudioInputKeypress, 1000);
        }
    });

    $(document).on('click', '.input-group-search-audio button', function() {
        var $this = $(this);
        var $block = $this.closest('.input-group-search-audio');
        var url = $block.data('url');
        var value = $block.find('input').val();
        lastKeypress = Date.now();
        searchAudio(url, value);
    });

    $(document).on('click', '.btn-edit-sound-link', function() {
        var $audioItem = $(this).closest('.audio-list-item').find('.sound-item');
        var title = $audioItem.data('title');
        var artist = $audioItem.data('artist');
        $('[name="modalEditMusicArtist"]').val(artist);
        $('[name="modalEditMusicTitle"]').val(title);
        $('.modal-edit-music .btn-music-save').data('id', $audioItem.data('music-id'));
    });

    $(document).on('click', '.modal-edit-music .btn-music-save', function() {
        var $this = $(this);
        var url = $this.attr('href');
        var $modalEditMusic = $this.closest('.modal-edit-music');
        var artist = $modalEditMusic.find('[name="modalEditMusicArtist"]').val();
        var title = $modalEditMusic.find('[name="modalEditMusicTitle"]').val();

        event.preventDefault();

        $.ajax({
            type: "POST",
            url: url,
            data: {
                id: $this.data('id'),
                artist: artist,
                title: title
            },
            dataType: 'json',
            success: function(data) {
                $('.sound-item[data-music-id=' + data['musicId'] + ']').replaceWith(data['musicHtml']);
                $('.modal-edit-music').modal('hide');
            }
        });

        return false;
    });
});
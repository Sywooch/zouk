$(document).ready(function() {
    var addImgSending = false;

    var $blockImgs = $("#blockImgs");
    $blockImgs.sortable();
    $blockImgs.disableSelection();

    function addImg(url, id) {
        var $blockImgs = $('#blockImgs');
        var maxImg = $(this).data('max-img');
        var urlQ = "'" + url + "'";
        var $blockImg = $('<div class="img-input-group pull-left"></div>').append([
            $('<div class="block-img-delete"><i class="glyphicon glyphicon-remove"></i></div>'),
            $('<input type="hidden" name="imgs[]" class="form-control" value="' + id + '" />'),
            $('<div style="background-image: url(' + urlQ + ')" class="background-img"></div>')
        ]);
        if ($blockImgs.find('div.img-input-group').length >= maxImg || url == '') {
        } else {
            $blockImgs.append($blockImg);
        }
    }

    $(document).on('click', '.block-imgs .block-img-add', function() {
        var $this = $(this);
        addImg($this.data('url'), $this.data('id'));
        $('.modal-add-img').modal('hide');
        return false;
    });

    $(document).on('click', '.btn-show-add-img', function() {
        var $blockImgs = $('#blockImgs');
        var maxImg = $(this).data('max-img');
        if ($blockImgs.find('div.img-input-group').length >= maxImg) {
        } else {
            $('.modal-add-img').modal('show');
            return false;
        }
    });

    $(document).on('errorUpload', '#imgAddForm', function(event, data) {
        $('.modal-add-img').modal('hide');
    });

    $(document).on('click', '.btn-add-img-form', function() {
        $('.block-img-user-list').addClass('hide');
        $('.block-add-img').removeClass('hide');
        $('.btn-add-from-list').removeClass('hide');
    });

    $(document).on('click', '.btn-add-from-list', function() {
        $('.block-img-user-list').removeClass('hide');
        $('.block-add-img').addClass('hide');
        $('.btn-add-from-list').addClass('hide');
    });

    $(document).on('successUpload', '#imgAddForm', function(event, data) {
        addImg(data['short_url'], data['id']);
        $('.modal-add-img').modal('hide');
        return false;
    });

    $(document).on('click', '#blockImgs .img-input-group .block-img-delete', function(event) {
        event.preventDefault();
        $(this).closest('.img-input-group').remove();
        return false;
    });

});
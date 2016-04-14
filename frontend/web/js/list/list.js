$(document).ready(function () {
    var itemIds = {};
    var minItemId = 0;
    var $blockList = $('#blockList');
    $blockList.find('div.block-item-summary').each(function () {
        var $this = $(this);
        var thisId = parseInt($this.data('id'));
        itemIds[thisId] = $this.attr('id');
        if (thisId < minItemId || minItemId == 0) {
            minItemId = thisId;
        }
    });

    $(document).on('click', '#loadMore', function () {
        var searchTag = $(this).data('tag');
        $.ajax({
            url: 'list/items',
            data: {
                lastId: minItemId,
                tag: searchTag
            },
            method: 'post',
            success: function (data) {
                var $block = $(data).find('div.block-item-summary');
                if ($block.length == 0) {
                    $('#loadMore').prop('disabled', true).hide();
                }
                $block.each(function() {
                    var $this = $(this);
                    var thisId = parseInt($this.data('id'));
                    if (typeof itemIds[thisId] == "undefined" || !itemIds[thisId]) {
                        itemIds[thisId] = $this.attr('id');
                        if (thisId < minItemId || minItemId == 0) {
                            minItemId = thisId;
                        }
                        $blockList.append($this);
                    }
                });
            }
        });
    });

    $(document).on('click', '.block-imgs .background-img, .main-image-item', function() {
        var $modalShowImg = $('.modal-show-img');
        $modalShowImg.find('img').attr('src', $(this).data('img-url'));
        $modalShowImg.modal('show');
    });
});
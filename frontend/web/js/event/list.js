$(document).ready(function () {
    var eventIds = {};
    var eventIdByDate = {};
    var minEventDate = 0;
    var pageDateCreateTime = '';
    if (typeof jsZoukVar['dateCreateType'] != "undefined") {
        pageDateCreateTime = jsZoukVar['dateCreateType'];
    }

    var $blockList = $('#blockList');
    $blockList.find('div.block-event-summary').each(function () {
        var $this = $(this);
        var thisId = parseInt($this.data('id'));
        var thisDate = parseInt($this.data('date'));
        eventIds[thisId] = $this.attr('id');
        if (typeof eventIdByDate[thisDate] == "undefined") {
            eventIdByDate[thisDate] = [];
        }
        eventIdByDate[thisDate].push(thisId);
        if (thisDate < minEventDate || minEventDate == 0) {
            minEventDate = thisDate;
        }
    });

    $(document).on('click', '#loadMore', function () {
        var searchTag = $(this).data('tag');
        var display = $(this).data('display');
        $.ajax({
            url: 'events',
            data: {
                lastDate: minEventDate,
                loadEventId: eventIdByDate[minEventDate],
                tag: searchTag,
                dateCreateType: pageDateCreateTime,
                display: display
            },
            method: 'post',
            success: function (data) {
                var $block = $(data).find('div.block-event-summary');
                if ($block.length == 0) {
                    $('#loadMore').prop('disabled', true).hide();
                }
                var countAdded = 0;
                $block.each(function() {
                    var $this = $(this);
                    var thisId = parseInt($this.data('id'));
                    var thisDate = parseInt($this.data('date'));
                    if (typeof eventIds[thisId] == "undefined" || !eventIds[thisId]) {
                        eventIds[thisId] = $this.attr('id');
                        if (typeof eventIdByDate[thisDate] == "undefined") {
                            eventIdByDate[thisDate] = [];
                        }
                        eventIdByDate[thisDate].push(thisId);
                        if (thisDate < minEventDate || minEventDate == 0) {
                            minEventDate = thisDate;
                        }
                        $blockList.append($this);
                        countAdded++;
                    }
                });
                if (countAdded == 0) {
                    $('#loadMore').prop('disabled', true).hide();
                }
            }
        });
    });

    $(document).on('click', '.block-imgs .background-img, .main-image-event', function() {
        var $modalShowImg = $('.modal-show-img');
        $modalShowImg.find('img').attr('src', $(this).data('img-url'));
        $modalShowImg.modal('show');
    });
});
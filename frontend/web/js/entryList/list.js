function initBlockEntry($obj) {
    var $carouselEntryView = $obj.find('.carousel-entry-view');
    if (!$carouselEntryView.data('is-slick')) {
        $carouselEntryView.slick({
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1
        });
        $carouselEntryView.data('is-slick', true);
    }
    $obj.find('.share42').each(function() {
        var $this = $(this);
        $this.removeClass('share42').addClass('share42init');
        initShare42(this);
    });
    $carouselEntryView.find('.block-entry-pic.hide').removeClass('hide');
}

var itemIds = {};
var oldSize = '';
var countColumns = 0;
var lastElement = {};
var page = 1;
var isLoading = false;
var isEnded = false;

function getMinHeight(onlyOffset) {
    if (typeof onlyOffset == "undefined") {
        onlyOffset = false;
    }
    var minHeight = {
        'value': -1,
        'index': -1
    };
    for (var i = 0; i < countColumns; i++) {
        if (typeof lastElement[i] == "undefined") {
            minHeight['index'] = i;
            break;
        }
        var $element = lastElement[i];
        var offset = $element.offset();
        var newValue = offset.top;
        if (!onlyOffset) {
            newValue += $element.height();
        }
        if (minHeight['index'] < 0 || minHeight['value'] > newValue) {
            minHeight = {
                'value': newValue,
                'index': i
            };
        }
    }
    return minHeight;
}

function addBlockEntry($this, size)
{
    var $rowBlock = $this.closest('.row');
    var i;
    var minHeight = getMinHeight();

    var blockColumnName = '#';
    if (size == 'sm') {
        blockColumnName += 'entryCol6_' + minHeight['index'];
    } else if (size == 'md') {
        blockColumnName += 'entryCol4_' + minHeight['index'];
    } else {
        blockColumnName += 'entryCol12_' + minHeight['index'];
    }
    var $blockColumn = $(blockColumnName);
    var $newElement = $rowBlock.clone();
    $newElement.find('.carousel-entry-view').data('is-slick', false);
    $blockColumn.append($newElement);
    initBlockEntry($newElement);
    lastElement[minHeight['index']] = $newElement;
}

function initEntryList() {
    var newSize = findBootstrapEnvironment();
    if (newSize == 'lg') {
        newSize = 'md';
    }
    if (newSize == oldSize) {
        return;
    }
    var $blockFullList = $('#blockFullList');
    $blockFullList.empty();
    var $blockAction = $('#blockAction');

    lastElement = {};
    if (newSize == 'sm') {
        $blockFullList.append([
            '<div class="col-sm-6" id="entryCol6_0"></div>',
            '<div class="col-sm-6" id="entryCol6_1"></div>'
        ]);
        countColumns = 2;
    } else if (newSize == 'md') {
        $blockFullList.append([
            '<div class="col-sm-4" id="entryCol4_0"></div>',
            '<div class="col-sm-4" id="entryCol4_1"></div>',
            '<div class="col-sm-4" id="entryCol4_2"></div>'
        ]);
        countColumns = 3;
    } else {
        $blockFullList.append([
            '<div class="col-sm-12" id="entryCol12_0"></div>',
        ]);
        countColumns = 1;
    }
    $blockFullList.find('div:first').append($blockAction.html());


    if (newSize == 'md' || newSize == 'sm' || newSize == 'xs') {
        oldSize = newSize;
        $('#blockList').find('.block-entry').each(function() {
            addBlockEntry($(this), newSize);
        });
    }
}

function testNeedLoading() {
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    var minHeight = getMinHeight(true);
    var $btn = $('#loadMore');

    var returnValue = minHeight['value'] < (scrollTop + 100);
    var offset = $btn.offset();
    if (typeof offset != "undefined") {
        returnValue = returnValue || offset.top < (scrollTop + document.body.clientHeight + 100);
    }
    
    return returnValue;
}

function loadMore() {
    if (!isLoading && !isEnded) {
        isLoading = true;

        var $btn = $('#loadMore');
        var search = $btn.data('search');
        var $blockList = $('#blockList');
        $btn.prop('disabled', true);

        $.ajax({
            url: $btn.data('url'),
            data: {
                search: search,
                page: page
            },
            method: 'post',
            success: function (data) {
                isLoading = false;
                $btn.prop('disabled', false);
                var $block = $(data).find('div.block-entry');
                if ($block.length == 0) {
                    $('#loadMore').prop('disabled', true).hide();
                    isEnded = true;
                }
                page++;
                $block.each(function() {
                    var $this = $(this);
                    var $block = $this.closest('.row');
                    var thisId = parseInt($this.data('id'));
                    if (typeof itemIds[thisId] == "undefined" || !itemIds[thisId]) {
                        itemIds[thisId] = $this.attr('id');
                        $blockList.append($block);
                        addBlockEntry($this, oldSize);
                    }
                });
                if (testNeedLoading()) {
                    loadMore();
                }
            }
        });
    }
}

$(document).ready(function () {
    var itemIds = {};
    var $blockList = $('#blockList');
    $blockList.find('div.block-entry').each(function () {
        var $this = $(this);
        var thisId = parseInt($this.data('id'));
        itemIds[thisId] = $this.attr('id');
    });

    initEntryList();

    $(document)
        .on('click', '#loadMore', function () {
            loadMore();
        })
        .on('click', '.block-imgs .background-img, .main-image-item', function() {
            var $modalShowImg = $('.modal-show-img');
            $modalShowImg.find('img').attr('src', $(this).data('img-url'));
            $modalShowImg.modal('show');
        })
        .on('click', '.btn-share-entry', function() {
            $(this).closest('.block-entry').find('.share42init').show().removeClass('hide');
            return false;
        })
    ;


    $(window)
        .resize(function() {
            initEntryList();
            if (testNeedLoading()) {
                loadMore();
            }
        })
        .scroll(function() {
            if (testNeedLoading()) {
                loadMore();
            }
        })
    ;

    setTimeout(function() {
        if (testNeedLoading()) {
            loadMore();
        }
    }, 10);
});
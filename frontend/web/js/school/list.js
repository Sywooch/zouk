$(document).ready(function () {
    var schoolIds = {};
    var schoolIdByDate = {};
    var minSchoolDate = 0;
    var pageDateCreateTime = '';
    if (typeof jsZoukVar['dateCreateType'] != "undefined") {
        pageDateCreateTime = jsZoukVar['dateCreateType'];
    }

    var $blockList = $('#blockList');
    $blockList.find('div.block-school-summary').each(function () {
        var $this = $(this);
        var thisId = parseInt($this.data('id'));
        var thisDate = parseInt($this.data('date'));
        schoolIds[thisId] = $this.attr('id');
        if (typeof schoolIdByDate[thisDate] == "undefined") {
            schoolIdByDate[thisDate] = [];
        }
        schoolIdByDate[thisDate].push(thisId);
        if (thisDate < minSchoolDate || minSchoolDate == 0) {
            minSchoolDate = thisDate;
        }
    });

    $(document).on('click', '#loadMore', function () {
        var searchTag = $(this).data('tag');
        console.log(schoolIdByDate);
        $.ajax({
            url: 'schools',
            data: {
                lastDate: minSchoolDate,
                loadSchoolId: schoolIdByDate[minSchoolDate],
                lastIds: $.map(schoolIds, function(element,index) {return index}),
                tag: searchTag,
                dateCreateType: pageDateCreateTime
            },
            method: 'post',
            success: function (data) {
                var $block = $(data).find('div.block-school-summary');
                if ($block.length == 0) {
                    $('#loadMore').prop('disabled', true).hide();
                }
                var countAdded = 0;
                $block.each(function() {
                    var $this = $(this);
                    var thisId = parseInt($this.data('id'));
                    var thisDate = parseInt($this.data('date'));
                    if (typeof schoolIds[thisId] == "undefined" || !schoolIds[thisId]) {
                        schoolIds[thisId] = $this.attr('id');
                        if (typeof schoolIdByDate[thisDate] == "undefined") {
                            schoolIdByDate[thisDate] = [];
                        }
                        schoolIdByDate[thisDate].push(thisId);
                        if (thisDate < minSchoolDate || minSchoolDate == 0) {
                            minSchoolDate = thisDate;
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

    $(document).on('click', '.block-imgs .background-img, .main-image-school', function() {
        var $modalShowImg = $('.modal-show-img');
        $modalShowImg.find('img').attr('src', $(this).data('img-url'));
        $modalShowImg.modal('show');
    });
});
$(document).ready(function() {
    var voteSending = false;
    $(document).on('click', '.vote-up-link, .vote-down-link', function() {
        if (voteSending) {
            return;
        }
        var $this = $(this);
        var $voteBlock = $('.vote-block');
        var url = $this.data('href');
        var data = {
            entity: $this.data('entity'),
            id: $this.data('id'),
            vote: $this.data('vote')
        };
        voteSending = true;
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function($data) {
                voteSending = false;
                if (typeof $data['error'] != "undefined") {
                    alert($data['error']);
                    return;
                }
                var newVote = $data['vote'];
                var newCount = $data['count'];
                $voteBlock.find('.vote-up-link, .vote-down-link').removeClass('voted');
                if (newVote == 2) {
                    $voteBlock.find('.vote-up-link').addClass('voted');
                } else if (newVote == 1) {
                    $voteBlock.find('.vote-down-link').addClass('voted');
                }
                $voteBlock.find('.vote-count-event').html(newCount);
            },
            dataType: 'json'
        });
    });

    $(document).on('click', '#alarm-item', function(event) {
        var $this = $(this);
        var msg = $this.closest('.modal-content').find('input').val();
        event.preventDefault();
        var url = $this.data('href');
        if (msg != '') {
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    id: $this.data('id'),
                    msg: msg
                },
                success: function(data) {
                    window.location.reload();
                },
                dataType: 'json'
            })
        }
        return false;
    });

    $(document).on('click', '#btnShare', function() {
        $('.share42init').show().removeClass('hide');
    });

    $(document).on('click', '.block-imgs .background-img', function() {
        var $modalShowImg = $('.modal-show-img');
        $modalShowImg.find('img').attr('src', $(this).data('img-url'));
        $modalShowImg.modal('show');
    });


    $(document).on('click', '.tab-event', function () {
        var $this = $(this);
        var $block = $this.closest('.block-footer-event');
        if ($this.hasClass('tab-event-comment')) {
            $block.find('.tab-event-list').closest('li').removeClass('active');
            $block.find('.tab-event-comment').closest('li').addClass('active');

            $block.find('.block-event-list').addClass('hide');
            $block.find('.block-event-comment').removeClass('hide');
        } else {
            $block.find('.tab-event-list').closest('li').addClass('active');
            $block.find('.tab-event-comment').closest('li').removeClass('active');

            $block.find('.block-event-list').removeClass('hide');
            $block.find('.block-event-comment').addClass('hide');
        }
        return false;
    });

});

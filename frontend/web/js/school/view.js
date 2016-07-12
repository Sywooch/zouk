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
                $voteBlock.find('.vote-count-school').html(newCount);
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
    
});

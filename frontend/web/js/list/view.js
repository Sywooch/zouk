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
                $voteBlock.find('.vote-count-item').html(newCount);
            },
            dataType: 'json'
        });
    });

    $(document).on('click', '#delete-item', function(event) {
        var $this = $(this);
        $('#myModal').on('shown.bs.modal', function () {
            $('#myInput').focus()
        })
        return false;
        // if (confirm($this.data('msg-confirm'))) {
        //     return true;
        // } else {
        //     event.preventDefault();
        //     return false;
        // }
    });

    $(document).on('click', '#alarm-item', function(event) {
        var $this = $(this);
        var msg = prompt($this.data('msg-alarm'), '');
        var url = $this.data('href');
        event.preventDefault();
        if (msg != '') {
            console.log(url);
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    id: $this.data('id'),
                    msg: msg
                },
                success: function(data) {
                    alert(data['msg']);
                },
                dataType: 'json'
            })
        }
        return false;
    });

});

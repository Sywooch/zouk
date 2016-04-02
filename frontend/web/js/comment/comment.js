$(document).ready(function() {
    var voteCommentSending = false;

    $(document).on('click', '.reply-comment', function() {
        var $form = $('#main-comment-form').clone();
        $('.comment-reply').empty();
        var $this = $(this);
        var $newBlockForm = $('.comment-reply[data-parent-id=' + $this.data('parent-id') + ']');
        $form.removeAttr('id');
        $form.find('.comment-parent-id').val($this.data('parent-id'));
        $form.find('textarea').val('');

        $newBlockForm.empty().append($form);
        $form.find('textarea').focus();

    });

    if (typeof jsZoukVar['anchor'] != "undefined") {
        var anchor = jsZoukVar['anchor'];
        $('html, body').animate({
            scrollTop: $('#' + anchor).offset().top - 55
        }, 100);
    }

    function onSubmitForm($this) {
        var data = $this.serialize();
        var url = $this.attr('action');
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: data,
            success: function(data){
                updateBlockComments(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });

        event.preventDefault();
    }

    $('#blockComments').on('submit', 'form', function() {
        onSubmitForm($(this));
    });

    $(document).on('click', '.btn-show-delete-comment', function() {
        var $button = $(this);
        $('.modal-delete-comment-confirm').modal('show').find('.btn-delete-comment').attr('href', $button.data('url'));
    });

    $(document).on('click', '.btn-delete-comment', function(event) {
        var $this = $(this);
        var url = $this.attr('href');
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            success: function(data){
                updateBlockComments(data);
                $('.modal-delete-comment-confirm').modal('hide');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
                $('.modal-delete-comment-confirm').modal('hide');
            }
        });
        event.preventDefault();
        return false;
    });

    $(document).on('click', '.btn-show-alarm-comment', function() {
        var $button = $(this);
        $('.modal-comment-alarm').modal('show').find('#alarm-comment').data('id', $button.data('id'));
    });

    $(document).on('click', '#alarm-comment', function(event) {
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
                dataType: 'json',
                success: function(data) {
                    updateBlockComments(data);
                    $('.modal-comment-alarm').modal('hide');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                    $('.modal-comment-alarm').modal('hide');
                }
            })
        }
        return false;
    });

    function updateBlockComments(data) {
        if (data['content'] != "") {
            $('#blockComments').empty().html(data['content']);
        }
        if (data['anchor'] != "") {
            $('html, body').animate({
                scrollTop: $('#comment-' + data['anchor']).offset().top - 55
            }, 100);
        }
    }

    $(document).on('click', '.comment-vote-up, .comment-vote-down', function() {
        if (voteCommentSending) {
            return;
        }
        var $this = $(this);
        var $voteBlock = $this.closest('div');
        var url = $this.data('href');
        var data = {
            entity: $this.data('entity'),
            id: $this.data('id'),
            vote: $this.data('vote')
        };
        voteCommentSending = true;
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            success: function($data) {
                voteCommentSending = false;
                if (typeof $data['error'] != "undefined") {
                    alert($data['error']);
                    return;
                }
                var newVote = $data['vote'];
                var newCount = $data['count'];
                $voteBlock.find('.comment-vote-up, .comment-vote-down').removeClass('voted');
                if (newVote == 2) {
                    $voteBlock.find('.comment-vote-up').addClass('voted');
                } else if (newVote == 1) {
                    $voteBlock.find('.comment-vote-down').addClass('voted');
                }
                $voteBlock.find('.comment-vote-count').html(newCount);
            }
        });
    });

    $(document).on('click', '.btn-cancel-comment', function() {
        var $this = $(this);
        var $form = $this.closest('form');
        if ($form.attr('id') == 'main-comment-form') {
            $form.find('textarea').val('');
        }
        $('.comment-reply').empty();
    });

    $(document).on('keypress', 'textarea', function(event) {
        if ((event.keyCode == 10 || event.keyCode == 13) && event.ctrlKey) {
            event.preventDefault();
            onSubmitForm($(this).closest('form'));
        }
    });
});
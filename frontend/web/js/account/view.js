$(document).ready(function () {
    $('.nav-main-tabs').on('click', 'li', function() {
        var $this = $(this);
        $this.closest('ul').find('li').removeClass('active');
        $this.addClass('active');
        var blockTabId = $this.data('tab');
        $('.block-user-tab-info').addClass('hide');
        $('#' + blockTabId).removeClass('hide');

        return false;
    });

    $(document).on('click', '.block-user-img-edit', function() {
        $('.modal-add-avatar').modal('show');
        return false;
    });
});
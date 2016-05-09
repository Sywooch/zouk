$(document).ready(function () {
    $(document).on('click', '.social-unbind', function() {
        var social = $(this).data('social');
        $.ajax({
            url: jsZoukVar['unbindSocialUrl'],
            type: "POST",
            data: {social: social},
            success: function (data) {
            }
        });
        return false;
    });
});

function bindSocial(tok) {
    $.ajax({
        url: jsZoukVar['bindSocialUrl'],
        type: "POST",
        data: {login_ulogin: tok},
        success: function (data) {
        }
    });
}

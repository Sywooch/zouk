$(document).ready(function () {
    $(document).on('click', '.label-tag-element', function() {
        if ($(this).attr('href') == "") {
            window.location = '?tag=' + $(this).text();
            return false;
        }
    });

    $(document).on('click', '.icon-x', function() {
        if ($(this).data('href') != "") {
            window.location = $(this).data('href');
            return false;
        }
    });

});


$(document).ready(function () {
    $(document).on('click', '.label-tag-element', function() {
        if ($(this).attr('href') == "") {
            window.location = '?tag=' + $(this).text();
            return false;
        }
    });

});


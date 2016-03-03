$(document).ready(function () {
    $(document).on('click', '.label-tag-element', function() {
        window.location = '?tag=' + $(this).text();
        return false;
    });

});


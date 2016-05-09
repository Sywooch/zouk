$(document).ready(function () {
    tinymce.init({
        selector: '#item-description',
        height: 300,
        min_height: 150,
        menubar: 'edit insert format table tools',
        theme: 'modern',
        plugins: [
            'advlist autolink lists link charmap preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern'
        ],
        toolbar1: 'preview | insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor',
        image_advtab: false,
        templates: [
            { title: 'Test template 1', content: 'Test 1' },
            { title: 'Test template 2', content: 'Test 2' }
        ]
    });
    
    tags = [];
    if (typeof jsZoukVar['tagsAll'] != "undefined") {
        tags = jsZoukVar['tagsAll'];
    }
    $('#tokenfield').tokenfield({
        autocomplete: {
            limit: 5,
            source: tags,
            delay: 100
        },
        showAutocompleteOnFocus: true
    });

});
$(document).ready(function() {

    var blockLocationCount = 0;
    if (typeof jsZoukVar['blockLocationCount'] != "undefined") {
        blockLocationCount = jsZoukVar['blockLocationCount'];
    }

    function getBlockLocation() {
        var $form = $('#locationAddForm');
        var locationId = blockLocationCount++;
        return $('<div class="block-location" id="blockLocation' + (locationId) + '"></div>').append([
            '<i class="glyphicon glyphicon-map-marker"></i>',
            ' <b>' + $form.find('#location-type option:selected').text() + '</b>: ',
            $form.find('#location-title').val(),
            ' <i class="btn-edit-location-link btn btn-link glyphicon glyphicon-pencil"></i>',
            ' <i class="btn-delete-location-link btn btn-link glyphicon glyphicon-remove"></i>',
            ' <input type="hidden" class="field-lat" name="location[' + locationId + '][lat]" value="' + $('#location-lat').val() + '" />',
            ' <input type="hidden" class="field-lng" name="location[' + locationId + '][lng]" value="' + $('#location-lng').val() + '" />',
            ' <input type="hidden" class="field-zoom" name="location[' + locationId + '][zoom]" value="' + $('#location-zoom').val() + '" />',
            ' <input type="hidden" class="field-title" name="location[' + locationId + '][title]" value="' + $('#location-title').val() + '" />',
            ' <input type="hidden" class="field-type" name="location[' + locationId + '][type]" value="' + $('#location-type').val() + '" />',
            ' <input type="hidden" class="field-description" name="location[' + locationId + '][description]" value="' + $('#location-description').val() + '" />'
        ]);
    }

    $(document).on('click', '#btnAddLocation', function() {
        var $blockLocation = $('#blockLocation');
        var $newLocation = getBlockLocation();
        $blockLocation.append($newLocation);

        return false;
    }).on('click', '#btnEditLocation', function() {
        var $this = $(this);
        var id = $this.data('id');
        var $newLocation = getBlockLocation();
        $('#' + id).replaceWith($newLocation);
        return false;
    }).on('click', '.btn-show-add-location', function() {
        $('.modal-add-location').modal('show');
        markerLocation = {};
        $('#location-title').val('');
        $('#location-type').val('other');
        $('#location-description').val('');
        $('#btnAddLocation').show();
        $('#btnEditLocation').hide();
        return false;
    }).on('click', '.btn-delete-location-link', function() {
        $(this).closest('div.block-location').remove();
    }).on('click', '.btn-edit-location-link', function() {
        $('.modal-add-location').modal('show');
        var $blockLocation = $(this).closest('div.block-location');
        $('#location-lat').val($blockLocation.find('input.field-lat').val());
        $('#location-lng').val($blockLocation.find('input.field-lng').val());
        $('#location-zoom').val($blockLocation.find('input.field-zoom').val());
        $('#location-title').val($blockLocation.find('input.field-title').val());
        $('#location-type').val($blockLocation.find('input.field-type').val());
        $('#location-description').val($blockLocation.find('input.field-description').val());
        $('#btnAddLocation').hide();
        $('#btnEditLocation').show().data('id', $blockLocation.attr('id'));
        markerLocation = {
            'lat': $blockLocation.find('input.field-lat').val(),
            'lng': $blockLocation.find('input.field-lng').val()
        };
    }).on('shown.bs.modal', function() {
        initMapSearch();
    });

    markerChange = function(marker, fromSearch) {
        $('#location-lat').val(marker.getPosition().lat());
        $('#location-lng').val(marker.getPosition().lng());
        if (fromSearch) {
            $('#location-title').val($('#pac-input').val());
        } else {
            geocoder.geocode({'location': marker.getPosition()}, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        $('#location-title').val(results[0]['formatted_address']);
                    }
                }
            });
        }
    };

    zoomChange = function (zoom) {
        $('#location-zoom').val(zoom);
    }
});
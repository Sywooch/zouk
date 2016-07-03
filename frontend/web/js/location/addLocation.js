$(document).ready(function() {
    var schoolAddMap = new googleMap();
    var markerLocation = {};

    var blockLocationCount = 0;
    if (typeof jsZoukVar['blockLocationCount'] != "undefined") {
        blockLocationCount = jsZoukVar['blockLocationCount'];
    }
    var searchBoxText = '';
    if (typeof jsZoukVar['searchBoxText'] != "undefined") {
        searchBoxText = jsZoukVar['searchBoxText'];
    }

    var initingMap = false;

    function getBlockLocation() {
        var $form = $('#locationAddForm');
        var locationId = blockLocationCount++;
        var locationType = $form.find('#location-type option:selected');
        if (locationType.length > 0) {
            locationType = locationType.text()
        } else {
            locationType = $form.find('#location-type-local').val();
        }
        return $('<div class="block-location" id="blockLocation' + (locationId) + '"></div>').append([
            '<i class="glyphicon glyphicon-map-marker"></i>',
            ' <b>' + locationType + '</b>: ',
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

        var maxLocation = $('.btn-show-add-location').data('max-location');
        var countLocation = $('#blockLocation').find('.block-location').length;
        if (typeof maxLocation == "undefined" || countLocation < maxLocation) {
            var $newLocation = getBlockLocation();
            $blockLocation.append($newLocation);
            if (typeof maxLocation != "undefined" && (countLocation >= maxLocation - 1)) {
                $('.btn-show-add-location').addClass('hide');
            }
        }
        return false;
    }).on('click', '#btnEditLocation', function() {
        var $this = $(this);
        var id = $this.data('id');
        var $newLocation = getBlockLocation();
        $('#' + id).replaceWith($newLocation);
        return false;
    }).on('click', '.btn-show-add-location', function() {
        var maxLocation = $(this).data('max-location');
        var countLocation = $('#blockLocation').find('.block-location').length;
        if (typeof maxLocation == "undefined" || countLocation < maxLocation) {
            initingMap = true;
            $('.modal-add-location').modal('show');
            markerLocation = {};
            $('#location-title').val('');
            var $locationType = $('#location-type');
            if ($locationType.find('option').length > 0) {
                $locationType.val('other');
            }
            $('#location-description').val('');
            $('#btnAddLocation').show();
            $('#btnEditLocation').hide();
        }
        return false;
    }).on('click', '.btn-delete-location-link', function() {
        $(this).closest('div.block-location').remove();
        var maxLocation = $('.btn-show-add-location').data('max-location');
        var countLocation = $('#blockLocation').find('.block-location').length;
        if (typeof maxLocation == "undefined" || countLocation < maxLocation) {
            $('.btn-show-add-location').removeClass('hide');
        }
    }).on('click', '.btn-edit-location-link', function() {
        $('.modal-add-location').modal('show');
        initingMap = true;
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
            'lat': parseInt($blockLocation.find('input.field-lat').val()),
            'lng': parseInt($blockLocation.find('input.field-lng').val())
        };
    }).on('shown.bs.modal', function() {
        var lat = typeof markerLocation.lat != "undefined" ? markerLocation.lat : $('#location-lat').val();
        var lng = typeof markerLocation.lng != "undefined" ? markerLocation.lng : $('#location-lng').val();
        var zoom = typeof markerLocation.zoom != "undefinde" ? markerLocation.zoom : 9;

        schoolAddMap.initMap('map', {'lat': lat, 'lng': lng, 'zoom': zoom});
        schoolAddMap.addSearchBox(searchBoxText);
        schoolAddMap.createMarkerOnClick(true, true);
        if (typeof markerLocation.lat != "undefined" && typeof markerLocation.lng != "undefined") {
            schoolAddMap.setMarkers([markerLocation], true, false, false);
        }
        initingMap = false;
    });

    schoolAddMap.addListener('markerChanged', function(marker, fromSearch) {
        if (typeof marker.getPosition() != "undefined") {
            $('#location-lat').val(marker.getPosition().lat());
            $('#location-lng').val(marker.getPosition().lng());
            if (fromSearch) {
                $('#location-title').val($('#pac-input').val());
            } else {
                if (!initingMap) {
                    schoolAddMap.geocoder.geocode({'location': marker.getPosition()}, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                $('#location-title').val(results[0]['formatted_address']);
                            }
                        }
                    });
                }
            }
        }
    });

    schoolAddMap.addListener('zoomChanged', function (zoom) {
        $('#location-zoom').val(zoom);
    });
});
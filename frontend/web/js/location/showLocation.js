$(document).ready(function() {
    var schoolShowMap = new googleMap();
    var markerLocation = {};

    function getLocationInfo(title, type, description, location)
    {
        var locationGotTo = '';
        if (typeof location != "undefined") {
            locationGotTo = $(' <span class="cp location-go-to-coordinate glyphicon glyphicon-map-marker"></span>')
                .data('lat', location.lat)
                .data('lng', location.lng)
                .data('zoom', location.zoom);
        }
        var $info = $('<div></div>').append([
            '<b>' + type + '</b><br/>',
            '<span>' + title + '</span>',
            locationGotTo
        ]);
        if (description.length > 0) {
            $info.append([
                $('<p>').html(description)
            ]);
        }
        $info.append(['<br/>']);
        return $info;
    }

    $(document).on('click', '.show-location-link', function() {
        var $this = $(this);
        var countLocationLink = $('#' + $this.data('id')).find('.show-location-link').length;
        var $btnShowAllLocations = $('.btn-show-all-locations');
        if (countLocationLink <= 1) {
            $btnShowAllLocations.hide();
        } else {
            $btnShowAllLocations.show();
        }
        $('.modal-show-location').modal('show');
        markerLocation = {
            lat: $this.data('lat'),
            lng: $this.data('lng'),
            zoom: $this.data('zoom'),
            type: $this.data('type'),
            title: $this.data('title'),
            titleUrl: $this.data('title-url'),
            siteUrl: $this.data('site-url'),
            description: $this.data('description')
        };
        var $infoBlock = $('.location-info-block');
        $infoBlock.empty().append([getLocationInfo($this.data('title'), $this.data('type'), $this.data('description'), markerLocation)]);
        $btnShowAllLocations.data('id', $this.closest('div').attr('id'));

        return false;
    }).on('click', '.btn-show-all-locations', function() {
        var $this = $(this);
        var $infoBlock = $('.location-info-block');
        var locationsInfo = [];
        var markerLocations = [];
        $('#' + $this.data('id')).find('.show-location-link').each(function() {
            var $thisLocation = $(this);
            var markerLocationValue = {
                lat: $thisLocation.data('lat'),
                lng: $thisLocation.data('lng'),
                zoom: $thisLocation.data('zoom'),
                type: $thisLocation.data('type'),
                title: $thisLocation.data('title'),
                description: $thisLocation.data('description')
            };
            locationsInfo.push(getLocationInfo($thisLocation.data('title'), $thisLocation.data('type'), $thisLocation.data('description'), markerLocationValue));
            markerLocations.push(markerLocationValue);
        });
        $infoBlock.empty().append(locationsInfo);
        schoolShowMap.setMarkers(markerLocations, false, false);
        $('.btn-show-all-locations').hide();
    }).on('click', '.location-go-to-coordinate', function() {
        var $this = $(this);
        var latlng = new google.maps.LatLng($this.data('lat'), $this.data('lng'));
        schoolShowMap.map.setCenter(latlng);
        schoolShowMap.map.setZoom($this.data('zoom'));
    }).on('shown.bs.modal', function() {
        schoolShowMap.initMap('mapShowLocation', {'lat': markerLocation.lat, 'lng': markerLocation.lng, 'zoom': markerLocation.zoom});
        schoolShowMap.setMarkers([markerLocation], false, false);
    });

});
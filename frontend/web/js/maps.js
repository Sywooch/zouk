
function googleMap() {
    var selfGoogleMap = this;

    this.map;

    this.markerLocation = {};
    this.markers = [];
    this.geocoder;
    this.infowindow;
    this.maxZoom = 17;
    this.markerCluster;

    var mcOptions = {gridSize: 20, maxZoom: 15, imagePath: '../../img/location/m'};

    var listener = {
        'markerChanged': [],
        'zoomChanged': []
    };

    this.addListener = function(event, callback) {
        listener[event].push(callback);
    };

    this.initMap = function(elementId, center) {
        var zoom = (typeof center.zoom == "undefined") ? 9 : center.zoom;
        var latlng = new google.maps.LatLng(center.lat, center.lng);
        var mapOptions = {
            zoom: parseInt(zoom),
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        selfGoogleMap.infowindow = new google.maps.InfoWindow({
            content: ''
        });

        selfGoogleMap.map = new google.maps.Map(document.getElementById(elementId), mapOptions);
        selfGoogleMap.geocoder = new google.maps.Geocoder;


        google.maps.event.addListener(selfGoogleMap.map, 'zoom_changed', function() {
            if (selfGoogleMap.map.getZoom() > 17) selfGoogleMap.map.setZoom(17);
            $.each(listener['zoomChanged'], function() {
                this(selfGoogleMap.map.getZoom());
            });
        });
    };

    this.addSearchBox = function(searchBoxText) {
        var input = $('<input id="pac-input" class="controls" type="text" placeholder="' + searchBoxText + '">').get(0);
        var searchBox = new google.maps.places.SearchBox(input);
        selfGoogleMap.map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        selfGoogleMap.map.addListener('bounds_changed', function() {
            searchBox.setBounds(selfGoogleMap.map.getBounds());
        });

        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                selfGoogleMap.setMarker(place.geometry.location, true);

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            searchBox.map.fitBounds(bounds);
        });
    };

    this.createMarkerOnClick = function(clearAnyMarkers, draggable) {
        google.maps.event.addListener(selfGoogleMap.map, "click", function(event) {
            if (clearAnyMarkers) {
                selfGoogleMap.clearMarker();
                selfGoogleMap.setMarkers([event.latLng], draggable);
            } else {
                selfGoogleMap.setMarkers([event.latLng], draggable);
            }
        });
    };

    this.clearMarker = function() {
        for (var i = 0; i < selfGoogleMap.markers.length; i++) {
            selfGoogleMap.markers[i].setMap(null);
        }
        selfGoogleMap.markers = [];
        if (typeof selfGoogleMap.markerCluster != "undefined") {
            selfGoogleMap.markerCluster.clearMarkers();
        }
    };

    this.setMarker = function(location, fromSearch, draggable, animation, locationMap) {
        var marker;
        if (typeof locationMap == "undefined") {
            locationMap = selfGoogleMap.map;
        }
        if (typeof draggable == "undefined") {
            draggable = true;
        }
        if (typeof animation == "undefined") {
            animation = google.maps.Animation.DROP;
        }

        marker = new google.maps.Marker({
            position: location,
            draggable: draggable,
            map: locationMap,
            animation: animation
        });
        marker.addListener('click', function() {
            selfGoogleMap.infowindow.setContent(
                '<b>' + location['type'] + '</b><br/>' +
                '<span>' + $('<p></p>').html(location['title']).text() + '</span><br/>' +
                '<p>' + $('<p></p>').html(location['description']).text() + '</p>');
            selfGoogleMap.infowindow.open(selfGoogleMap.map, this);
        });
        if (draggable) {
            google.maps.event.addListener(marker, 'dragend', function() {
                $.each(listener['markerChanged'], function() {
                    this(marker, false);
                });
            });
        }
        $.each(listener['markerChanged'], function() {
            this(marker, fromSearch);
        });
        return marker;
    };

    this.setMarkers = function(locations, draggable, animation) {
        if (typeof draggable == "undefined") {
            draggable = true;
        }
        if (typeof animation == "undefined") {
            animation = google.maps.Animation.DROP;
        }
        selfGoogleMap.clearMarker();
        $.each(locations, function() {
            var location = this;
            var marker = selfGoogleMap.setMarker(location, false, draggable, animation, null);
            selfGoogleMap.markers.push(marker);
        });
        selfGoogleMap.markerCluster = new MarkerClusterer(selfGoogleMap.map, selfGoogleMap.markers, mcOptions);
    }
}

function googleMap() {
    var selfGoogleMap = this;

    this.map;

    this.markerLocation = {};
    this.markers = [];
    this.geocoder;
    this.infowindow;
    this.maxZoom = 17;
    this.markerCluster;
    this.imagePath = '../../img/location/m';
    if (typeof jsZoukVar['imagePath'] != "undefined") {
        this.imagePath = jsZoukVar['imagePath'];
    }

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
            places = [places[0]];
            places.forEach(function(place) {
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                selfGoogleMap.setMarkers([place.geometry.location], true);

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            selfGoogleMap.map.fitBounds(bounds);
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
            var title = '<b>' + location['type'] + '</b>';
            if (typeof location['titleUrl'] != "undefined") {
                title = $('<a target="_blank"></a>').attr('href', location['titleUrl']).append(title);
                title = $('<div>').append(title.clone()).remove().html();
            }
            var site = '';
            if (typeof location['siteUrl'] != "undefined") {
                site = $('<a target="_blank"></a>').attr('href', location['siteUrl']).append(location['siteUrl']);
                site = $('<div>').append(site.clone()).remove().html();
            }
            selfGoogleMap.infowindow.setContent(
                title + '<br/>' +
                '<span>' + $('<p></p>').html(location['title']).text() + '</span><br/>' +
                '<p>' + $('<p></p>').html(location['description']).text() + '</p>' +
                site
            );
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
        var mcOptions = {gridSize: 20, maxZoom: 15, imagePath: this.imagePath};

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
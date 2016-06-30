
var markerLocation = {};
var marker;
var markers = [];
var map;
var geocoder;
var infowindow;

var markerChange;
var zoomChange;

var searchBoxText = '';
if (typeof jsZoukVar['searchBoxText'] != "undefined") {
    searchBoxText = jsZoukVar['searchBoxText'];
}

function getCoordinates() {
    if (typeof marker != "undefined" && marker) {
        return {
            'lat': marker.getPosition().lat(),
            'lng': marker.getPosition().lng()
        };
    }
}

function setMarker(location, fromSearch, draggable, animation, locationMap) {
    if (typeof locationMap == "undefined") {
        locationMap = map;
    }
    if (typeof draggable == "undefined") {
        draggable = true;
    }
    if (typeof animation == "undefined") {
        animation = google.maps.Animation.DROP;
    }
    if (typeof marker == "undefined" || !marker) {
        marker = new google.maps.Marker({
            position: location,
            draggable: draggable,
            map: locationMap,
            animation: animation
        });
        if (draggable) {
            google.maps.event.addListener(marker, 'dragend', function() {
                if (typeof markerChange != "undefined") {
                    markerChange(marker, false);
                }
            });
        }
    } else {
        marker.setPosition(location);
    }
    if (typeof markerChange != "undefined") {
        markerChange(marker, fromSearch);
    }
}

function setMarkers(locations, draggable, animation) {
    if (typeof draggable == "undefined") {
        draggable = true;
    }
    if (typeof animation == "undefined") {
        animation = google.maps.Animation.DROP;
    }
    clearMarker();
    $.each(locations, function() {
        var location = this;
        marker = false;
        setMarker(location, false, draggable, animation, null);
        marker.addListener('click', function() {
            infowindow.setContent(
                '<b>' + location['type'] + '</b><br/>' +
                '<span>' + $('<p></p>').html(location['title']).text() + '</span><br/>' +
                '<p>' + $('<p></p>').html(location['description']).text() + '</p>');
            infowindow.open(map, this);
        });
        markers.push(marker);
    });
    var mcOptions = {gridSize: 50, maxZoom: 15, imagePath: '../../img/location/m'};
    var markerCluster = new MarkerClusterer(map, markers, mcOptions);
}

function clearMarker()
{
    if (typeof marker != "undefined") {
        marker.setMap(null);
    }
    for (var i in markers) {
        markers[i].setMap(null);
    }
    markers = [];
}

function initMapSearch() {
    var city= new google.maps.LatLng($('#location-lat').val(), $('#location-lng').val());
    var mapOptions = {
        zoom: parseInt($('#location-zoom').val()),
        center: city,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById('map'), mapOptions);
    geocoder = new google.maps.Geocoder;

    // Create the search box and link it to the UI element.
    var input = $('<input id="pac-input" class="controls" type="text" placeholder="' + searchBoxText + '">').get(0);
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
    });

    google.maps.event.addListener(map, "click", function(event) {
        position_x = event.latLng.lat();
        position_y = event.latLng.lng();
        setMarker(event.latLng, false);
    });

    google.maps.event.addListener(map, 'zoom_changed', function() {
        if (map.getZoom() > 17) map.setZoom(17);
        if (typeof zoomChange != "undefined") {
            zoomChange(map.getZoom());
        }
    });

    // [START region_getplaces]
    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
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
            setMarker(place.geometry.location, true);

            if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds);
    });
    // [END region_getplaces]

    marker = false;
    if (markerLocation.lat && markerLocation.lng) {
        var latlng = new google.maps.LatLng(markerLocation.lat, markerLocation.lng);
        setMarker(latlng);
    }

}

function initMapShow() {
    var latlng = new google.maps.LatLng(markerLocation.lat, markerLocation.lng);
    var mapOptions = {
        zoom: parseInt(markerLocation.zoom),
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    infowindow = new google.maps.InfoWindow({
        content: ''
    });

    map = new google.maps.Map(document.getElementById('mapShowLocation'), mapOptions);
    geocoder = new google.maps.Geocoder;

    google.maps.event.addListenerOnce(map, 'idle', function(){
        marker = false;
        setMarker(latlng, false, false, false);
    });

    google.maps.event.addListener(map, 'zoom_changed', function() {
        if (map.getZoom() > 17) map.setZoom(17);
        if (typeof zoomChange != "undefined") {
            zoomChange(map.getZoom());
        }
    });

}




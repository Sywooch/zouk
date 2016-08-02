$(document).ready(function () {
    var eventMap = new googleMap();
    eventMap.initMap('eventMap', {'lat': 55.7522200, 'lng': 37.6155600, 'zoom': 5});

    var markerLocations = [];

    if (typeof jsZoukVar['locations'] != "undefined") {
        var locations = jsZoukVar['locations'];
        $.each(locations, function() {
            var markerLocationValue = {
                lat: this['lat'],
                lng: this['lng'],
                type: this['event-title'],
                title: this['type'] + ': ' + this['title'],
                titleUrl: this['title-url'],
                siteUrl: this['site-url'],
                description: this['description']
            };
            markerLocations.push(markerLocationValue);
        });
    }

    eventMap.setMarkers(markerLocations, false, false);

});
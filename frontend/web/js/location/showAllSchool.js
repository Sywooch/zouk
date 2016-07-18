$(document).ready(function() {
    var schoolMap = new googleMap();
    schoolMap.initMap('schoolMap', {'lat': 55.7522200, 'lng': 37.6155600, 'zoom': 5});

    var markerLocations = [];
    $('.block-school-summary').each(function() {
        var $this = $(this);
        var $location = $this.find('.show-location-link');
        if ($location.length > 0) {
            var markerLocationValue = {
                lat: $location.data('lat'),
                lng: $location.data('lng'),
                type: $this.find('.summary .school-hyperlink').text(),
                title: $location.data('title'),
                titleUrl: $location.data('title-url'),
                siteUrl: $location.data('site-url'),
                description: $location.data('description')
            };
            markerLocations.push(markerLocationValue);
        }
    });
    schoolMap.setMarkers(markerLocations, false, false);
});
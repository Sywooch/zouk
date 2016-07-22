$(document).ready(function() {
    var schoolMap = new googleMap();
    schoolMap.initMap('schoolMap', {'lat': 55.7522200, 'lng': 37.6155600, 'zoom': 5});

    var markerLocations = [];

    $.ajax({
        url: 'data',
        method: 'get',
        dataType: "json",
        success: function (data) {
            $.each(data, function() {
                var markerLocationValue = {
                    lat: this['lat'],
                    lng: this['lng'],
                    type: this['school-title'],
                    title: this['title'],
                    titleUrl: this['title-url'],
                    siteUrl: this['site-url'],
                    description: this['description']
                };
                markerLocations.push(markerLocationValue);
            });
            schoolMap.setMarkers(markerLocations, false, false);
        }
    });

});
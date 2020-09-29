/**
 * googlemapを表示するだけのもの 
 *
 */
function zf_show_map(mapId, mylat, mylng) {
    //for v3
    var latlng = new google.maps.LatLng(mylat, mylng);
    var myOptions = {
        zoom: 16,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: false
    };
    var map = new google.maps.Map(document.getElementById(mapId), myOptions);

    var markerOpt = {
        position: latlng,
        map: map
    };
    var myMarker = new google.maps.Marker(markerOpt);
}


function initialize_store(markers, zoomLevel, markerimage)
{
    
    var bounds = new google.maps.LatLngBounds();
    var mapOptions = {
        zoom : zoomLevel
    };
    var map = new google.maps.Map(document.getElementById('store-list-map-canvas'), mapOptions);
    
    var icon = {
        url: markerimage, // url
        scaledSize: new google.maps.Size(35, 35), // scaled size
        //origin: new google.maps.Point(0,0), // origin
        //anchor: new google.maps.Point(0,0) // anchor
    };
    
    var infoWindow = new google.maps.InfoWindow(), marker, i;
    var split;
    var content;
    for (i = 0; i < markers.length; i++) {
        var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
        bounds.extend(position);
        if (markerimage != '') {
                marker = new google.maps.Marker({
                position : position,
                map : map,
                title : markers[i][0],
                icon : icon
            });
        } else {
            marker = new google.maps.Marker({
                position : position,
                map : map,
                title : markers[i][0],
            });
        }
        

        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                
                split = markers[i][0].split(",");
                content = '<div class="info_content">';
                for (j = 0; j < split.length; j++) {
                    if (split[j].indexOf("http") >= 0) {
                        content += '<div style="float:left;padding:5px;"><img height="150px;" width="150px;" src="'+split[j]+'"></div>';
                    } else {
                        if (j == 0) {
                            content += '<div style="float:right;padding:5px;">';
                        }
                        content += '<b>' + split[j] + '</b><br>';

                        if (j == (split.length-2)) {
                            content += '</div>';
                        }
                    }
                }
                content += '</div>';
                infoWindow.setContent(content);
                infoWindow.open(map, marker);
            }
        })(marker, i));
        map.fitBounds(bounds);
    }

}

function initialize(coordinate, text, zoomLevel, markerimage)
{
    var mapOptions = {
        zoom : zoomLevel,
        center : new google.maps.LatLng(coordinate[0], coordinate[1])
    };

    var map = new google.maps.Map(document.getElementById('store-list-map-canvas'), mapOptions);
    
    var icon = {
        url: markerimage, // url
        scaledSize: new google.maps.Size(35, 35), // scaled size
        //origin: new google.maps.Point(0,0), // origin
        //anchor: new google.maps.Point(0,0) // anchor
    };
    
    if (markerimage != '') {
            var marker = new google.maps.Marker({
                position : new google.maps.LatLng(coordinate[0], coordinate[1]),
                map : map,
                icon : icon
            });
        } else {
            var marker = new google.maps.Marker({
                position : new google.maps.LatLng(coordinate[0], coordinate[1]),
                map : map,
            });
        }
    

    google.maps.event.addListener(map, 'center_changed', function () {
        window.setTimeout(function () {
            map.panTo(marker.getPosition());
        }, 3000);
    });
    var splitText;
    var content;

    splitText = text.split(",");
    content = '<div class="info_content">';
    for (j = 0; j < splitText.length; j++) {
        content += '<b>' + splitText[j] + '</b><br>';
    }
    content += '</div>';

    var infowindow = new google.maps.InfoWindow(), marker, i;

    google.maps.event.addListener(marker, 'click', (function (marker) {
        return function () {
            splitText = text.split(",");
            content = '<div class="info_content">';
            /*for ( j = 0; j < splitText.length; j++) {
				content += '<b>' + splitText[j].split("/").join("  ") + '</b><br>';
			}*/
            for (j = 0; j < splitText.length; j++) {
                    if (splitText[j].indexOf("http") >= 0) {
                        content += '<div style="float:left;padding:5px;"><img height="150px;" width="150px;" src="'+splitText[j]+'"></div>';
                    } else {
                        if (j == 0) {
                            content += '<div style="float:right;padding:5px;">';
                        }
                        content += '<b>' + splitText[j].split("/").join("  ") + '</b><br>';
                        if (j == (splitText.length-2)) {
                            content += '</div>';
                        }
                    }
                }
            
            content += '</div>';
            infowindow.setContent(content);
            infowindow.open(map, marker);
        }
    })(marker));
}
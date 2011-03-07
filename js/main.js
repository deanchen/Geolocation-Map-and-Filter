/**
 * @author Dean Chen
 */
var hideLoader = function(){
  dojo.fadeOut({
    node:"preloader",
    duration: 700,
    onEnd: function(){
            dojo.style("preloader", "display", "none");
            // $("#preloader").css("display","none"); 
    }
  }).play();
}

function lookUpMarker(kind) {
  switch(kind) {
    case "four_year":
      return "http://maps.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
    case "community":
      return "http://maps.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png";
    case "high_school":
      return "http://maps.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png";
  }
}

dojo.require( "dijit.layout.BorderContainer" );
dojo.require( "dijit.layout.ContentPane" );
dojo.addOnLoad( function() {
    var myLatlng = new google.maps.LatLng(38.6,-98);
    var myOptions = {
        zoom: 5,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var markers = [];
    
    createMarkers(map, markers);
    dojo.addOnLoad(function() {
      // add an extra second of timeout for map to load completely
      setTimeout("hideLoader()", 1000);
    });
});


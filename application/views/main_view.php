<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>School Filter</title>

<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dojo/resources/dojo.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dijit/themes/claro/claro.css" type="text/css" media="all" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script djConfig="parseOnLoad:true" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dojo/dojo.xd.js"></script>

<style>
html, body {
  height: 100%;
  width: 100%;
}

#preloader {
  width:100%; height:100%; margin:0; padding:0;
  background:#fff 
    url('http://o.aolcdn.com/dojo/1.5/dojox/image/resources/images/loading.gif')
    no-repeat center center;
  position:absolute;
  z-index:999;
}
</style>

<script type="text/javascript">
var hideLoader = function(){
  dojo.fadeOut({
    node:"preloader",
    onEnd: function(){
      dojo.style("preloader", "display", "none");
    }
  }).play();
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
      hideLoader();
    });
});

function createMarkers(map, markers) {
  var input_markers = <?php print $markers; ?>;

  for (var i = 0; i < input_markers.length; i++) {
    var input_marker = input_markers[i];
    markers.push(
      new google.maps.Marker({
        position: new google.maps.LatLng(input_marker.lat, input_marker.lng),
        map: map,
        title: input_marker.school
      })
    );
  }
}


</script>
</head>
<div id="preloader"></div>
<body class="claro" style="height:100%;padding:0;margin:0; overflow:hidden">


<div dojoType="dijit.layout.BorderContainer" style="height:100%">
    <div dojoType="dijit.layout.ContentPane" region="left" splitter="true" style="width:200px">
        Left search thing
    </div>
    <div dojoType="dijit.layout.ContentPane" region="top" style="height:100px">
        Top
    </div>
    <div dojoType="dijit.layout.ContentPane" region="center" style="overflow:hidden">

        <div id="map_canvas" style="height:100%; width:100%"></div>

    </div>
</div>

</body>
</html>
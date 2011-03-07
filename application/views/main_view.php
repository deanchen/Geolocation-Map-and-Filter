<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>School Filter</title>

<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dojo/resources/dojo.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dijit/themes/claro/claro.css" type="text/css" media="all" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script djConfig="parseOnLoad:true" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dojo/dojo.xd.js"></script>
<script type="text/javascript" src="/js/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" src="/js/main.js"></script>

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

#filteredResults td.header {
  font-weight: bold;
  padding-top: 8px;
}

#filteredResults td {
  padding-bottom: 3px;
}
</style>

<script type="text/javascript">
  // need to declare this function here instead of main.js because of php input
  function createMarkers(map, markers) {
  
    var input_markers = <?php print $markers; ?>;

    for (var i = 0; i < input_markers.length; i++) {
      var input_marker = input_markers[i];
      markers[input_marker['id']] =
        new google.maps.Marker({
          recordId: input_marker.id,
          kind: input_marker.kind,
          position: new google.maps.LatLng(input_marker.lat, input_marker.lng),
          map: map,
          icon: lookUpMarker(input_marker.kind, false),
          title: input_marker.school
        });
    }
    
    // create center marker
    centerMarker = new google.maps.Marker({
      position: startLocation,
      map: map,
      draggable: true,
      icon:  "http://maps.google.com/mapfiles/arrow.png",
      shadow: "http://maps.google.com/mapfiles/arrowshadow.png",
      title: "Search Center",
    });
    
    // keep the marker bouncing after being dragged
    google.maps.event.addListener(centerMarker, "dragend", function() {
      centerMarker.setAnimation(google.maps.Animation.BOUNCE);
    });
  }
</script>

</head>
<div id="preloader"></div>
<body class="claro" style="height:100%;padding:0;margin:0; overflow:hidden">


<div dojoType="dijit.layout.BorderContainer" style="height:100%">
    <div dojoType="dijit.layout.ContentPane" region="left" splitter="true" style="width:210px">
        <input id="high_school" name="high_school" dojoType="dijit.form.CheckBox" 
           checked="true">
        <label for="high_school">
            H.S.&nbsp;
        </label>
        <input id="community" name="community" dojoType="dijit.form.CheckBox" 
           checked="true">
        <label for="community">
            Comm.&nbsp;
        </label>
        
        <input id="four_year" name="four_year" dojoType="dijit.form.CheckBox" 
           checked="true">
        <label for="four_year">
            4 Yr.&nbsp;
        </label>
        <br />
        <label style='float: left'>0</label> <label style='float: right'>50</label>
        <div id="slider"></div>
        <br />
        <label style='float: left'>0</label> <label style='float: right'>1000</label>
        <div id="slider2"></div>
        <br />
        <label>Distance: </label><div id="distanceValue"></div>
        <button id="filterButton" type="button"></button>
        <button id="clearButton" type="button"></button>
        <br />
        <br />
        <button id="copyButton" type="button"></button>
        <div id="schoolsList"></div>
    </div>
    <!-- don't need hearder for now 
    <div dojoType="dijit.layout.ContentPane" region="top" style="height:100px">
        Top
    </div>
    -->
    <div dojoType="dijit.layout.ContentPane" region="center" style="overflow:hidden">

        <div id="map_canvas" style="height:100%; width:100%"></div>

    </div>
</div>

</body>
</html>
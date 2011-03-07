<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>School Filter</title>

<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dojo/resources/dojo.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dijit/themes/claro/claro.css" type="text/css" media="all" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script djConfig="parseOnLoad:true" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dojo/dojo.xd.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/js/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/js/main.js"></script>

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
    /**
   * onLoad setup
   */
  dojo.addOnLoad( function() {
      var myOptions = {
          zoom: 5,
          center: startLocation,
          mapTypeId: google.maps.MapTypeId.ROADMAP
      }
      var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
      
      createMarkers(map, <?php print $markers; ?>);
      createUi();

      setupClipboardCopy('<?php print base_url(); ?>');
      dojo.addOnLoad(function() {
        // add an extra second of timeout for map to load completely
        setTimeout("initializeApp()", 1500);
      });
  });
  
  
</script>

</head>
<div id="preloader"></div>
<body class="claro" style="height:100%;padding:0;margin:0; overflow:hidden">


<div dojoType="dijit.layout.BorderContainer" style="height:100%">
    <div dojoType="dijit.layout.ContentPane" region="left" splitter="true" style="width:210px">
        <input id="high_school" name="high_school" dojoType="dijit.form.CheckBox" 
           checked="true">
        <label for="high_school" style="color: #5b5cec; font-weight: bold;">
            H.S.&nbsp;
        </label>
        <input id="community" name="community" dojoType="dijit.form.CheckBox" 
           checked="true">
        <label for="community" style="color: #02e54d; font-weight: bold;">
            Comm.&nbsp;
        </label>
        
        <input id="four_year" name="four_year" dojoType="dijit.form.CheckBox" 
           checked="true">
        <label for="four_year" style="color: #ff3e4f; font-weight: bold;">
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

    <div dojoType="dijit.layout.ContentPane" region="bottom" style="height:16px; padding: 2px">
        <div id="license" style="float: left">Released Under the The <a href="/license-2.txt">MIT License</a>.</div>
        <div id="credit" style="float: right">Copyright &copy; 2011 Dean Chen</div>
    </div>

    <div dojoType="dijit.layout.ContentPane" region="center" style="overflow:hidden">

        <div id="map_canvas" style="height:100%; width:100%"></div>

    </div>
</div>

</body>
</html>
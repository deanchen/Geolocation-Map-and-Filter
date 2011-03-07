/**
 * @author Dean Chen
 */
var markers = {};
var centerMarker;
var startLocation = new google.maps.LatLng(38.6, -98);

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

function lookUpMarker(kind, selected) {
  switch(kind) {
    case "four_year":
      if (selected) return "http://maps.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
      else return "http://labs.google.com/ridefinder/images/mm_20_red.png";
    case "community":
      if (selected) return "http://maps.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png";
      else return "http://labs.google.com/ridefinder/images/mm_20_green.png";
    case "high_school":
      if (selected) return "http://maps.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png";
      else return "http://labs.google.com/ridefinder/images/mm_20_blue.png";
  }
}

function filterMarkers(lat, lng, distance, kind) {
  if (kind === undefined) {
    kind = "";
  } else if (kind instanceof Array) {
    kind = kind.join("|");
  } 
  dojo.xhrGet({
    url: "/main/search/" + lat + "/" + lng + "/" + distance + "/" + kind,
    handleAs: "json",
    load: function(data){
      var records = {};
      
      for(var i=0; i<data.length; i++){
         var id = data[i].id;
         records[id] = data[i];
      }
      
      // set all markers to unselected
      clearMarkers();
      
      var resultTable = "<table id='results'>";

      for (var key in records) {
        record = records[key];
        
        resultTable += '<tr><td>' + record.school + '</td></tr>';
        
        var selected = true;
        var id = record.id;
        var marker = markers[id];
        
        marker.setIcon(lookUpMarker(marker.kind, selected));
        
        // hack: need to call twice or dropped icons won't drop again
        marker.setAnimation(google.maps.Animation.DROP);
        marker.setAnimation(google.maps.Animation.DROP);
      }
      
      resultTable += "</table>";
      dojo.byId('schoolsList').innerHTML = resultTable;
    }
  });
}

function clearMarkers() {
  for (var key in markers) {
        marker = markers[key];
        var selected = false;
        marker.setIcon(lookUpMarker(marker.kind, selected));
        marker.setAnimation(null);
      }
}

var slider1;
function createUi() {
  
  var rulesNode = document.createElement('div');
  dojo.byId('slider').appendChild(rulesNode);
  
  var slider1Rule = new dijit.form.HorizontalRule({
    count: 6,
    style: "height:5px"
  }, 
  rulesNode);

  slider1 = new dijit.form.HorizontalSlider({
    name: "slider",
    value: 10,
    discreteValues: 6,
    minimum: 0,
    maximum: 50,
    intermediateChanges: true,
    style: "width: 200px;",
    onChange: function(value) {
      dojo.byId("distanceValue").value = value;
    },
    showButtons: false,
  },
  "slider");
  
  rulesNode = document.createElement('div');
  dojo.byId('slider2').appendChild(rulesNode);
  
  var slider2Rule = new dijit.form.HorizontalRule({
    count: 11,
    style: "height:5px"
  }, rulesNode);
  
  
  var slider2 = new dijit.form.HorizontalSlider({
    name: "slider2",
    value: 0,
    minimum: 0,
    maximum: 1000,
    discreteValues: 11,
    intermediateChanges: true,
    style: "width:200px;",
    onChange: function(value) {
      dojo.byId("distanceValue").value = value;
    },
    showButtons: false
  }, "slider2");
  
  var textBox = new dijit.form.NumberTextBox({
    name: "distance",
    constraints: {
      min: 0,
      max: 2000,
      places: 0
    },
    value: "10" /* no or empty value! */,
  }, 
  "distanceValue");
  
  dojo.style(textBox.domNode, "width", "40px");
  
  dojo.connect(textBox, "onChange", function() {
    var value = textBox.attr("value");
    if (value <= 50) {
      slider1.attr('value', value);
    } else {
      slider2.attr('value', value);
    }
  });
  
  var filterButton = new dijit.form.Button({
    label: "Filter",
    onClick: function() {
      // see main views for the checkbox declaration
      // matches enums in mysql db kind column
      var selections = ['community', 'high_school', 'four_year'];
      var checkedKinds = [];
      
      for (var i=0; i<selections.length; i++) {
        var kind = selections[i];
        if (dojo.byId(kind).checked) {
          checkedKinds.push(kind);
        }
      }
      
      var centerPosition = centerMarker.getPosition();
      filterMarkers(centerPosition.lat(), centerPosition.lng(), textBox.attr("value"), checkedKinds);
    }
  },
  "filterButton");
  
  var clearButton = new dijit.form.Button({
    label: "Clear",
    onClick: function() {
      clearMarkers();
      dojo.byId('schoolsList').innerHTML = '';
    }
  },
  "clearButton");
}

function initializeApp() {
  hideLoader();
  centerMarker.setAnimation(google.maps.Animation.BOUNCE);
}

dojo.require( "dijit.layout.BorderContainer" );
dojo.require( "dijit.layout.ContentPane" );
dojo.require("dijit.form.Slider");
dojo.require("dijit.form.HorizontalRule");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.form.NumberTextBox");
dojo.require("dijit.form.CheckBox");


dojo.addOnLoad( function() {
    var myOptions = {
        zoom: 5,
        center: startLocation,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    
    createMarkers(map, markers);
    createUi();
    dojo.addOnLoad(function() {
      // add an extra second of timeout for map to load completely
      setTimeout("initializeApp()", 1500);
    });
});


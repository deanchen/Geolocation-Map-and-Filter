/**
 * @author Dean Chen
 */
 
 /**
 * Copyright (c) 2011 Dean Chen <dean.chen@duke.edu>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
var markers = {};
var centerMarker;
var startLocation = new google.maps.LatLng(38.6, -98);
var clipboardText = '';
var openWindow = null;
/**
 * Include required dojo components
 */
dojo.require("dijit.layout.BorderContainer");
dojo.require("dijit.layout.ContentPane" );
dojo.require("dijit.form.Slider");
dojo.require("dijit.form.HorizontalRule");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.form.NumberTextBox");
dojo.require("dijit.form.CheckBox");

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
    
    createMarkers(map, markers);
    createUi();
    setupClipboardCopy();
    dojo.addOnLoad(function() {
      // add an extra second of timeout for map to load completely
      setTimeout("initializeApp()", 1500);
    });
});

/**
 * hides the spinner, called when page load is complete
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

/**
 * Called after hideLoader takes place
 */
function initializeApp() {
  hideLoader();
  centerMarker.setAnimation(google.maps.Animation.BOUNCE);
}

/**
 * returns the correct image url based on kind and if marker is selected or not
 */
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

/**
 * Retrieve a single record for a school given id and format it for the infobox
 */
function fetch_info_record(id, currentMarker, map) {
  dojo.xhrGet({
    url: "index.php/main/fetch_record/" + id,
    handleAs: "json",
    load: function(data){
      var infoWindowOutput = "<table id='info-window'>";
      
      for (var i in data) {
        infoWindowOutput += "<tr>" +
          "<td><strong>" + i.charAt(0).toUpperCase() + i.slice(1) + "</strong></td>" + 
          "<td>" + data[i] + "</td></tr>";
      }
      infoWindowOutput += '</table>'
      currentMarker.infoWindow.setContent(infoWindowOutput);
      if (openWindow) {
        openWindow.close(map);
      }
      currentMarker.infoWindow.open(map, currentMarker);
      openWindow = currentMarker.infoWindow;
    }
  });
}

/**
 * Creates new XHR filter request and updates map and result table accordingly
 */
function filterMarkers(lat, lng, distance, kind) {
  if (kind === undefined) {
    kind = "";
  } else if (kind instanceof Array) {
    kind = kind.join("|");
  } 
  dojo.xhrGet({
    url: "index.php/main/search/" + lat + "/" + lng + "/" + distance + "/" + kind,
    handleAs: "json",
    load: function(data){
      var records = [];
      
      for(var i=0; i<data.length; i++){
         var id = data[i].id;
         records.push(data[i]);
      }
      
      // set all markers to unselected
      clearFilter();

      if (records.length > 0) {
        dijit.byId('copyButton').set('disabled',false);
      } else {
        dojo.byId('schoolsList').innerHTML = "<p><strong>No Results</strong></p>";
        return;
      }
      clipboardText = '';
      var currentKind = null;
      var resultTable = "<table id='filteredResults'>";
      for (var i=0; i<records.length; i++) {
        record = records[i];
        
        if (currentKind != record.kind) {
          currentKind = record.kind;
          // replace _ with space and capitalize
          currentKindHeader = currentKind.replace('_', ' ').replace(/\w+/g, 
            function(a){
              return a.charAt(0).toUpperCase() + a.substr(1).toLowerCase()
          });
          if (clipboardText != '') clipboardText += '\r'; 
          clipboardText += currentKindHeader + '\r'; 
          resultTable += "<tr><td colspan=2 class='header'>" + currentKindHeader + "</td></tr>";
        }
        clipboardText += record.school + "\r";
        resultTable += '<tr><td>' + record.school + '</td><td>' 
          + Math.floor(record.distance) + '</td></tr>';
        
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

/**
 * Utility function to reset all the markers to unselected state on map
 */
function clearFilter() {
  for (var key in markers) {
    marker = markers[key];
    var selected = false;
    marker.setIcon(lookUpMarker(marker.kind, selected));
    marker.setAnimation(null);
  }
  dijit.byId('copyButton').set('disabled',true);
  dojo.byId('schoolsList').innerHTML = '';
}

function createUi() {
  
  /**
   * Create sliders
   */
  // slider 1
  var rulesNode = document.createElement('div');
  dojo.byId('slider').appendChild(rulesNode);
  
  var slider1Rule = new dijit.form.HorizontalRule({
    count: 6,
    style: "height:5px"
  }, 
  rulesNode);

  var slider1 = new dijit.form.HorizontalSlider({
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
  
  // slider 2
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
  
  /**
   * Distance text box and buttons
   */
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
      clearFilter();
    }
  },
  "clearButton");
  
  
  var copyButton = new dijit.form.Button({
    label: "Copy to Clipboard",
    disabled: true
  },
  "copyButton");
}

function setupClipboardCopy() {
  //set path
  ZeroClipboard.setMoviePath('js/zeroclipboard/ZeroClipboard.swf');
  //create client
  var clip = new ZeroClipboard.Client();
  //event
  clip.addEventListener('mousedown',function() {
  clip.setText(clipboardText);
  });
  //glue it to the button
  clip.glue('copyButton');
}





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Create new record</title>

<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dojo/resources/dojo.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dijit/themes/claro/claro.css" type="text/css" media="all" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script djConfig="parseOnLoad:true" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.5.0/dojo/dojo.xd.js"></script>


<script type="text/javascript">
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.Button");
    dojo.require("dijit.form.ValidationTextBox");
    dojo.require("dijit.form.DateTextBox");
    dojo.require("dijit.form.RadioButton");
    dojo.require("dijit.form.Textarea");
    
    var geocoder = new google.maps.Geocoder();
    
    dojo.addOnLoad(function() {
      var addressField = dijit.byId('address');
      var stateField = dijit.byId('state');
      var cityField = dijit.byId('city');

      addressField.connect(addressField, "onBlur", function() {
        lookUpCoordinate(addressField, stateField, cityField)
        });
      stateField.connect(stateField, "onBlur", function() {
        lookUpCoordinate(addressField, stateField, cityField)
        });
      cityField.connect(cityField, "onBlur", function() {
        lookUpCoordinate(addressField, stateField, cityField)
        });
    });
    
    function lookUpCoordinate(addressField, stateField, cityField) {
      if (addressField.value != '' && stateField.value != '' &&
        cityField.value != '') {
        var processedAddressValue = addressField.value + "," + cityField.value + 
          "," + stateField.value;
        console.log(processedAddressValue);
        geocoder.geocode(
          {address: processedAddressValue},
          function(results) {
            if (results && results.length > 0) {
              console.log(results);
              dijit.byId('latitude').set('value', results[0].geometry.location.Ba);
              dijit.byId('longitude').set('value', results[0].geometry.location.Da);
            } else {
              dijit.byId('latitude').set('value', '');
              dijit.byId('longitude').set('value', '');
            }
          }
        );
      }
    }
    
</script>

<style type="text/css">
  body.claro {
    font-size: 1.5em;
  }
</style>


</head>

<body class="claro" style="height:100%;padding:0;margin:0; overflow:hidden"></body>
<div dojoType="dijit.form.Form" id="insertRecord" jsId="insertRecord"
action="" method="">
    <script type="dojo/method" event="onReset">
        return confirm('Press OK to reset widget values');
    </script>
    <script type="dojo/method" event="onSubmit">
        if (this.validate()) {
          dojo.xhrPost({
            url: 'import/insert_record',
            handleAs: 'text',
            form: dojo.byId('insertRecord'),
            load: function(data) {
              alert('Record successfuly added');
            },
            error: function(error) {
              alert(error);
            }
          });
          return false;
        } else {
          return false;
        }

    </script>
    <table cellspacing="10">
        <tr>
            <td><label for="name">Name:</label></td>
            <td>
                <input type="text" id="name" name="name" trim="true"
                  required="true" dojoType="dijit.form.ValidationTextBox"/>
            </td>
        </tr>
        <tr>
            <td><label for="school">School:</label></td>
            <td>
                <input type="text" id="school" name="school"  trim="true"
                  required="true" dojoType="dijit.form.ValidationTextBox" />
            </td>
        </tr>
        <tr>
            <td><label for="kind">Kind:</label></td>
            <td>
                <input type="radio" dojoType="dijit.form.RadioButton" name="kind" id="kindHs"
                  value="high_school" checked="true"/>
                  <label for="kindHs">
                      High School
                  </label>
                </input>
                <br />
                <input type="radio" dojoType="dijit.form.RadioButton" name="kind" id="kindComm"
                  value="community" />
                  <label for="kindComm">
                      Community College
                  </label>
                </input>
                <br />
                <input type="radio" dojoType="dijit.form.RadioButton" name="kind" id="kind4year"
                  value="four_year" />
                  <label for="kind4year">
                      Four Year College
                  </label>
                </input>
            </td>
        </tr>
        <tr>
            <td><label for="course">Course:</label></td>
            <td>
                <input type="radio" dojoType="dijit.form.RadioButton" name="course" id="courseNew"
                  value="newcourse" checked="true" />
                  <label for="courseNew">
                      New
                  </label>
                </input>
                <input type="radio" dojoType="dijit.form.RadioButton" name="course" id="courseExisting"
                  value="existing" />
                  <label for="courseExisting">
                      Existing
                  </label>
                </input>
            </td>
        </tr>
        <tr>
            <td><label for="email">Email:</label></td>
            <td>
                <input type="text" id="email" name="email" trim="true"
                  required="true" dojoType="dijit.form.ValidationTextBox" />

            </td>
        </tr>
        <tr>
            <td><label for="phone">Phone:</label></td>
            <td>
                <input type="text" id="phone" name="phone" trim="true"
                  required="true" dojoType="dijit.form.ValidationTextBox" />
    
            </td>
        </tr>
        <tr>
            <td><label for="address">Address:</label></td>
            <td>
                <input type="text" id="address" name="address" trim="true"
                   required="true" dojoType="dijit.form.ValidationTextBox" />
            </td>
        </tr>
        <tr>
            <td><label for="city">City:</label></td>
            <td>
                <input type="text" id="city" name="city" trim="true"
                   required="true" dojoType="dijit.form.ValidationTextBox" />
            </td>
        </tr>

                <input type="hidden" id="canary" name="canary" trim="true"
                   value="bird" dojoType="dijit.form.TextBox" />

            <td><label for="state">State:</label></td>
            <td>
                <input type="text" id="state" name="state" trim="true"
                   required="true" dojoType="dijit.form.ValidationTextBox" />

            </td>
        </tr>
        <tr>
            <td><label for="latitude">Latitude:</label></td>
            <td>
                <input type="text" id="latitude" name="latitude"
                invalidMessage="Please ensure that the address information entired above is complete and correct"
                   required="true" readonly="true" dojoType="dijit.form.ValidationTextBox" />
            </td>
        </tr>
        <tr>
            <td><label for="longitude">Longitude:</label></td>
            <td>
                <input type="text" id="longitude" name="longitude"
                   required="true" readonly="true"
                   dojoType="dijit.form.ValidationTextBox" />
            </td>
        </tr>
    </table>
    <button dojoType="dijit.form.Button" type="submit" name="submit"
    value="submit">
        Submit
    </button>
    <button dojoType="dijit.form.Button" type="reset">
        Reset
    </button>
</div>
</body>

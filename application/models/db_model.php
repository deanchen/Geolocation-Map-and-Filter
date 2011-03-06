<?php

class Db_model extends CI_Model {
  
  function __construct()
  {
    // Call the Model constructor
    parent::__construct();
  }
  
  function insert_record($record)
  {
    /*
     * convert coordinate string to mysql geospacial fucntion call
     */
    $coord_string = $record['coordinates'];
    unset($record['coordinates']);
    
    // convert string to function call, if string is null or not well formatted
    // set as null
    
    $coord_array = NULL;
    if ($coord_string != NULL and $coord_string != '') 
    {
      $coord_array = explode(", ", $coord_string);
      if (sizeof($coord_array) != 2) $coord_array = NULL;
    } 
    
    if ($coord_array == NULL) {
      // set to antarctica
      $coord_array = array(-90, 0); 
    }
    print_r($coord_array);
    print("<br />");
    $coord = "GeomFromText('POINT(" . implode(' ', $coord_array) . ")')";
    
    // insert in to db
    $this->db->set($record);
    $this->db->set('coordinate', $coord, FALSE);
    $this->db->insert('point');
  }
  
  /**
   * Return records according to distance
   */
  function get_records($center_array = NULL, $distance = NULL)
  {
    /**
     * Query for points inside square bounding box and then filter points
     * too far away
     * 
     * http://howto-use-mysql-spatial-ext.blogspot.com/
     */
    
    if (sizeof($center_array) == 2 && $distance) 
    {
      $center = "GeomFromText('POINT(" . implode(' ', $center_array) . ")')";
      $bounding_box = "CONCAT('POLYGON((', 
        X($center) - $distance, ' ', Y($center) - $distance, ',', 
        X($center) + $distance, ' ', Y($center) - $distance, ',', 
        X($center) + $distance, ' ', Y($center) + $distance, ',', 
        X($center) - $distance, ' ', Y($center) + $distance, ',', 
        X($center) - $distance, ' ', Y($center) - $distance, '))' 
        )
      ";
      
      $sql = "SELECT name, AsText(coordinate), SQRT(POW( ABS( X(coordinate) - X($center)), 2) + POW( ABS(Y(coordinate) - Y($center)), 2 )) AS distance 
              FROM point 
              WHERE Intersects( coordinate, GeomFromText($bounding_box) ) 
              AND SQRT(POW( ABS( X(coordinate) - X($center)), 2) + POW( ABS(Y(coordinate) - Y($center)), 2 )) < $distance 
              ORDER BY distance;";
      $query = $this->db->query($sql);
      
      print("<pre>");
      print_r($query->result());
      print("</pre>");
    } 
    else 
    {
      print "need more paramters";
    }
    
    
  }
}

<?php

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
    
    $coord = "GeomFromText('POINT(" . implode(' ', $coord_array) . ")')";
    
    // insert in to db
    $this->db->set($record);
    $this->db->set('coordinate', $coord, FALSE);
    $this->db->insert('point');
  }
  
  /**
   * Return a single record given id
   */
  function get_record($id) 
  {
    $this->db->select('id, name, school, kind, course, email, phone');
    $this->db->where('id', $id);
    return $this->db->get('point')->row(); 
  }
  
  /**
   * Return records according to distance
   */
  function get_records($center_array = NULL, $distance = NULL, $kinds = NULL)
  {
    /**
     * Query for points inside square bounding box and then filter points
     * too far away
     * 
     * Latitude is approx 69m per unit
     * Longitude is 69m-0 per unit
     * 
     * Accurate to +- 1 mile
     * 
     * http://howto-use-mysql-spatial-ext.blogspot.com/
     */
    
    if (sizeof($center_array) == 2 && is_int($distance)) 
    {
      $kinds_clause = NULL;
      if ($kinds and sizeof($kinds) > 0) {
        $kinds_clause = "AND `kind` IN ('" . implode('\',\'', $kinds) . "')";
      }
      $distance_bound = $distance / 69;
      
      $center = "GeomFromText('POINT(" . implode(' ', $center_array) . ")')";
      $bounding_box = "CONCAT('POLYGON((', 
        X($center) - $distance_bound, ' ', Y($center) - $distance_bound, ',', 
        X($center) + $distance_bound, ' ', Y($center) - $distance_bound, ',', 
        X($center) + $distance_bound, ' ', Y($center) + $distance_bound, ',', 
        X($center) - $distance_bound, ' ', Y($center) + $distance_bound, ',', 
        X($center) - $distance_bound, ' ', Y($center) - $distance_bound, '))' 
        )
      ";
      
    
      $distance_formula = "3956 * 2 * ASIN(SQRT(POWER(SIN((X($center) - X(coordinate)) 
      * pi()/180 / 2), 2) + COS(X($center) * pi()/180) *COS(X(coordinate) * pi()/180) 
      * POWER(SIN((Y($center) - Y(coordinate)) * pi()/180 / 2), 2)))";
      
      $sql = "SELECT id, school, kind, X(coordinate) as lat, Y(coordinate) as lng, $distance_formula AS distance 
              FROM point 
              WHERE MBRContains(GeomFromText($bounding_box), coordinate) 
              $kinds_clause
              AND $distance_formula < $distance 
              ORDER BY `kind` ASC, `distance` ASC;";

      $query = $this->db->query($sql);
      return $query->result();
      /*
      print "<pre>";
      print_r($query->result());
      print "</pre>";
      */
      
    } 
    else 
    {
      print "Not enough paramwters";                  
    }
  }
  
  function get_all_records()
  {
    $this->db->select('id, school, kind, X(coordinate) AS lat, 
                 Y(coordinate) AS lng', FALSE);
    return $this->db->get('point')->result();  
  }
}

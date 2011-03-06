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
    $coord = NULL;
    if ($coord_string != NULL and $coord_string != '') 
    {
      $coord_array = explode(", ", $coord_string);
      
      if (sizeof($coord == 2)) { // make sure we have two coordinates
        $coord = "GeomFromText('POINT(" . implode(' ', $coord_array) . ")')";
      } else {
        $coord = NULL;
      }
    }
    
    // insert in to db
    $this->db->set($record);
    if ($coord) $this->db->set('coordinate', $coord, FALSE);
    $this->db->insert('point');
  }
}

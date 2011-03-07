<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

  function __construct()
  {
    parent::__construct();
  }

  function index()
  {
    
  }
  
  function search($x, $y, $distance, $kinds = NULL)
  {
    if ($kinds) {
      $kinds = explode('%7C', $kinds);
    }
    print(json_encode($this->db_model->get_records(array($x, $y), (int) $distance, $kinds)));
  }
}
    
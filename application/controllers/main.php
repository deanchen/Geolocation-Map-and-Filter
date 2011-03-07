<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

  function __construct()
  {
    parent::__construct();
  }

  function index()
  {
    $this->load->view('main_view', array('markers' => $this->get_all(FALSE)));
  }
  
  function search($x, $y, $distance, $kinds = NULL)
  {
    if ($kinds) {
      $kinds = explode('%7C', $kinds);
    }
    
    $result = json_encode($this->db_model->get_records(
                      array($x, $y), (int) $distance, $kinds));
    print($result);
  }
  
  function fetch_record($id)
  {
    $result = json_encode($this->db_model->get_record($id));
    print($result);
  }
  
  function get_all($print = TRUE)
  {
    $result = json_encode($this->db_model->get_all_records());
    
    if ($print) print $result;
    return $result;
  }
}
    
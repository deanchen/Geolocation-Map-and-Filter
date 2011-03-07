<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import extends CI_Controller {

  function __construct()
  {
    parent::__construct();
  }

  function index()
  {
    $this->load->view('import_view');
    echo "Go to /import_and_remove_csv/csvfile.csv";
  }
  
  /**
   * Read in a correctly formatted csv file and import contents in to database 
   * using db_model and delete the csv file afterwards to reduce the chance of
   * double import
   */
  function import_and_remove_csv($filename)
  {
    $fields = array();
    $records = array();  
    $header = true;
    $count = 0;
    if (($handle = fopen(getenv("DOCUMENT_ROOT") . "/$filename", "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (!$header) {
          for ($c=0; $c < count($data); $c++) {
              $record[$fields[$c]] = $data[$c];
          }
          
          $this->db_model->insert_record($record);
          $count++;
        } else {
          $fields = $data;
          $header = false;
        }   
      }
      fclose($handle);
    }
    echo "Successfully inserted $count records";
  }
}
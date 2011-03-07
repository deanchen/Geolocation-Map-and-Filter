<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
    
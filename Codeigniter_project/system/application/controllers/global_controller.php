<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of global_controller
 * @package PL
 * @subpackage
 * @author SM@K <mudaser.ali@pruelogics.net>
 */

/**
 * @property Users_m $User
 */
class Global_controller extends MY_Controller
{
  function Global_controller()
  {
    parent::__construct();
    $this->load->model('User_m',"User");
  }

  function update_user_info()
  {
    $user_name = $this->input->post('first_name') . ' ' . $this->input->post('last_name');
    $first_name = $this->input->post('first_name') ;
    $last_name = $this->input->post('last_name');
    $email = $this->input->post('email');
    $new_email = '';
    if(trim($this->input->post('new_email')))
    {
      $new_email = $this->input->post('new_email');
    }

    $user = current($this->User->getUserByEmail($email));
    $response = array('status' => '0');
    if(count($user))
    {
      $data = array('user_name' => $user_name, 'first_name' => $first_name, 'last_name' => $last_name);
      if($new_email != '')
      {
        $data['email'] = $new_email;
      }
      //mail('mudaser.ali@purelogics.net', 'test', print_r($data, true));

      if($this->User->updateMerchangeInfoById($data, $user['id']))
      {
        $response = array('status' => '1');
      }
    }
    exit(json_encode($response));
  }
}


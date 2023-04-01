<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 *
 * @package
 *
 *
 */

class Products_deleted_m extends MY_Model {

  function insert($data)
  {
    $data['created_at'] = date('Y-m-d H:i:s');

    return parent::insert($data);
  }

  function update($id, $input)
  {
    return parent::update($id, $input);
  }

  /**
   *
   * function getMerchantsByEmail
   *
   * @param <string>   $email
   *
   */
  function getMerchantsByEmail($email)
  {
    $this->db->select('email as title, id, username');

    $this->db->like('email', $email, 'after');

    return $this->db->get($this->_table_users)->result();
  }
}

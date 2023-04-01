<?php

class Footer extends MY_Controller
{

  function Footer()
  {
    parent::__construct();
    $this->load->model('alerts_messages_m', 'footer');
  }

  function index()
  {

  }

  function alert_messages()
  {
    return $this->footer->alert_messages($this->store_id);
  }

  function get_message($user_id)
  {
    return $this->footer->get_message($user_id);
  }

  function get_message_detail($message_id)
  {
	$this->_response_type('json');
    $message_detail = $this->footer->get_a_message($message_id);
    $this->data->heading = $message_detail[0]->subject;
    $this->data->html = '<table width="600">';
    $this->data->html .= '<tr><td style="line-height:23px;"><div style="max-height:300px; overflow:auto;"><p>' . $message_detail[0]->message . '</p></div></td></tr>';
    $this->data->html .= '<tr><td align="right"><a href="javascript:void(0);" onclick="show_unread_messages(' . $this->user_id . ');">Back</a></td></tr>';
    $this->data->html .= '</table>';
    $this->db->where('message_id', $message_id);
    $this->db->update('email_reference', array('is_message_checked' => '1'));
  }

  function show_news($news_id)
  {
	$this->_response_type('json');
    $news_details = $this->footer->show_news($news_id);
    $this->data->heading = $news_details[0]->heading;
    $this->data->html = '<table width="600">';
    $this->data->html .= '<tr><td style="line-height:23px;"><div style="max-height:300px; overflow:auto;"><p>' . $news_details[0]->description . '</p></div></td></tr>';
    $this->data->html .= '</table>';
  }

  function delete_alerts()
  {
    if (isset($_POST)) {
      $store_id = $_POST['store_id'];
      $datetime = $_POST['datetime'];
      $this->footer->delete_alerts($store_id, $datetime);
      echo $this->footer->alert_messages($this->store_id);
    }
  }
}

<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

function get_messages($user_id)
{
  $CI = & get_instance();
  $CI->load->model('alerts_messages_m', 'footer');
  return $CI->footer->get_message($user_id);
}

function footer_alerts($store_id)
{
  $CI = & get_instance();
  $CI->load->model('alerts_messages_m', 'footer');
  return $CI->footer->alert_messages($store_id);
}

function get_news($user_id)
{
  $CI = & get_instance();
  $CI->load->model('alerts_messages_m', 'footer');
  return $CI->footer->news_messages($user_id);
}

function global_news_count($user_id, $result_merchant)
{
  if (count($result_merchant) > 0)
    $html = "You have <b> <a href='javascript:void(0);' style='color:#333333;' onclick='show_unread_messages(" . $user_id . ");'>" . count($result_merchant) . " unread </a> </b> items.";
  else
    $html = "You have <b> <a href='javascript:void(0);' style='color:#333333;' onclick='show_empty_messages(" . $user_id . ");'>" . count($result_merchant) . " unread </a> </b> items.";
  return $html;
}
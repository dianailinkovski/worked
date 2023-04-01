<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Alerts_messages_m extends MY_Model {

	function Alerts_messages_m() {
		parent::MY_Model();
	}

	function alert_messages($store_id) {
		$today = date('Y-m-d', strtotime("now"));
		$yesterday = date('Y-m-d', strtotime("-1 day"));
		$result = array();

		$html = '';
		if (count($result) > 0) {
			$html .= '<div class="section clearfix">';
			$html .= '<h4>Today</h4>';
			foreach ($result as $row) {

				$html .= '<div class="content_alert">You have <strong>'.$row->count.' new violation</strong></div>';
				$html .= '<div class="action_alert"><a href="javascript:void(0);"><img src="'.frontImageUrl().'close.png" alt="Close" title="Close" onclick="delete_alerts(\''.$store_id.'\', \''.$row->datetime_tracked.'\');" /></a></div>';
				$html .= '<div class="clear"></div>';
			}
			$html .= '</div>';
		} else {
			$html .= '<div class="section clearfix">';
			$html .= '<h4>Today</h4>';
			$html .= "0 Alerts";
			$html .= '</div>';
		}

		$result = array();

		if (count($result) > 0) {
			$html .= '<div class="section clearfix">';
			$html .= '<h4>'.date('F j, Y', strtotime("-1 day")).'</h4>';
			foreach ($result as $row) {
				$html .= '<div class="content_alert">You have <strong>'.$row->count.' new violation</strong></div>';
				$html .= '<div class="action_alert"><a href="javascript:void(0);"><img src="'.frontImageUrl().'close.png" alt="Close" title="Close" onclick="delete_alerts(\''.$store_id.'\', \''.$row->datetime_tracked.'\');" /></a></div>';
				$html .= '<div class="clear"></div>';
			}
			$html .= '</div>';
		} else {
			$html .= '<div class="section clearfix">';
			$html .= '<h4>'.date('F j, Y', strtotime("-1 day")).'</h4>';
			$html .= "0 Alerts";
			$html .= '</div>';
		}

		$bad_upc = $store_id ? getBadUPCs($store_id) : array();

		$html .= '<div class="section clearfix">';
		$html .= '<h4>Today</h4>';
		$html .= '<a href="'.base_url().'catalog">'.count($bad_upc). " Bad UPC's</a>";
		$html .= '</div>';

		return $html;
	}

	function get_message($store_id) {
		$query_merchant = $this->db->get_where('email_reference', array('receiver_id' => $store_id, 'is_message_checked' => '0'));
		$result_merchant = $query_merchant->result('array');
		if (count($result_merchant) > 0)
			$html = "You have <b> <a href='javascript:void(0);' style='color:#333333;' onclick='show_unread_messages(".$store_id.");'>".count($result_merchant)." unread </a> </b> items.";
		else
			$html = "You have <b> <a href='javascript:void(0);' style='color:#333333;' onclick='show_empty_messages(".$store_id.");'>".count($result_merchant)." unread </a> </b> items.";
		return $html;
	}

	function get_all_messages($store_id) {
		return $this->db->get_where('email_reference', array('receiver_id' => $store_id, 'is_message_checked' => '0'))->result();
	}

	function get_a_message($message_id) {
		return $this->db->get_where('email_messages', array('id' => $message_id))->result();
	}

	function delete_alerts($store_id, $datetime) {
		/*            $query = "Update violations set is_alert_delete = '1' WHERE store_id = '".$store_id."' AND datetime_tracked = '".$datetime."' "; */

		$crieteriaUpdate = array(
			'store_id' => $store_id,
			'datetime_tracked' => $datetime
		);
		$updatedFields = array('$set' => array('is_alert_delete' => '1'));
		// ViolationsMongo::updateMultipleRecord($crieteriaUpdate, $updatedFields);
		//$this->db->query($query);
	}

	function show_news($news_id) {
		return $this->db->get_where('news', array('id' => $news_id))->result();
	}

	function news_messages() {
		$this->db->order_by("id", "desc");
		$result = $this->db->get('news')->result('array');
		$html = '';
		$html .= '<h1>News</h1>';
		$divCount = 0;
		if (count($result) > 0) {
			foreach ($result as $news) {
				$divCount++;
				if ($divCount > 5) {
					$display = 'style="display:none;"';
				} else {
					$display = 'style="display:blcok;"';
				}
				$html .= '<div class="content_are_footer" id="news_'.$divCount.'" '.$display.' >';
				$html .= '<ul class="question_answer">';
				$html .= '<li class="question">'.$news['heading'].'</li>';
				$html .= '<li class="ans"><a href="javascript:void(0);" onclick="show_news('.$news['id'].')">Click here to learn more!</a></li>';
				$html .= '</ul>';
				$html .= '</div>';
			}
			if ($divCount > 5) {
				$html .= '<div class="content_are_footer" id="readmore">';
				$html .= '<ul class="question_answer">';
				$html .= '<li class="question"><a href="javascript:void(0);" onclick="show_more_news('.$divCount.')">Read More</a></li>';
				$html .= '</ul>';
				$html .= '</div>';

				$html .= '<div class="content_are_footer" id="readless" style="display:none">';
				$html .= '<ul class="question_answer">';
				$html .= '<li class="question"><a href="javascript:void(0);" onclick="show_less_news('.$divCount.')">Read Less</a></li>';
				$html .= '</ul>';
				$html .= '</div>';
			}
		} else {
			$html .= '<div class="content_are_footer">';
			$html .= '<ul class="question_answer">';
			$html .= '<li class="question">0 News</li>';
			$html .= '</ul>';
			$html .= '</div>';
		}
		return $html;
	}

}
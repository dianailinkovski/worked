<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * email_helper.php
 *
 * Extends the CI email helper
 */

/**
 * Send an email to the email addresses listed in the alerts config
 *
 * @param String $msg
 * @param String $subject
 * @param String $from
 * @param array $to { default : NULL }
 * @return boolean
 */
function send_alert($msg, $subject = 'TrackStreet Alert', $from = 'TrackStreet', $to = NULL)
{
	$ci =& get_instance();
	if (empty($to))
		$to = $ci->config->item('alerts');

	if ( ! is_array($to))
		$to = array((String)$to);

	return send_email($to, $from, $subject, $msg);
}

/**
 * Send an email
 *
 * @access public
 * @return bool
 */
function send_email($to, $from, $subject, $html, $text = ''){
	//we REALLY need to verify 'confirmed' - we cannot assume that we can just send here, it might kill our SES account
	$userInfo = array(
		'email' => $to,
		'confirmed' => 1
	);
	return sendEmailSES($to, $subject, $html, $text, $userInfo, 'support@juststicky.com');
}

function template_email_send($template_type, $user_id, $subject='',$receiver ='' ,$htmlContent = '', $textContent = ''){
	$CI =& get_instance();
	//$CI->load->model('admin/Merchant_model','Merchant');
	$result_template = getEmailTemplateByType($template_type);
	$query_merchant = $CI->db->get_where($CI->_table_users, array('id'=>$user_id));
	$result_merchant = $query_merchant->result();

	$user_info = array(
		'email' => isset($result_merchant[0])?$result_merchant[0]->email:'',
		'confirmed' => 1
	);

	if(strtolower($template_type) == 'general'){
		$subject = $result_template->subject.'['.$subject.']';
	}else {
		$subject = $result_template->subject;
	}
	$recipient = ($receiver != '')?$receiver:$result_merchant[0]->email;
	$from = $result_template->from_name;

	$subject = str_replace('[','',$subject);
	$subject = str_replace(']','',$subject);
	$subjectHalf = explode(':',$subject);
	$subJectExpl = explode(' ',$subjectHalf[1]);
	$newSubJect = $subjectHalf[0].': ';
	foreach($subJectExpl as $k=>$v){
		$newSubJect.= ($k==0) ? ucfirst($v): ' '.ucfirst($v);
	}

	//echo "recipient: $recipient \n"; 
	//echo "newSubJect: $newSubJect \n"; 
	//echo "htmlContent: $htmlContent \n"; 
	//echo "textContent: $textContent \n"; 
	//echo "user_info: ".print_r($user_info,true)." \n"; 
	//echo "from: $from \n";
	//exit;
	//return;
	//$recipient = 'chris@juststicky.com';
	return sendEmailSES($recipient, $newSubJect, $htmlContent, $textContent, $user_info, $from);
}

function sendEmailSES($email, $subject, $htmlMessage, $txtMessage, $userInfo, $from){
	//$email .= ',chris@juststicky.com,andrew@juststicky.com';
	
  if (is_array($email))
  {
      $email[] = 'chris@juststicky.com';     
      $email[] = 'andrew@juststicky.com';
  }
  else
  {
      $email .= ',chris@juststicky.com,andrew@juststicky.com';
  }

	$ret = false;

	$urltopost = "http://ses.juststicky.com/api/index.php";
	$datatopost = array (
		"service_key" => "Sticky_MarketVision",
		"action" => "sendEmail",
		"email" => '',
		"from" => $from,
		'user_info' => $userInfo,
		"message_content" => array(
			"Subject" => $subject,
			"txtMessage" => $txtMessage,
			"htmlMessage" => $htmlMessage
		)
	);
	if( ! is_array($email)){
		$email = explode(',',$email);
	}
	$datatopost['email'] = $email;
	$datatopost['user_info']['email'] = $email;
	//debug('DATA TO POST', $datatopost, 2);

	$ch = curl_init($urltopost);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datatopost) );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$returndata = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	$data = json_decode($returndata);

	if(isset($data->status) && ! empty($data->ses_status) && isset($data->ses_code)){
		if($data->status === '200' && $data->ses_code === '001'){
			return true;
			//$ret = true;
		}
	}
	//else
	
	//temporarily log all messages until stable
	log_message('error', 'sendEmailSES failed: '.var_export($data,true));
	return $ret;
}

/**
 * Send an email using SMTP
 *
 * @param SMTP_auth $smtpInfo
 * @param string/array $to Either the email or an array in the format Email => Name
 * @param String $subject
 * @param String $html
 * @param String $text
 * @param string/array $from Either the email or an array in the format Email => Name
 * @param array $attachments
 * @return boolean
 */
function send_smtp(SMTP_auth $smtpInfo, $to, $subject, $html, $text = '', $from = 'support@juststicky.com', $attachments = array(), $exceptions = TRUE) {

  require_once(APPPATH . 'libraries/phpmailer/class.phpmailer.php');

  $mail = new PHPMailer($exceptions);

  // configure SMTP Settings

  //for some reason, IsSMTP() causes it to fail sometimes. Example - irwinnaturals client
  //see this nugget: http://stackoverflow.com/questions/1233291/problem-with-smtp-authentication-in-php-using-phpmailer-with-pear-mail-works#answer-12410579

  // If this isn't called this function always returns true and no exceptions are thrown
  // which are used to notify users of failed SMTP emails.
  $mail->IsSMTP();
  $mail->AddBCC('chris@juststicky.com');

  $mail->SMTPAuth = true;
  //$mail->SMTPSecure = $smtpInfo->get('use_ssl') == '1' ? 'ssl' : '';
  if( $smtpInfo->get('use_ssl') == 'YES') {
    $mail->SMTPSecure = 'ssl';
  }
  elseif($smtpInfo->get('use_tls') == 'YES') {
    $mail->SMTPSecure = 'tls';
  }
  else {
    $mail->SMTPSecure = '';
  }
  $mail->Host = $smtpInfo->get('host');
  $mail->Port = $smtpInfo->get('port');
  $mail->Username = $smtpInfo->get('username');
  $mail->Password = $smtpInfo->get('password');
  $mail->SMTPDebug = 1;
  // 1 = errors and messages
  // 2 = messages only

  // Set the sender information
  if ( ! is_array($from))
    $from = array($from => $from);
  foreach ($from as $email => $name) {
    $mail->SetFrom($email, $name);
    $mail->AddReplyTo($email, $name);
  }

  // Set the recipients
  if ( ! is_array($to))
    $to = array($to => $to);
  foreach ($to as $email => $name) {
    if (is_numeric($email))
      $email = $name;
    $mail->AddAddress($email, $name);
  }

  // Set the Content
  $mail->Subject = $subject;
  $mail->AltBody = $text;
  $mail->MsgHTML($html);

  // Add the attachments
  if($attachments){
	foreach ($attachments as $attachment){
		$mail->AddAttachment($attachment);
	}
  }

  if ( ! $mail->Send()) {
  	//echo 'mail error';
  	//var_dump($mail->ErrorInfo);
    log_message('error', "Mailer Error: " . $mail->ErrorInfo);

    return FALSE;
  }

  return TRUE;
}
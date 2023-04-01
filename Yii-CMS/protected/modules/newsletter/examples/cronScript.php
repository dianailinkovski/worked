<?php
$newsletters = array(
	'http://site1.com/fr/newsletter/api' => '<apiKey>',
	'http://site2.com/newsletter/api' => '<apiKey>',
);



ini_set("log_errors", 1);
ini_set("error_log", "mailer-error.log");
set_time_limit(0);
chdir(realpath(dirname(__FILE__)));

if (file_exists('mailer.log') && filesize('mailer.log') > 1024 * 30)
{
	if (file_exists('mailer-old.log'))
		unlink('mailer-old.log');

	rename('mailer.log', 'mailer-old.log');
}

$currentTime = date('Y-m-d H:i:s');
$mailsPerConnection = 30;

require_once('PHPMailer/class.phpmailer.php');


function call($url, $xmlFormat=true)
{
	logMessage('call '.$url);
	
	$result = file_get_contents($url);
	if ($result === false)
	{
		logMessage('call failed');
	}
	else {
		if ($xmlFormat) 
		{
			$xml = new SimpleXMLElement($result);
			return $xml;
		}
		else {
			return $result;
		}
	}
}

function logMessage($message)
{
	global $currentTime;
	
	file_put_contents('mailer.log', "\n".'['.$currentTime.']  '.$message."\n", FILE_APPEND);
}


logMessage('beginning operations');

foreach ($newsletters as $newsletter => $key)
{
	logMessage('starting '.$newsletter);
	
	$check = call($newsletter.'/check/key/'.$key);
	
	if ((string)$check->send == 'true')
	{
		logMessage('check passed, sending emails');

		$newsletterContents = array();
		
		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
		
		$mail->IsSMTP();
		$mail->SMTPKeepAlive = true;
		$mail->Host       = "";
		//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
		$mail->SMTPAuth   = true;
		$mail->Port       = 587;
		$mail->Username   = "";
		$mail->Password   = "";
		$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
		$mail->SetFrom('', (string)$check->fromname);
		$mail->CharSet = 'UTF-8';
		
		$i = 0;
		while (true)
		{
			$subs = call($newsletter.'/getsubs/key/'.$key.'/limit/'.$mailsPerConnection.'/offset/'.$i);

			$entryCount = count($subs->entry);
			
			if ($entryCount == 0)
			{
				logMessage('no more emails');
				break;
			}
			else {
				logMessage('sending batch #'.(($i / $mailsPerConnection)+1));
			}
		
			foreach ($subs->entry as $entry)
			{
				$bodyUrl = (string)$entry->bodyurl;
				
				if (!array_key_exists($bodyUrl, $newsletterContents))
					$newsletterContents[$bodyUrl] = call($bodyUrl.'/key/'.$key, false);

				$body = str_replace('%7B%7B%7Btoken%7D%7D%7D', base64_encode(serialize(array('id'=>(string)$entry->id, 'email'=>(string)$entry->email))), $newsletterContents[$bodyUrl]);

				try {
					$mail->ClearAddresses();
					$mail->AddAddress((string)$entry->email);
					$mail->Subject = (string)$entry->subject;
					$mail->MsgHTML($body);
					$mail->Send();
				}
				catch (phpmailerException $e) {
					logMessage('phpmailerException: '.$e->errorMessage());
				}
				catch (Exception $e) {
					logMessage('Exception: '.$e->getMessage());
				}
			}
			
			$i += $mailsPerConnection;
		}
		
		$mail->SmtpClose();
		logMessage('done with '.$newsletter);

		call($newsletter.'/update/key/'.$key, false);
	}
	else {
		logMessage('check failed, not sending emails');
	}
}

logMessage('all operations completed');
?>
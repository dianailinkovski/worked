<?php
/**
 * General Helper class
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Helper
 */

class Helper
{
	/**
	 * Generate a random string
	 * 
	 * @param int $length the length of the hash. Default 32.
	 * 
	 * @return string the string
	 */
	public static function randStr($length=32)
	{
		return substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',$length)),0,$length);
	}
	
	/**
	 * Log message to a file.
	 * 
	 * @param string $filename the file name (and path)
	 * @param string $msg the message
	 */
	public static function logToFile($filename, $msg)
	{ 
		$fd = fopen($filename, "a");
		
		$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg; 
		
		fwrite($fd, $str . "\n");
		
		fclose($fd);
	}

	/**
	 * Add a suffix to a file (following an underscore, before the file extension).
	 * 
	 * @param $name string the name of the file.
	 * @param $suffix string the wanted suffix.
	 * 
	 * @return string the modified file name.
	 */
    public static function fileSuffix($name, $suffix)
    {
    	return preg_replace('/^(.+)(\.\w+)$/u', '$1_'.$suffix.'$2', $name);
    }
    
	/**
	 * Replace accentuated characters with their non accentuated version.
	 * 
	 * Note that this might not catch everything and if you need clean the string of all special characters you still need to filter it.
	 * 
	 * @param string $str string to operate on.
	 * 
	 * @return string the string without accents.
	 */
	public static function removeAccents($str) 
	{
	    $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
	    $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	    return str_replace($a, $b, $str);
	}

	/**
	 * Encode file name (replace any special character with an underscore)
	 * 
	 * @param string $name the file name
	 * 
	 * @return string the filtered string
	 */
    public static function encodeFileName($name)
    {
    	return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $name);
    }
    
	/**
	 * Format a date
	 *
	 * Note that some translation messages are used.
	 * 
	 * @param string $date the date, many formats are supported, @see php function strtotime.
	 * @param string $type type of formatting: reg, reg+time, timesince, post, short, slashes, slashes+time, rfc2822
	 * @param string $language the language of formatting, "fr" and "en" are supported. Defaults to Yii language.
	 * 
	 * @return string the filtered string
	 */
	public static function formatDate($date, $type, $language=null) 
	{
		$unixDate = strtotime($date);
		
		if (!$language)
			$language = Yii::app()->language;

		if ($type == 'reg' || $type == 'reg+time')
		{
			if ($language == 'en') 
			{
				 return Yii::t('common', date('F', $unixDate)).($type == 'reg' ? date(' j, Y', $unixDate) : date(' j, Y, H:i:s', $unixDate));
			} 
			else if ($language == 'fr') 
			{
				$day = date('j', $unixDate);
				$daySuffix = $day == 1 ? 'er' : '';
				
				return $day.$daySuffix.' '.mb_strtolower(Yii::t('common', date('F', $unixDate)), "UTF-8").($type == 'reg' ? date(' Y', $unixDate) : date(' Y, H:i:s', $unixDate));
			}
		}
		else if ($type == 'timesince')
		{
			$year = date('Y', $unixDate);
			$month = date('m', $unixDate);
			$day = date('d', $unixDate);
			$hour = date('H', $unixDate);
			$minute = date('m', $unixDate);
			$second = date('s', $unixDate);
			
			$yeartoday = date('Y');
			$monthtoday = date('m');
			$daytoday = date('d');
			$hourtoday = date('H');
			$minutetoday = date('i');
			$secondtoday = date('s');
			
			$returnDate = '';
			
			if ($language == 'fr') 
				$returnDate .= 'il y a ';
			
			if ($yeartoday > $year)
				 $returnDate .= ($yeartoday - $year).' '.Yii::t('common', 'years');
			else if ($monthtoday > $month) 
				$returnDate .= ($monthtoday - $month).' '.Yii::t('common', 'months');
			else if ($daytoday > $day)
				$returnDate .= ($daytoday - $day).' '.Yii::t('common', 'days');
			else if ($hourtoday > $hour)
				$returnDate .= ($hourtoday - $hour).' '.Yii::t('common', 'hours');
			else if ($minutetoday > $minute)
				$returnDate .= ($minutetoday - $minute).' '.Yii::t('common', 'minutes');
			else if ($secondtoday > $second)
				$returnDate .= ($secondtoday - $second).' '.Yii::t('common', 'seconds');
			
			if ($language == 'en') 
				$returnDate .= ' ago';
		}
		else if ($type == 'post')
		{
			return Yii::t('common', date('F', $unixDate)).date(' j g:ia', $unixDate);
		}		
		else if ($type == 'short')
		{
			return Yii::t('common', date('F', $unixDate)).date(' j', $unixDate);
		}		
		else if ($type == 'slashes')
		{
			return date('m/d/Y', $unixDate);
		}		
		else if ($type == 'slashes+time')
		{
			return date('m/d/Y H:i:s', $unixDate);
		}		
		else if ($type == 'rfc2822')
		{
			return date('r', $unixDate);
		}
		
		return $returnDate;
	}

	/**
	 * Generate breadcrumbs from CMS alias. Uses cache.
	 * 
	 * @param bool $linkLast wether to link the last item or not.
	 * @param int $aliasId the alias id. Defaults to the current alias.
	 * 
	 * @return array the breadcrumbs.
	 */
    public static function breadcrumbsFromAlias($linkLast=false, $aliasId=null)
    {
    	$controller = Yii::app()->controller;
    	$breadcrumbs = array();

    	if ($aliasId === null)
    	{
    		$currentAlias = true;
    		$aliasId = Yii::app()->cms->currentAlias->primaryKey;
    	}
    	else
    		$currentAlias = false;

    	if (($serializedBreadcrumb = Yii::app()->cache->get('aliasBreadcrumb_'.$aliasId)) === false)
    	{
	    	if ($currentAlias)
	    		$aliasModel = Yii::app()->cms->currentAlias;
	    	else
	    		$aliasModel = CmsAlias::model()->findByPk($aliasId);

	    	$ancestors = $aliasModel->ancestors()->findAll();
	 
	    	foreach ($ancestors as $ancestor)
	    	{
	    		if ($ancestor->level != 1)
	    		{
		    		$routes = $ancestor->routes;
		    		
			    	if (count($routes) == 0)
			    	{
			    		$breadcrumbs[] = $ancestor->title;
			    	}
			    	elseif (is_null($ancestor->section))
			    		$breadcrumbs[] = $ancestor->title;
			    	else
			    	{
						$aliasUrl = $controller->createUrl('/', array('keyword'=>$ancestor->keyword));
			    		$breadcrumbs[$ancestor->title] = $aliasUrl;
			    	}
	    		}
	    	}
	        $routes = $aliasModel->routes;
	    	
	    	if (!$linkLast || count($routes) == 0 || is_null($aliasModel->section))
	    		$breadcrumbs[] = $aliasModel->title;
	    	else
	    	{
				$aliasUrl = $controller->createUrl('/', array('keyword'=>$aliasModel->keyword));
				$breadcrumbs[$aliasModel->title] = $aliasUrl;
	    	}
	    	Yii::app()->cache->set('aliasBreadcrumb_'.$aliasId, serialize($breadcrumbs));
    	}
    	else
    		$breadcrumbs = unserialize($serializedBreadcrumb);

    	return $breadcrumbs;
    }
    
	/**
	 * Generate page title from the breadcrumbs
	 * 
	 * @param array $bc the breadcrumbs array. Defaults to "breadcrumbs" variable in controller, if it exists.
	 * @param string $sep the separator string between items. Defaults to "titleSeparator" variable in controller, if it exists.
	 * 
	 * @return array the title.
	 */
    public static function titleFromBreadcrumbs($bc=null, $sep=null)
    {
    	$activeController = Yii::app()->controller;

    	if ($bc !== null)
    		$breadcrumbs = $bc;
    	else if (isset($activeController->breadcrumbs))
    		$breadcrumbs = $activeController->breadcrumbs;
    	else
    		$breadcrumbs = array();
    		
    	if ($sep !== null)
    		$separator = $sep;
    	else if (isset($activeController->titleSeparator))
    		$separator = $activeController->titleSeparator;
    	else
    		$separator = ' - ';
    		
    	$out = "";

    	foreach ($breadcrumbs as $breadcrumbkey => $breadcrumb)
    	{
    		if (!is_numeric($breadcrumbkey))
    			$label = $breadcrumbkey;
    		else
    			$label = $breadcrumb;

    		$out = $label.$separator.$out;
    	}
		$out .= Yii::app()->name;
    	
    	return $out;
    }
    
	/**
	 * Query all messages of a category
	 * 
	 * @param string $category the category
	 * 
	 * @return array the messages
	 */
    public static function messageCategoryArray($category)
    {
    	$array = array_reduce(Yii::app()->db->createCommand()
				->select('s.message, m.translation')
				->from('SourceMessage s')
				->leftjoin('Message m', 's.id=m.id')
				->leftjoin('MessageOrder mo', 's.id=mo.id')
				->where('s.category=:category AND (m.language=:language OR m.language IS NULL)', array(
					':category'=>$category, 
					':language'=>Yii::app()->language))
				->order('mo.rank')
				->queryAll(), 
			function($data, $item){
				$data[$item['message']] = isset($item['translation']) ? $item['translation'] : $item['message'];
				return $data;
			}
		);
		
    	return empty($array) ? array() : $array;
    }
    
	/**
	 * Recursively delete a directory
	 * 
	 * @param string $dir the path to the directory
	 */
	public static function rrmdir($dir) 
	{
	    if (is_dir($dir)) 
	    {
	        $objects = scandir($dir);
	     
	        foreach ($objects as $object) 
	        {
	            if ($object != "." && $object != "..")
	            {
		            if (filetype($dir."/".$object) == "dir") 
		          		rrmdir($dir."/".$object); 
		          	else 
		          		unlink($dir."/".$object);
	            }
	        }
	        reset($objects);
	        rmdir($dir);
	    }
	}

	/**
	 * Send an email using Yii params
	 *
	 * Add a "mail" array to the Yii params with the following keys: Host, Username, Password, Port, Form, FormName.
	 * 
	 * @param string $to the email to send to
	 * @param string $subject the email subject
	 * @param string $body the email body
	 * 
	 * @return bool|string bool true if success, string with error info if error
	 */
    public static function sendMail($to, $subject, $body)
    {    	
		$mailer = Yii::createComponent('application.extensions.mailer.EMailer');
				        		
		$mailer->Host = Yii::app()->params['mail']['Host'];
		$mailer->IsSMTP();
		$mailer->SMTPAuth = true;
		$mailer->Username = Yii::app()->params['mail']['Username'];
		$mailer->Password = Yii::app()->params['mail']['Password'];
		$mailer->Port = Yii::app()->params['mail']['Port'];
							
		$mailer->From = Yii::app()->params['mail']['From'];
		$mailer->FromName = Yii::app()->params['mail']['FromName'];
		
		$mailer->IsHTML(true);
		$mailer->CharSet = 'UTF-8';
		$mailer->AltBody = "Your e-mail program does not support HTML, the content of this email could not be displayed.";
						
		$mailer->AddAddress($to);
						
		$mailer->Subject = $subject;
		$mailer->Body = $body;

		if(!$mailer->Send())
			return $mailer->ErrorInfo;
		else
			return true;
    }
    
	/**
	 * Format a readable string from the mime type of a file
	 *
	 * Uses Yii translation fonction
	 * 
	 * @param string $mimetype the mime type
	 * 
	 * @return string the string
	 */
    public static function formatMimeType($mimetype)
    {
		switch ($mimetype) 
		{
			case "image/gif";
				$type = Yii::t('blocs', 'Image au format {format}', array('{format}'=>'GIF'));
				break;
			case "image/jpeg";
				$type = Yii::t('blocs', 'Image au format {format}', array('{format}'=>'JPG'));
				break;
			case "image/png";
				$type = Yii::t('blocs', 'Image au format {format}', array('{format}'=>'PNG'));
				break;
			case "image/bmp";
				$type = Yii::t('blocs', 'Image au format {format}', array('{format}'=>'BMP'));
				break;
			case "application/pdf";
				$type = Yii::t('blocs', 'Document {format}', array('{format}'=>'PDF'));
				break;
			case "application/vnd.ms-excel";
				$type = Yii::t('blocs', 'Document {format}', array('{format}'=>'Microsoft Excel'));
				break;
			case "application/msword";
				$type = Yii::t('blocs', 'Document {format}', array('{format}'=>'Microsoft Word'));
				break;
			default:
				$type = Yii::t('blocs', 'Document {format}', array('{format}'=>''));
				break;
   	 	}
   	 	
   	 	return $type;
    }
}
<?php
/**
 * Manages a newsletter
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Other
 */

class Newsletter extends CComponent
{
	/**
	 * The language (abbreviated, fr, en...) of the newsletter.
	 */
	public $language;
	

	/**
	 * Render a newsletter
	 * @return string the newsletter.
	 */
	public function make()
	{
		$module = Yii::app()->controller->module;
		
		require_once('protected/vendors/simplepie/autoloader.php');
		require_once('protected/vendors/simplepie/idn/idna_convert.class.php');
		
		$timeLimit = KeyValue::model()->findByPk('newsletter_execution_time')->value;

 		$simplePie = new SimplePie();
 		
 		$simplePie->set_cache_location('./protected/cache/simplepie');
		$simplePie->set_cache_duration(1);		// 1 seconde
 		
        // This makes sure that the content is sent to the browser as 
        // text/html and the UTF-8 character set (since we didn't change it).
        $simplePie->handle_content_type();
		
 		if ($module->multiLang)
 		{
	 		if (isset($this->language))
	 		{
	 			$feeds = $module->feeds[$this->language];
	        	$renderLanguage = $this->language;
	 		}
	        else
	        {
	        	$feeds = $module->feeds[Yii::app()->language];
	        	$renderLanguage = Yii::app()->language;
	        }
 		}
 		else
 		{
 			$feeds = $module->feeds;
 			$renderLanguage = Yii::app()->language;
 		}
		
 		$atLeastOne = false;
        for ($i = 0; $i < count($feeds); $i++)
        {
        	if (isset($feeds[$i]['expression']))
        	{
        		$feeds[$i]['content'] = $this->evaluateExpression($feeds[$i]['expression'],array('timeLimit'=>$timeLimit, 'language'=>$renderLanguage));
        		
        		if ($feeds[$i]['content'] != '')
        			$atLeastOne = true;
        	}
        	else
        	{
        		$simplePie->set_feed_url($feeds[$i]['url']);
        		$simplePie->init();
        		
        		$feeds[$i]['link'] = $simplePie->get_permalink();
        		
        	    $feeds[$i]['items'] = array();
		        foreach ($simplePie->get_items(0, $feeds[$i]['limit']) as $item)
		        {
		        	if ($item->get_date('U') > strtotime($timeLimit))
		        	{
		        		$feeds[$i]['items'][] = $item;
		        		$atLeastOne = true;
		        	}
		        }
        	}
        }
		
        if ($atLeastOne)
        	return Yii::app()->controller->renderPartial('newsletter.components.views.newsletter', array('feeds'=>$feeds, 'language'=>$renderLanguage), true);
        else
        	return false;
	}
}
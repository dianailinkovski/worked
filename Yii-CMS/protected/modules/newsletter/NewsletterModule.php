<?php
/**
 * Newsletter module
 *
 * Manages newsletters, the module delivers newsletters in the form of an API
 * and you must have a cron script somewhere calling it and deliver the newsletters. 
 * An example script is in the "examples" folder.
 *
 * Subscriptions can be registered with the subscription form widget.
 *
 * The module pulls its information from RSS feeds of other modules or from a custom function. 
 * You must configure the module to say where to pull the information from.
 *
 * Here is a non-multilang example:
 * 
 * 		'multiLang' => false,
 * 		'apiKey' => 'vNgtq7DCkJkxXcEbPeWGf7oZcgDOHl5E',
 * 		'emailSubject' => 'SiteName.com - Newsletter',
 * 		'feeds' => array(
 * 			array(
 * 				'url' => 'http://domain.com/news/default/rss',
 * 				'title' => 'News',
 * 				'limit' => 10,
 * 			),
 * 			array(
 * 				'url' => 'http://domain.com/event/default/rss',
 * 				'title' => 'Events',
 * 				'limit' => 10,
 * 			),
 * 		)
 *
 * Here is a multilang example:
 *
 * 		'multiLang' => true,
 * 		'apiKey' => 'vNgtq7DCkJkxXcEbPeWGf7oZcgDOHl5E', 
 * 		'emailSubject' => array(
 * 			'fr' => 'Infolettre',
 * 			'en' => 'Newsletter',
 * 		),
 * 		'feeds' => array(
 * 			'fr' => array(
 * 				array(
 * 					'url' => 'http://domain.com/fr/news/default/rss',
 * 					'title' => 'Nouvelles',
 * 					'limit' => 5,
 * 				),
 * 				array(
 * 					'expression' => array('NewsletterItem', 'run'),
 * 					'title' => 'Produit du mois',
 * 				)
 * 			),
 * 			'en' => array(
 * 				array(
 * 					'url' => 'http://domain.com/en/news/default/rss',
 * 					'title' => 'News',
 * 					'limit' => 5,
 * 				),
 * 				array(
 * 					'expression' => array('NewsletterItem', 'run'),
 * 					'title' => 'Product of the month',
 * 				)
 * 			),
 * 		)
 *
 * In this example, the second newsletter item calls the "run" function inside the "NewsletterItem" component. (@see CComponent::evaluateExpression())
 * This function has the following signature: ($timeLimit, $language) where timeLimit is a YYYY-MM-DD timestamp
 * and language is the language abbreviation (fr, en...).
 *
 * The module uses the "key_value" table to store the latest execution time of the newsletter.
 * 			
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Module
 */

class NewsletterModule extends CmsModule
{
	public $feeds;
	
	public $limit;
	
	public $multiLang;
	
	public $emailSubject;
	
	public $apiKey;


	public function init()
	{
		$this->setImport(array(
			'newsletter.models.*',
			'newsletter.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
	
	public static function getAdminMenu()
	{
		return array(
			array('label'=>'{sectionName}', 'url'=>array('/newsletter/admin/admin'), 'icon'=>'edit', 'subMenu' => array(
				array('label'=>'Infolettre', 'url'=>array('/newsletter/admin/admin')),
				array('label'=>'Inscriptions', 'url'=>array('/newsletter/adminsub/admin')),
			))
		);
	}
}

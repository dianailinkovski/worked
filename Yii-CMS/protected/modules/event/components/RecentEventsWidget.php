<?php
Yii::import('event.models.*');

/**
 * List recent events.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class RecentEventsWidget extends CWidget
{
	/**
	 * @var int number of entries to show. Defaults to 5.
	 */
	public $maxNbrEntries=5;

	/**
	 * @var int the CmsSection id of this module if you want to restrict to only one section.
	 * @see CmsSection
	 */
	public $sectionId;


    public function run()
    {
		Yii::app()->clientScript->registerCssFile(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('event.assets'), false, -1, YII_DEBUG).'/css/event.css');
	
		if ($this->sectionId === null)
		{
			$events = Event::model()->findAll(array(
				'order' => 'date_start ASC, date_end ASC',
				'limit' => $this->maxNbrEntries,
			));
		}
		else {
			$events = Event::model()->findAll(array(
				'order' => 'date_start ASC, date_end ASC',
        		'condition' => 'section_id = '.$this->sectionId,
				'limit' => $this->maxNbrEntries,
			));
		}
		
        $this->render('recentEventsWidget', array(
        	'events'=>$events,
        ));
    }
}
?>
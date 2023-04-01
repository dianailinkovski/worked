<?php
Yii::import('event.models.*');

/**
 * Show an interactive calendar showing the events.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class EventsCalendarWidget extends CWidget
{
	/**
	 * @var int the CmsSection id of this module if you want to restrict to only one section.
	 * @see CmsSection
	 */
	public $sectionId;
	

    public function run()
    {
    	if ($this->sectionId !== null && isset($_POST['eventsCalendarWidgetDate']) && (!isset($_POST['eventsCalendarWidgetSectionId']) || $_POST['eventsCalendarWidgetSectionId'] != $this->sectionId))
    		return;
    		
		Yii::app()->clientScript->registerCssFile(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('event.assets'), false, -1, YII_DEBUG).'/css/events-calendar-widget.css');

		if (Yii::app()->request->isAjaxRequest && isset($_POST['eventsCalendarWidgetDate']))
	    	$date = getdate(strtotime($_POST['eventsCalendarWidgetDate']));
	    else
	    	$date = getdate();
	    
	    $dateSqlStart = $date['year'].'-'.str_pad($date['mon'], 2, '0', STR_PAD_LEFT).'-01';
	    $nextMonth = getDate(mktime(0, 0, 0, $date['mon'] + 1, 1, $date['year']));
	    $dateSqlEnd = $date['year'].'-'.str_pad($nextMonth['mon'], 2, '0', STR_PAD_LEFT).'-01';
	    
	    if ($this->sectionId !== null)
	    	$eventModels = Event::model()->findAll(array(
				'condition' => 't.date_start < :dateend AND t.date_end >= :datestart AND t.section_id = :sectionId',
        		'params' => array(':datestart'=>$dateSqlStart, ':dateend'=>$dateSqlEnd, ':sectionId'=>$this->sectionId), 
			));
	   	else
	    	$eventModels = Event::model()->findAll(array(
				'condition' => 't.date_start < :dateend AND t.date_end >= :datestart',
        		'params' => array(':datestart'=>$dateSqlStart, ':dateend'=>$dateSqlEnd), 
			));

	   	$events = array();
	   	foreach ($eventModels as $eventModel)
	   	{
	   		$monthStart = substr($eventModel->date_start, 5, 2);
	   		$yearStart = substr($eventModel->date_start, 0, 4);
	   		$dayStart = substr($eventModel->date_start, 8, 2);
	   		$dayEnd = substr($eventModel->date_end, 8, 2);
	   		$monthEnd = substr($eventModel->date_end, 5, 2);
	   		$yearEnd = substr($eventModel->date_end, 0, 4);
			
	   		if ($monthStart == $monthEnd && $yearStart == $yearEnd)
            	$dayEnd = substr($eventModel->date_end, 8, 2);
	   		elseif (($monthStart < $monthEnd || $yearStart < $yearEnd) && ($date['mon'] == $monthEnd && $date['year'] == $yearEnd))
				$dayStart = 1;
	   		elseif (($monthStart < $monthEnd || $yearStart < $yearEnd) && ($date['mon'] == $monthStart && $date['year'] == $yearStart))
				$dayEnd = 31;
	   		else {
	   			$dayStart = 1;
	   			$dayEnd = 31;
	   		}
	   		
	   		for ($i = (int)$dayStart; $i <= $dayEnd; $i++)
	   		{
	   			if (!isset($events[$i]))
	   				$events[$i] = array();
	   			$events[$i][] = $eventModel;
	   		}
	   	}
	   	
        $render = $this->render('eventsCalendarWidget', array(
        	'events'=>$events,
        	'date'=>$date,
        ), true);
        
        if (Yii::app()->request->isAjaxRequest)
    	{
    		echo "\n".'<div id="events-calendar-widget-render">'."\n";
    		echo $render;
    		echo "\n".'</div>'."\n";

    		Yii::app()->end();
    	}
    	else 
    		echo $render;
    }
}
?>
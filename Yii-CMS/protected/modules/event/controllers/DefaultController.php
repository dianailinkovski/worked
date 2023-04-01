<?php

class DefaultController extends Controller
{
	public $layout='//layouts/column1';
	
	public function init()
	{
		Yii::app()->clientScript->registerCssFile($this->module->assetsUrl.'/css/event.css');

		return parent::init();
	}
	
	public function actionIndex($archives=null, $page=null)
	{
		if ($archives === null) 
		{
			$condition = "date_end >= :dateNow";
			$order = "date_start ASC, date_end ASC";
		}
		else {
			$condition = "date_end < :dateNow";
			$order = "'date_end DESC'";
		}
		
		$eventProvider = new CActiveDataProvider('Event', array(
			'criteria' => array(
				'order' => $order,
				'condition' => $condition." AND section_id = ".Yii::app()->cms->currentSectionId,
				'params' => array(':dateNow'=>date('Y-m-d H:i:s')),
			),
			'pagination' => array(
				'pageSize' => 10
			)
		));

		if (isset($page))
		{
			$page = (int)$page - 1;
			$eventProvider->pagination->currentPage = $page;
		}
		
		$languageManager = Yii::app()->languageManager;
		$languageManager->translatedGetVars['archives'] = array();
		foreach (array_keys($languageManager->languages) as $language)
		{
			$languageManager->translatedGetVars['archives'][$language] = $this->module->archivesVarName[$language];
		}
			
		$this->render('index', array(
			'eventProvider'=>$eventProvider,
			'archives'=>$archives,
		));
	}
	
	
	public function actionDetail($n, $archives=null)
	{
		if (!($event = Event::model()->find('i18nEvent.l_title_url=:n AND section_id = '.Yii::app()->cms->currentSectionId, array('n'=>$n))))
			throw new CHttpException(404,'The requested page does not exist.');
			
		$eventMultilang = Event::model()->multilang()->find('i18nEvent.l_title_url=:n AND section_id = '.Yii::app()->cms->currentSectionId, array('n'=>$n));
		
		$languageManager = Yii::app()->languageManager;
		$languageManager->translatedGetVars['archives'] = array();
		$languageManager->translatedGetVars['n'] = array();
		foreach (array_keys($languageManager->languages) as $language)
		{
			$languageManager->translatedGetVars['n'][$language] = $eventMultilang->{'title_url_'.$language};
			$languageManager->translatedGetVars['archives'][$language] = $this->module->archivesVarName[$language];
		}
		
		$this->render('detail', array(
			'event'=>$event,
			'archives'=>$archives,
		));
	}
	
	
	protected function afterRender($view, &$output)
	{
		parent::afterRender($view,$output);
		//Yii::app()->facebook->addJsCallback($js); // use this if you are registering any $js code you want to run asyc
		Yii::app()->facebook->initJs($output); // this initializes the Facebook JS SDK on all pages
		Yii::app()->facebook->renderOGMetaTags(); // this renders the OG tags
		return true;
	}
	
	
	public function actionRss()
	{
		// disabling web log
	    foreach (Yii::app()->log->routes as $route)
        {
            if ($route instanceof CWebLogRoute)
                $route->enabled = false;
        }

		Yii::import('ext.feed.*');

		$feed = new EFeed();
		 
		$feed->title = Yii::app()->name.' | '.Yii::t('eventModule.common', 'Événements');
		$feed->description = Yii::app()->name.' | '.Yii::t('eventModule.common', 'meta_description');

		$feed->addChannelTag('language', Yii::app()->language);
		$feed->addChannelTag('pubDate', date(DATE_RSS, time()));
		$feed->addChannelTag('link', $this->createAbsoluteUrl('index'));
		
		if (($events = Event::model()->findAll(array('order'=>'date_start ASC, date_end ASC', 'limit'=>25, 'condition'=>"date_end >= '".date('Y-m-d H:i:s')."' AND section_id = ".Yii::app()->cms->currentSectionId))))
		{
			foreach ($events as $event)
			{
				$item = $feed->createNewItem();
				 
				$item->title = $event->title;
				$item->link = $this->createAbsoluteUrl('detail', array('n'=>$event->title_url));
				$item->date = $event->date_created;
				
				if (!empty($event->image))
					$item->description = '<div style="margin-bottom: 1em;"><img src="'.Yii::app()->request->hostInfo.Yii::app()->request->baseUrl.'/'.$event->imageHandler->dir.'/'.Helper::encodeFileName(Helper::fileSuffix($event->image, 's')).'" alt="'.CHtml::encode($event->title).'" /></div><div>'.CHtml::encode($event->summary).'</div>';
				else
					$item->description = CHtml::encode($event->summary);
				
				$feed->addItem($item);
			}
		}
		$feed->generateFeed();
		Yii::app()->end();
	}
}

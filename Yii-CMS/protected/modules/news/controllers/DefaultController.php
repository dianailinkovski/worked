<?php

class DefaultController extends Controller
{
	public $layout='//layouts/column1';
	
	public function init()
	{
		Yii::app()->clientScript->registerCssFile($this->module->assetsUrl.'/css/news.css');

		return parent::init();
	}
	
	public function actionIndex($page=null)
	{
		$newsProvider = new CActiveDataProvider('News', array(
			'criteria' => array(
				'order' => 'date DESC',
				'condition' => "date <= '".date('Y-m-d H:i:s')."' AND section_id = ".Yii::app()->cms->currentSectionId,
			),
			'pagination' => array(
				'pageSize' => 10
			)
		));
		
		if (isset($page))
		{
			$page = (int)$page - 1;
			$newsProvider->pagination->currentPage = $page;
		}
			
		$this->render('index', array(
			'newsProvider'=>$newsProvider,
		));
	}
	
	
	public function actionDetail($n)
	{
		if (!($news = News::model()->find('i18nNews.l_title_url=:n AND section_id = '.Yii::app()->cms->currentSectionId, array('n'=>$n))))
			throw new CHttpException(404,'The requested page does not exist.');
			
		$newsMultilang = News::model()->multilang()->find('i18nNews.l_title_url=:n AND section_id = '.Yii::app()->cms->currentSectionId, array('n'=>$n));
		
		Yii::app()->languageManager->translatedGetVars['n'] = array();
		foreach (array_keys(Yii::app()->languageManager->languages) as $language)
		{
			Yii::app()->languageManager->translatedGetVars['n'][$language] = $newsMultilang->{'title_url_'.$language};
		}
		
		$this->render('detail', array(
			'news'=>$news,
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
		 
		$feed->title = Yii::app()->name.' | '.Yii::t('newsModule.common', 'Nouvelles');
		$feed->description = Yii::app()->name.' | '.Yii::t('newsModule.common', 'Nouvelles');

		$feed->addChannelTag('language', Yii::app()->language);
		$feed->addChannelTag('pubDate', date(DATE_RSS, time()));
		$feed->addChannelTag('link', $this->createAbsoluteUrl('index'));
		
		if (($news = News::model()->findAll(array('order'=>'date DESC', 'limit'=>25, 'condition'=>"date <= '".date('Y-m-d H:i:s')."' AND section_id = ".Yii::app()->cms->currentSectionId))))
		{
			foreach ($news as $new)
			{
				$item = $feed->createNewItem();
				 
				$item->title = $new->title;
				$item->link = $this->createAbsoluteUrl('detail', array('n'=>$new->title_url));
				$item->date = $new->date;
				
				if (!empty($new->image))
					$item->description = '<div style="margin-bottom: 1em;"><img src="'.Yii::app()->request->hostInfo.Yii::app()->request->baseUrl.'/'.$new->imageHandler->dir.'/'.Helper::encodeFileName(Helper::fileSuffix($new->image, 's')).'" alt="'.CHtml::encode($new->title).'" /></div><div>'.CHtml::encode($new->summary).'</div>';
				else
					$item->description = CHtml::encode($new->summary);
				
				$feed->addItem($item);
			}
		}
		$feed->generateFeed();
		Yii::app()->end();
	}
}
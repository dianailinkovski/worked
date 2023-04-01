<?php
class ApiController extends CController
{
	public $layout=false;

	protected function beforeAction($action)
	{
		if (isset($_GET['key']) && $_GET['key'] != $this->module->apiKey)
			throw new CHttpException(400, 'Bad key.');
			
        foreach (Yii::app()->log->routes as $route)
        {
            if ($route instanceof CWebLogRoute)
            {
            	$route->enabled = false;
            }
        }
        return true;
	}

	public function actionGetnewsletter($key, $language)
	{
		$newsletter = new Newsletter;
		$newsletter->language = $language;
		
		$this->render('getnewsletter', array('newsletter'=>$newsletter->make()));
		
		Yii::app()->end();
	}
	
	public function actionGetsubs($key, $limit, $offset)
	{
		$subscriptions = NewsletterSubscription::model()->findAll(array('limit'=>$limit, 'offset'=>$offset, 'order'=>'id ASC'));
		
		$this->render('getsubs', array('subs'=>$subscriptions));

		Yii::app()->end();
	}
	
	public function actionCheck($key)
	{
		$executionTime = KeyValue::model()->findByPk('newsletter_execution_time');
		$frequency = KeyValue::model()->findByPk('newsletter_frequency');
		$newsletter = new Newsletter;

		if (time() >= strtotime($executionTime->value) + $frequency->value && $frequency->value != '1' && $newsletter->make() !== false)
			$this->render('check', array('send'=>'true'));
		else
			$this->render('check', array('send'=>'false'));

		Yii::app()->end();
	}
	
	public function actionUpdate($key)
	{
		$executionTime = KeyValue::model()->findByPk('newsletter_execution_time');
		$frequency = KeyValue::model()->findByPk('newsletter_frequency');

		$executionTime->value = date('Y-m-d H:i:s');
		$executionTime->save();

		if ($frequency->value < 0)
		{
			$frequency->value = abs($frequency->value);
			$frequency->save();
		}

		Yii::app()->end();
	}
}

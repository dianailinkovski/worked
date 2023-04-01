<?php
class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	public function actionIndex()
	{
		Yii::app()->clientScript->registerCssFile('/css/blocs.css');
		$this->render('index');
	}
	
	public function actionError()
	{
		$this->layout=false;

	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
				$this->render('error', $error);
	    }
	}
	
	public function actionGetdropboxlink($path)
	{
		$path = base64_decode($path);

		$pathError = Dropbox\Path::findError($path);
		if ($pathError !== null) {
		    throw new CHttpException(500, "Invalid <dropbox-path>: $pathError\n");
		}
		
		$client = new Dropbox\Client(Yii::app()->params['dropboxToken'], 'dropbox-client');
		
		$link = $client->createTemporaryDirectLink($path);
		
		echo CJSON::encode($link);
		
		Yii::app()->end();
	}
	
	/*
	public function actionLogin()
	{
		$this->render('login');
	}

	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
	public function actionRedirect($path)
	{
		$this->redirect(urldecode($path));
		
		Yii::app()->end();
	}
	*/
}
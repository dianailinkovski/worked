<?php
class AdminsubController extends BackController
{
	public $layout='//adminLayouts/column1';
	
	public $sectionLabel;
	
	public $modelName;
	
	public function init()
	{
		$this->sectionLabel = Yii::t('newsletterModule.admin', 'Subscriptions');
		$this->modelName = 'NewsletterSubscription';
		return parent::init();
	}

	public function actionAdmin()
	{
		$model=new $this->modelName('search');
		$model->unsetAttributes();
		
		if(isset($_GET[$this->modelName]))
			$model->attributes=$_GET[$this->modelName];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
}

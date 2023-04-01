<?php
class AdminController extends BackController
{
	public $layout='//adminLayouts/column1';
	
	public $sectionLabel;

	public function init()
	{
		$this->sectionLabel = Yii::t('newsletterModule.admin', 'Newsletter');
		return parent::init();
	}

	public function actionAdmin()
	{
		$model = new AdminForm;
		
		$frequency = KeyValue::model()->findByPk('newsletter_frequency');
		
		$model->frequency = abs($frequency->value);
		
		if(isset($_POST['AdminForm']))
		{
			$model->attributes=$_POST['AdminForm'];
			
			if ($model->validate())
			{
				if(isset($_POST['yt1']) || $frequency->value < 0)
					$frequency->value = -$model->frequency;
				else
					$frequency->value = $model->frequency;
					
				$frequency->save();
				
				$this->redirect('admin');
			}
		}
		
		$newsletter = new Newsletter;

		$this->render('admin',array(
			'newsletter'=>$newsletter->make(),
			'model'=>$model,
		));
	}
}

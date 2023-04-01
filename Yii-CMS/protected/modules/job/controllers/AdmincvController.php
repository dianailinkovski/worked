<?php

class AdmincvController extends BackController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//adminLayouts/column1';
	public $sectionLabel;
	public $sectionParentLabel;
	
	public function init()
	{
		$this->sectionLabel = Yii::t('jobModule.admin', 'CV');
		$this->sectionParentLabel = Yii::t('jobModule.admin', 'Emploi');
		return parent::init();
	}
	
	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new JobCv('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['JobCv']))
			$model->attributes=$_GET['JobCv'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	public function actionRead($id)
	{
		$cv = JobCv::model()->findByPk($id);
				
		$this->render('read',array(
			'modelCv'=>$cv,
		));
	}
}

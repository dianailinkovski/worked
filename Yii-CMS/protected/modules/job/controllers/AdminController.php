<?php
class AdminController extends BackController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//adminLayouts/column2';
	public $sectionLabel;
	
	public function init()
	{
		$this->sectionLabel = Yii::t('jobModule.admin', 'Emploi');
		return parent::init();
	}
	
	public function actions()
	{
		$formSettings = array(
			'redirect'=>$this->createUrl('admin'),
			'forms' => array(
				'id'=>'mainForm',
				'varName'=>'job',
				'models'=>'Job',
				'forms' => array(
					'id'=>'blocsForm',
					'blocs' => 'job',
					'varName'=>'blocs',
				),
			),
		);

		return array(
			'create'=>array(
				'class' => 'application.components.actions.Create',
				'formSettings' => $formSettings,
			),
			'update'=>array(
				'class' => 'application.components.actions.Update',
				'formSettings' => $formSettings,
			),
			'delete'=>array(
				'class' => 'application.components.actions.Delete',
				'modelClass' => 'Job',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'Job',
			),
		);
	}
}
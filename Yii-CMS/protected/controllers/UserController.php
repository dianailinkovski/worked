<?php
class UserController extends BackController
{
	public $layout='//adminLayouts/withMenu';
	
	public $sectionLabel;
	
	public function init() 
	{
		if (!Yii::app()->userBack->checkAccess('adminUsers'))
			Yii::app()->user->loginRequired();

		$this->sectionLabel = 'Administrateur';

		return parent::init();
	}

	public function actions()
	{
		$formSettings = array(
			'redirect'=>$this->createUrl('admin'),
			'forms' => array(
				'id'=>'mainForm',
				'varName'=>'user',
				'models'=>'User',
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
				'modelClass' => 'User',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'User',
			),
		);
	}
}

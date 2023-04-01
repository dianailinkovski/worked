<?php
class AdminController extends BackController
{
	public $layout='//adminLayouts/column2';
	
	public $sectionLabel;
	
	public function init()
	{
		$this->sectionLabel = 'Message';
		return parent::init();
	}
	
	public function actions()
	{
		$formSettings = array(
			'id'=>'mainForm',
			'models'=>'Message',
			'redirect'=>$this->createUrl('admin')
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
				'modelClass' => 'Message',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'Message',
			),
		);
	}
}

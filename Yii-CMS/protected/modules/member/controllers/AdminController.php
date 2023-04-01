<?php
class AdminController extends BackController
{
	public $layout='//adminLayouts/withMenu';
	
	public $sectionLabel;
	
	
	public function init()
	{
		$this->sectionLabel = 'Membre';
		return parent::init();
	}

	public function actions()
	{
		$formSettings = array(
			'redirect'=>$this->createUrl('admin'),
			'forms' => array(
				'id'=>'mainForm',
				'varName'=>'member',
				'models'=>'Member',
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
				'onAfterProcessFormSettings' => function($event)
				{
					$model = $event->sender->formSettings['forms']['models'];
					$model->password = '';
				},
			),
			'delete'=>array(
				'class' => 'application.components.actions.Delete',
				'modelClass' => 'Member',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'Member',
			),
		);
	}
}

<?php
class AdminController extends BackController
{
	public $layout='//adminLayouts/column2';
	
	public $sectionLabel;
	
	public function init()
	{
		$this->sectionLabel = Yii::t('eventModule.admin', 'Events');
		return parent::init();
	}
	
	public function actions()
	{
		if (!isset($_GET['section_id']))
			throw new CHttpException(400);
		else
			$sectionId = (int)$_GET['section_id'];

		$formSettings = array(
			'redirect'=>$this->createUrl('admin', array('section_id'=>$sectionId)),
			'forms' => array(
				'id'=>'mainForm',
				'varName'=>'event',
				'models'=>'Event',
				'onAfterSetAttributes' => function($event) use ($sectionId)
				{
					$model = $event->params['model'];
					$model->section_id = $sectionId;
				},
				'forms' => array(
					'id'=>'blocsForm',
					'blocs' => 'event',
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
				'modelClass' => 'Event',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'Event',
			),
		);
	}
}

<?php
class AdminController extends BackController
{
	public $layout='//adminLayouts/withMenu';
	
	public $sectionLabel;
	
	public function init()
	{
		$this->sectionLabel = Yii::t('newsModule.admin', 'News');
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
				'varName'=>'news',
				'models'=>'News',
				'onAfterSetAttributes' => function($event) use ($sectionId)
				{
					$model = $event->params['model'];
					
					if ($model->source_url == 'http://')
						$model->source_url = '';
					
					$model->section_id = $sectionId;
				},
				'onInvalid' => function($event)
				{
					$model = $event->params['model'];
					
					if ($model->source_url == '')
						$model->source_url = 'http://';
				},
				'forms' => array(
					'id'=>'blocsForm',
					'blocs' => 'news',
					'varName'=>'blocs',
				),
			),
		);

		return array(
			'create'=>array(
				'class' => 'application.components.actions.Create',
				'formSettings' => $formSettings,
				'onAfterProcessFormSettings' => function($event)
				{
					$form = $event->params['form'];
					
					if ($form['id'] == 'mainForm')
					{
						$model = $form['models'];
						$model->date = date('Y-m-d H:i:s');
						$model->source_url = 'http://';
					}
				},
			),
			'update'=>array(
				'class' => 'application.components.actions.Update',
				'formSettings' => $formSettings,
			),
			'delete'=>array(
				'class' => 'application.components.actions.Delete',
				'modelClass' => 'News',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'News',
			),
		);
	}
}

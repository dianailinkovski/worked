<?php
class AdminController extends BackController
{
	public $layout='//adminLayouts/column2';
	
	public $sectionLabel;
	
	
	public function init()
	{
		$this->sectionLabel = Yii::t('bannerModule.admin', 'Bannière');
		return parent::init();
	}
	
	public function actions()
	{
		$formSettings = array(
			'id'=>'mainForm',
			'models'=>'Banner',
			'redirect'=>$this->createUrl('admin')
		);
		$formEvents = array(
			'onAfterSetAttributes' => function($event)
			{
				$form = $event->params['form'];
				
				if ($form['id'] == 'mainForm')
				{
					$model = $event->params['model'];
					
					if (substr($model->color, 0, 1) == '#')
						$model->color = substr($model->color, 1);
				}
			},
			'onBeforeSave' => function($event)
			{
				$form = $event->params['form'];
				
				if ($form['id'] == 'mainForm')
				{
					$model = $event->params['model'];
					
					if ($model->active == '1')
						Yii::app()->db->createCommand('UPDATE banner SET active=0')->execute();
				}
			},
		);
		return array(
			'create'=>array(
				'class' => 'application.components.actions.Create',
				'formSettings' => $formSettings,
				'formEvents' => $formEvents,
			),
			'update'=>array(
				'class' => 'application.components.actions.Update',
				'formSettings' => $formSettings,
				'formEvents' => $formEvents,
			),
			'delete'=>array(
				'class' => 'application.components.actions.Delete',
				'modelClass' => 'Banner',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'Banner',
			),
		);
	}
}

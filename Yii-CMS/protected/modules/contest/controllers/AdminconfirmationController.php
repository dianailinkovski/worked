<?php
class AdminconfirmationController extends BackController
{
	public $layout='//adminLayouts/column2';
	
	public $sectionLabel;
	
	public function init()
	{
		$this->sectionLabel = 'Concours/Sondage page de confirmation';

		return parent::init();
	}
	
	public function beforeRender($action)
	{
		if ($action == 'update')
		{
			$id = (int)$_GET['id'];
			
			$this->tabs = array(
				array(
					'label'=>'Général',
					'url'=>array('admin/update', 'id'=>$id),
					'controller'=>'admin',
				),
				array(
					'label'=>'Description',
					'url'=>array('adminintroduction/update', 'id'=>$id),
					'controller'=>'adminintroduction',
				),
				array(
					'label'=>'Formulaire',
					'url'=>array('adminform/update', 'id'=>$id),
					'controller'=>'adminform',
				),
				array(
					'label'=>'Page confirmation',
					'url'=>array('adminconfirmation/update', 'id'=>$id),
					'controller'=>'adminconfirmation',
				),
				array(
					'label'=>'Page conclusion',
					'url'=>array('adminconclusion/update', 'id'=>$id),
					'controller'=>'adminconclusion',
				),
				array(
					'label'=>'Résultats',
					'url'=>array('adminresults/admin', 'id'=>$id),
					'controller'=>'adminresults',
				),
			);
		}
		return parent::beforeRender($action);	
	}
	
	public function actions()
	{
		$formSettings = array(
			'id'=>'mainForm',
			'models'=>'Contest',
			'postConditionKey'=>'',
			'subForms' => array(
				array(
					'id'=>'blocsForm',
					'blocs' => 'contest_confirmation',
				),
			),
			'redirect'=>$this->createUrl('update', array('id'=>(int)$_GET['id']))
		);
		$formEvents = array(
			'onAfterSetAttributes' => function($event)
			{
				$form = $event->params['form'];
				
				if ($form['id'] == 'mainForm')
				{
					$model = $event->params['model'];
					$model->image = '';
				}
			},
		);
		return array(
			'update'=>array(
				'class' => 'application.components.actions.Update',
				'formSettings' => $formSettings,
				'formEvents' => $formEvents,
			),
		);
	}
}

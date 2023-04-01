<?php
class AdminformController extends BackController
{
	public $layout='//adminLayouts/column2';
	
	public $sectionLabel;
	
	public $typeArr = array(
			'title'=>'Titre de groupe de champs', 
			'text'=>'Champ texte', 'email'=>'Courriel', 
			'textarea'=>'Boite de texte', 
			'radio'=>'Boutons radio', 
			'checkbox'=>'Cases à cocher (choix multiples)', 
			'file'=>'Fichier'
	);
	
	public function init()
	{
		$this->sectionLabel = 'Concours/Sondage formulaire';

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
			'subForms'=>array(
				array(
					'id' => 'fieldForm',
					'models' => 'ContestField',
					'tabularId' => 'fieldsTabular',
					'parentIdAttribute' => 'contest_id',
					'subForms'=>array(
						array(
							'id' => 'multiForm',
							'models' => 'ContestFieldMulti',
							'tabularId' => '{widgetId}multiTabular{id}',
							'parentIdAttribute' => 'contest_field_id',
						),
					),
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

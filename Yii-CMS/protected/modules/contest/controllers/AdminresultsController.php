<?php
class AdminresultsController extends BackController
{
	public $layout='//adminLayouts/column2';
	
	public $sectionLabel;
	
	public $fields=array();
	
	
	public function init()
	{
		$this->sectionLabel = 'Concours/Sondage page de résultats';

		return parent::init();
	}
	
	public function actions()
	{
		return array(
			'delete'=>array(
				'class' => 'application.components.actions.Delete',
				'modelClass' => 'ContestEntry',
			),
		);
	}
	
	public function beforeRender($action)
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
		
		return parent::beforeRender($action);	
	}
	
	public function actionAdmin($id)
	{
		$model = new ContestEntry;
		$model->contest_id = $id;
		
		if (!($contest = Contest::model()->findByPk($id)))
			throw new CHttpException(404);

		$fieldsValidators = '';
		$i = 0;
		foreach (ContestField::model()->with(array('multi'=>array('index'=>'id')))->findAllByAttributes(array('contest_id'=>$id)) as $field)
		{
			if ($field->result && $field->type != 'title')
			{
				$this->fields[] = array('title'=>$field->title, 'type'=>$field->type, 'multi'=>$field->multi, 'id'=>$field->id);
				$fieldsValidators .= 'field_'.$i.', ';
				
				if (isset($_GET['ContestEntry']))
				{
					$model->{'field_'.$i} = $_GET['ContestEntry']['field_'.$i];
				}
				$i++;
			}
		}
		$model->fields = $this->fields;
		$validators = $model->getValidatorList();
		$validators->add(CValidator::createValidator('safe', $this, substr($fieldsValidators, 0, -2)));
		
		if (isset($_GET['ContestEntry']))
		{
			$model->id = $_GET['ContestEntry']['id'];
			$model->created_at = $_GET['ContestEntry']['created_at'];
		}

		$this->render('admin',array(
			'model'=>$model,
			'contest'=>$contest,
		));
	}
	
	public function actionView($id, $view_id)
	{
		if (!($model=ContestEntry::model()->with(array('contest', 'contest.fields', 'items', 'contest.fields.multi'=>array('index'=>'id')))->findByPk($view_id)))
			throw new CHttpException(404, 'The requested page does not exist.');

		$this->render('view',array(
			'model'=>$model,
		));
	}
	
	public function actionStats($id)
	{
		if (!($model=Contest::model()->with(array('fields'))->findByPk($id)))
			throw new CHttpException(404, 'The requested page does not exist.');

		$this->render('stats',array(
			'model'=>$model,
		));
	}
	
	public function actionWinners($id)
	{
		$nbWinners = isset($_POST['nbWinners']) ? ((int)$_POST['nbWinners'] < 1 ? 1 : (int)$_POST['nbWinners']) : 1;
		
		$winners = Yii::app()->db->createCommand("SELECT id FROM contest_entry WHERE contest_id = :contest_id ORDER BY RAND() LIMIT ".$nbWinners)->queryAll(true, array(':contest_id'=>$id));
		
		$this->renderPartial('_winners',array(
			'winners'=>$winners,
		));
		
		Yii::app()->end();
	}
}

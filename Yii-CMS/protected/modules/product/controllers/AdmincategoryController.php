<?php
class AdmincategoryController extends BackController
{
	public $layout='//adminLayouts/column2';
	
	public $sectionLabel;
	
	public function init()
	{
		$this->sectionLabel = 'Catégories';
		return parent::init();
	}
	
	public function actions()
	{
		$formSettings = array(
			'redirect'=>$this->createUrl('admin'),
			'forms' => array(
				'id'=>'mainForm',
				'varName'=>'productCategory',
				'models'=>'ProductCategory',
				'onAfterSave' => function($event)
				{
					$model = $event->params['model'];

					if ($model->location != 'nochange')
					{
						$decode = CJSON::decode($_POST['ProductCategory']['location']);
						$to = ProductCategory::model()->findByPk((int)$decode['to']);
						$action = $decode['action'];
	
					    switch ($action) 
					    {
		                    case 'child':
		                        $model->moveAsLast($to);
		                        break;
		                    case 'before':
		                        if ($to->isRoot())
		                            $model->moveAsRoot();
		                        else
		                            $model->moveBefore($to);
		                        break;
		                    case 'after':
		                        if($to->isRoot())
		                            $model->moveAsRoot();
		                        else
		                            $model->moveAfter($to);
		                        break;
		                }
					}
				},
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
				'modelClass' => 'ProductCategory',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'ProductCategory',
			),
		);
	}
}

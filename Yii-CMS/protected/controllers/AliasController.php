<?php
class AliasController extends BackController
{
	public $layout='//adminLayouts/withMenu';
	
	public $sectionLabel;

	public function init() 
	{
		if (!Yii::app()->userBack->checkAccess('adminAlias'))
			Yii::app()->user->loginRequired();
		
		$this->sectionLabel = Yii::t('admin', 'Alias');

		return parent::init();
	}

	public function actions()
	{
		$formSettings = array(
			'redirect'=>$this->createUrl('admin'),
			'forms' => array(
				'id'=>'mainForm',
				'varName'=>'cmsAlias',
				'models'=>'CmsAlias',
				'onAfterSave' => function($event)
				{
					$model = $event->params['model'];

					if ($model->location != 'nochange')
					{
						$decode = CJSON::decode($_POST['CmsAlias']['location']);
						$to = CmsAlias::model()->findByPk((int)$decode['to']);
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
				'forms' => array(
					'id' => 'routesForm',
					'models' => 'CmsAliasRoute',
					'varName' => 'cmsAliasRoutes',
					'parentIdAttribute' => 'alias_id',
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
				'modelClass' => 'CmsAlias',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'CmsAlias',
			),
		);
	}
}

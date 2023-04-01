<?php
class AdminController extends BackController
{
	public $layout='//adminLayouts/column2';
	
	public function actions()
	{
		$formSettings = array(
			'redirect'=>$this->createUrl('admin'),
			'forms' => array(
				'id'=>'mainForm',
				'varName'=>'contentPage',
				'models'=>'ContentPage',
				'forms' => array(
					'id'=>'blocsForm',
					'blocs' => 'content',
					'varName'=>'blocs',
				),
				'onAfterSetAttributes' => function($event) 
				{
					if (Yii::app()->user->id != 'Admin-1' && isset($this->module->layouts)) // Temporary user condition before we set up admin types.
					{
						$model = $event->params['model'];
						
						if ($model->layout == '')
						{
							foreach ($this->module->layouts as $layoutName => $layoutTitle)
							{
								$model->layout = $layoutName;
								break;
							}
						}
					}
				},
				'onBeforeSave' => function($event) 
				{
					$model = $event->params['model'];

					if ($model->isNewRecord) 
					{
						$alias = new CmsAlias();
						foreach (Yii::app()->languageManager->languages as $language => $fullLanguage) 
						{
							if($language === Yii::app()->sourceLanguage) $suffix = '';
				    			else $suffix = '_'.$language;
				    			
							$alias->{'alias'.$suffix} = AdminHelper::generateUrlStr($model->{'title'.$suffix}, $alias, 'alias', null, $language);
							$alias->{'title'.$suffix} = $model->{'title'.$suffix};
						}
						$alias->section_id = CmsSection::model()->findByAttributes(array('module'=>'content'))->id;
						$alias->allow_children = 1;
						$alias->attributes = $_POST['CmsAlias'];
						$root = CmsAlias::model()->roots()->find();
						$alias->appendTo($root);
						$aliasRoute = new CmsAliasRoute();
						$aliasRoute->route = 'content';
						$aliasRoute->alias_id = $alias->primaryKey;
						$aliasRoute->save();
						$model->alias_id = $alias->primaryKey;
					} 
					else {
						$alias = CmsAlias::model()->multilang()->findByPk($model->alias->id);
						$alias->attributes = $_POST['CmsAlias'];
						
						foreach (Yii::app()->languageManager->languages as $language => $fullLanguage) 
						{
							if($language === Yii::app()->sourceLanguage) $suffix = '';
				    			else $suffix = '_'.$language;
				    			
							$alias->{'alias'.$suffix} = AdminHelper::generateUrlStr($model->{'title'.$suffix}, $alias, 'alias', $alias->id, $language);
							$alias->{'title'.$suffix} = $model->{'title'.$suffix};
						}
					}
					if ($alias->location != 'nochange')
					{
						$decode = CJSON::decode($_POST['CmsAlias']['location']);
						$to = CmsAlias::model()->findByPk((int)$decode['to']);
						$action = $decode['action'];
	
					    switch ($action) 
					    {
		                    case 'child':
		                        $alias->moveAsLast($to);
		                        break;
		                    case 'before':
		                        if ($to->isRoot())
		                            $alias->moveAsRoot();
		                        else
		                            $alias->moveBefore($to);
		                        break;
		                    case 'after':
		                        if($to->isRoot())
		                            $alias->moveAsRoot();
		                        else
		                            $alias->moveAfter($to);
		                        break;
		                }
					}
					else {
						$alias->saveNode();
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
				'modelClass' => 'ContentPage',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'ContentPage',
			),
		);
	}
}
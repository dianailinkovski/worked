<?php
/**
 * Base class for crud action components
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Action
 * 
 * @see AdminBlocsWidget
 */

class CrudAction extends CAction 
{
	/**
	 * @var array the form settings passed on to the form manager.
	 * You can add 2 new variables to your forms settings : "blocs" and "varName". 
	 * "blocs" must be a simple string (no special characters). This is if you want that form to be blocs it will generate the form settings. 
	 * The variable that contains the models that will be passed on to the view file will have that name. This name represents the "unique_id" in the "bloc" table.
	 * "varName" must be a simple string (no special characters). This is the variable that will passed on to your view containing the models of that form. 
	 * varName is required if it's not a blocs form.
	 * @see FormManager
	 * @see FormManagerForm
	 */
	public $formSettings;
	/**
	 * @var int the id variable in GET (for update).
	 */
	public $id;

	
	/**
	 * @var array used to remember some form information.
	 * Defaults to array().
	 */
	private $_formIds=array();
	/**
	 * @var string "create" or "update".
	 */
	private $_action;

	/**
	 * Logic for both create and update actions.
	 */
	protected function createUpdateCommon() 
	{
		$this->_action = get_class($this);

		// Recursively processing all forms.
		if (!isset($this->formSettings['forms']['id']))
		{
			foreach ($this->formSettings['forms'] as $key => $value)
			{
				$this->formSettings['forms'][$key] = $this->processFormSettingsForm($this->formSettings['forms'][$key]);
			}
		} 
		else
			$this->formSettings['forms'] = $this->processFormSettingsForm($this->formSettings['forms']);

		$formManager = new FormManager($this->formSettings);
		$formManager->process();

		// Render with form variables. Blocs type forms have a variable that is an array containing its sub-forms as well.
		$renderArray = array();
		foreach ($this->_formIds as $varName => $formIdArr)
		{
			if ($formIdArr[1] !== null)
			{
				$blocsForm = $formManager->getForm($formIdArr[0]);
				$renderArray[$varName] = array($formManager->getModels($formIdArr[0]));

				foreach ($blocsForm->forms as $blocsSubForm)
				{
					$renderArray[$varName][substr($blocsSubForm->id, 18, -4)] = $blocsSubForm->models;
				}
			}
			else
				$renderArray[$varName] = $formManager->getModels($formIdArr[0]);
		}

		$this->controller->render(lcfirst($this->_action), $renderArray);
	}
	
	/**
	 * Process and manipulate the settings of one form. This function is called recursively.
	 * 
	 * Generates forms for blocs and loads models given as string with actual models.
	 * 
	 * @param array $form the form settings to process. Passed by reference.
	 * @param null|CActiveRecord|array $parentModels the models of the parent form. Can be different types depending on the context. Defaults to null.
	 * 
	 * @return array the processed form settings.
	 */
	protected function processFormSettingsForm(&$form, $parentModels=null)
	{
		$this->onBeforeProcessFormSettings(new CEvent($this, array('parentModels'=>$parentModels, 'form'=>&$form, 'id'=>$this->id)));
		
		// If the blocs parameter is given, the form is generated here.
		$blocsUniqueId = null;
		if (isset($form['blocs']))
		{
			$blocsUniqueId = $form['blocs'];

			// Initialize blocs and create forms for blocs sub models.
			$blocsSubModelsFormArr = array();
			foreach (Yii::app()->params['blocs'] as $bloc)
			{
				$blocClass = ucfirst($bloc).'Bloc';

				if (!array_key_exists($bloc, Yii::app()->cms->blocs))
				{
					Yii::import('application.components.blocs.'.$bloc.'.*');
					Yii::import('application.components.blocs.'.$bloc.'.models.*');

					Yii::app()->cms->blocs[$bloc] = new $blocClass;
				}
				$blocSubModelsArr = Yii::app()->cms->blocs[$bloc]->subModels();
				if (is_array($blocSubModelsArr))
				{
					foreach ($blocSubModelsArr as $subModel)
					{
						$blocsSubModelsFormArr[] = array(
							'id' => '{formId}-{itemId}-'.lcfirst($subModel[0]).'Form',
							'models' => $subModel[0],
							'parentIdAttribute' => $subModel[1],
							'varName' => $form['blocs'].'_'.lcfirst($subModel[0]).'Form',
							'blocsSubForm'=>true,
						);
					}
				}
			}
			
			$newForm = array(
				'id' => $form['id'],
				'varName' => $form['varName'],
				'models' => null,
				'parentIdAttribute' => 'parent_id',
				'forms' => $blocsSubModelsFormArr,
				// Extra virtual attributes are assigned to the blocs.
				'onBeforeSetAttributes' => function($event) use ($blocsUniqueId)
				{			
					$model = $event->params['model'];
 					$paramName = $event->params['paramName'];
 					$paramNameArray = explode('[', $paramName);
					$formId = mb_substr($paramNameArray[1], 0, -1);
					$itemId = mb_substr($paramNameArray[2], 0, -1);
					$postItem = $_POST[get_class($model)][$formId][$itemId];

					$model->title_anchor = $postItem['title_anchor'];
					$model->title_page = $postItem['title_page'];
					$model->rank = $postItem['rank'];
					$model->unique_id = $blocsUniqueId;
					
					foreach (Yii::app()->languageManager->suffixes as $suffix)
					{
					   $model->{'title'.$suffix} = $postItem['title'.$suffix];
					}
				},
			);

			// Loading of the blocs models.
			$contentBlocs = Bloc::model()->multilang()->findAllByAttributes(array('parent_id'=>($parentModels !== null ? $parentModels->primaryKey : null), 'unique_id'=>$form['blocs']), array('index'=>'id', 'order'=>'rank ASC'));
		
			$blocsModels = array();
			foreach ($contentBlocs as $id => $contentBloc)
			{
				$className = 'Bloc'.ucfirst($contentBloc->bloc_type);

				if (array_key_exists('ml', $className::model()->behaviors()))
					$blocsModels[$id] = $className::model()->multilang()->findByPk($contentBloc->bloc_id);
				else
					$blocsModels[$id] = $className::model()->findByPk($contentBloc->bloc_id);

				$blocsModels[$id]->rank = $contentBloc->rank;
				$blocsModels[$id]->title_anchor = $contentBloc->title_anchor;
				$blocsModels[$id]->title_page = $contentBloc->title_page;
				
				foreach (Yii::app()->languageManager->suffixes as $suffix)
				{
				    $blocsModels[$id]->{'title'.$suffix} = $contentBloc->{'title'.$suffix};
				}
			}
			$newForm['models'] = $blocsModels;
							
			// Loading of the blocs sub-models.
			$this->onBeforeProcessFormSettings = function($event)
			{
				$form = &$event->params['form'];
	
				if (isset($form['blocsSubForm']))
				{
					unset($form['blocsSubForm']);
					// parentModels here contains blocs from multiple different models ex: BlocDocument, BlocEditor, etc.
					$parentModels = $event->params['parentModels'];
					
					$model = new $form['models'];
					$behaviors = $model->behaviors();
					
					$findOptions = array('index'=>$model->tableSchema->primaryKey);
					if (isset($form['orderAttribute']))
						$findOptions['order'] = $form['orderAttribute'].' ASC';
					
					$form['models'] = array();
					
					if (!empty($parentModels))
					{
						foreach ($parentModels as $id => $parentModel)
						{
							$relations = $model->relations();

							if (get_class($parentModel) != $relations['bloc'][1])
								continue;
	
							if (isset($behaviors['ml']))
								$form['models'][$id] = $model->multilang()->findAllByAttributes(array($form['parentIdAttribute']=>$parentModel->primaryKey), $findOptions);
							else
								$form['models'][$id] = $model->findAllByAttributes(array($form['parentIdAttribute']=>$parentModel->primaryKey), $findOptions);
						}
					}
				}
			};
			
			$form = $newForm;
		}

		// Models given in the form of a string are automatically created / loaded here.
		if (is_string($form['models']))
		{
			// 1st level models with no parent. A single model is created / loaded. Update loads model from GET id var. 
			// If there are multiple forms like this or if you want an array of unparented models, you must load your models in your controller this won't work.
			if ($parentModels === null)
			{
				if ($this->_action == 'Create')
					$form['models'] = new $form['models'];
				elseif ($this->_action == 'Update')
					$form['models'] = $this->loadModel($form['models']);
			}
			// Models that are parented to a single model. An array of models is loaded.
			elseif (is_object($parentModels))
			{
				$parentPk = $parentModels->primaryKey;

				$model = new $form['models'];
				$behaviors = $model->behaviors();
				
				$findOptions = array('index'=>$model->tableSchema->primaryKey);
				if (isset($form['orderAttribute']))
					$findOptions['order'] = $form['orderAttribute'].' ASC';
				
				if (isset($behaviors['ml']))
					$form['models'] = $model->multilang()->findAllByAttributes(array($form['parentIdAttribute']=>$parentPk), $findOptions);
				else
					$form['models'] = $model->findAllByAttributes(array($form['parentIdAttribute']=>$parentPk), $findOptions);
			}
			// Models that are parented to multiple different models. A multi dimensional array with arrays of models keyed by the id of the parent is loaded.
			else {
				$model = new $form['models'];
				$behaviors = $model->behaviors();
				
				$findOptions = array('index'=>$model->tableSchema->primaryKey);
				if (isset($form['orderAttribute']))
					$findOptions['order'] = $form['orderAttribute'].' ASC';
				
				$form['models'] = array();
	
				foreach ($parentModels as $id => $parentModel)
				{
					if (isset($behaviors['ml']))
						$form['models'][$id] = $model->multilang()->findAllByAttributes(array($form['parentIdAttribute']=>$parentModel->primaryKey), $findOptions);
					else
						$form['models'][$id] = $model->findAllByAttributes(array($form['parentIdAttribute']=>$parentModel->primaryKey), $findOptions);
				}
			}
		}
		$this->onAfterProcessFormSettings(new CEvent($this, array('parentModels'=>$parentModels, 'form'=>&$form, 'id'=>$this->id)));

		$this->_formIds[$form['varName']] = array($form['id'], $blocsUniqueId);
		
		unset($form['varName']);
		
		// Recursively processing child forms.
		if (isset($form['forms']))
		{
			if (!isset($form['forms']['id']))
			{
				foreach ($form['forms'] as $key => $subForm)
				{
					$form['forms'][$key] = $this->processFormSettingsForm($form['forms'][$key], $form['models']);
				}
			}
			else
				$form['forms'] = $this->processFormSettingsForm($form['forms'], $form['models']);
		}

		return $form;
	}
	
	/**
	 * Loads a model with the given id (in GET).
	 * 
	 * @param string $modelClass the model class.
	 * @param null|boolean $ml whether to load it in multilang mode. If null then will attempt to detect if model contains multilang behavior.
	 * 
	 * @return CActiveForm the model.
	 */
	protected function loadModel($modelClass, $ml=null)
	{
		if (!isset($ml))
		{
			$behaviors = $modelClass::model()->behaviors();
			$ml = isset($behaviors['ml']);
		}
		if ($ml)
			$model=$modelClass::model()->multilang()->findByPk($this->id);
		else
			$model=$modelClass::model()->findByPk($this->id);
			
		if ($model===null)
			throw new CHttpException(404);
			
		return $model;
	}

	/**
	 * Event, called before processing a (single) form.
	 * 
	 * Event parameters : 
	 * $form array the form settings to process. Passed by reference so you can modify it.
	 * $parentModels null|CActiveRecord|array parentModels the models of the parent form. Can be different types depending on the context. Defaults to null.
	 * $id int the id in GET (for update).
	 * 
	 * @param CEvent $event the event object.
	 */
	public function onBeforeProcessFormSettings($event)
	{
	    $this->raiseEvent('onBeforeProcessFormSettings', $event);
	}
	
	/**
	 * Event, called after processing a (single) form.
	 * 
	 * Event parameters : 
	 * $form array the form settings to process. Passed by reference so you can modify it.
	 * $parentModels null|CActiveRecord|array parentModels the models of the parent form. Can be different types depending on the context. Defaults to null.
	 * $id int the id in GET (for update).
	 * 
	 * @param CEvent $event the event object.
	 */
	public function onAfterProcessFormSettings($event)
	{
	    $this->raiseEvent('onAfterProcessFormSettings', $event);
	}
}
?>
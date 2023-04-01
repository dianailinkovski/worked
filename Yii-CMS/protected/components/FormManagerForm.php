<?php
/**
 * A form component used for FormManager
 * 
 * @see FormManager for more details
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Other
 */

class FormManagerForm extends CComponent
{
	/**
	 * @var array|CActiveRecord the model(s) involved in this form (array of models or just one model). 
	 *
	 * Required.
	 * 
	 * For top parent model, it can be a single model or be an array of models (like children models but without a parent).
	 * 
	 * For children of single model parent, it should be an array of models indexed by the primaryKey 
	 * ex: $chapters = Chapter::model()->findAllByAttributes(array('parent_id'=>$book->id), array('index'=>'id', 'order'=>'rank ASC'));
	 * 
	 * For children of parent with array of models, it should be like children but inside an array indexed by the parent models ids. ex:
	 * 
	 * $chapters = Chapter::model()->with(array(
	 * 		'pages'=>array('index'=>'id')
	 * ))->findAllByAttributes(array('parent_id'=>$book->id), array('index'=>'id', 'order'=>'rank ASC'));
	 * 	
	 * $pages = array();
	 * foreach ($chapters as $id => $chapter)
	 * 		$pages[$id] = $chapter->pages;
	 */
	public $models;
	/**
	 * @var string the id of this form
	 * Required.
	 * For top parent model or children to a parent with a single model, it can be anything ex: "bookForm".
	 * For children of parent with array of models, it must be the POST key in which your models are contained
	 * and you must have {formId} and {itemId} in your id because there might be multiple instances of this form 
	 * ex: {formId}-{itemId}-pageForm will translate into chapterForm-n7-pageForm,
	 * this way you can have an infinite depth of forms each with their unique id.
	 */
	public $id;
	/**
	 * @var array|FormManagerForm the sub-forms to this form. 
	 * Can be an array of FormManagerForm parameters or a FormManagerForm object or an array of either (if multiple forms).
	 */
	public $forms;
	/**
	 * @var array the attributes to pass on to the validate function.
	 * Defaults to default CActiveRecord functionality.
	 * @see CActiveRecord::validate()
	 */
	public $validateAttributes;
	/**
	 * @var boolean the clean errors variable to pass on to the validate function. 
	 * Defaults to default CActiveRecord functionality.
	 * @see CActiveRecord::validate()
	 */
	public $validateCleanErrors;
	/**
	 * @var array the attributes to pass on to the save function. 
	 * Defaults to default CActiveRecord functionality.
	 * @see CActiveRecord::save()
	 */
	public $saveAttributes;
	/**
	 * @var boolean whether or not to delete children models when a parent is deleted
	 * This is useful if you're not using database cascading deletion or have some logic to execute on the deletion of the children (such as in the beforeDelete or afterDelete events in the model).
	 * Defaults to false.
	 */
	public $recursiveDelete=false;
	/**
	 * @var string the name of the attribute linking to the parent model.
	 * If set, the script will assign the id of the parent model to this attribute.
	 */
	public $parentIdAttribute;
	/**
	 * @var int if set, this will add a validation where there needs to be a minimum amount of items present in the batch.
	 * If an error is triggered then validateCleanErrors will be set to false.
	 */
	public $minItems;
	/**
	 * @var string the name of the class of your models.
	 * If you have no models and no item was sent, the script needs to know which model to instance to put the error into 
	 * (this should be the name of the model class for your models in the models variable).
	 * If you use minItems then you must set this variable also
	 */
	public $minItemsModelClass;
	/**
	 * @var string the error message when a min items validation error is triggered.
	 * This error is added to the primary key attribute.
	 */
	public $minItemsErrorMessage;
	/**
	 * @var int if set, this will add a validation where there is a maximum amount of items allowed in the batch.
	 * If an error is triggered then validateCleanErrors will be set to false.
	 */
	public $maxItems;
	/**
	 * @var string the error message when a max items validation error is triggered.
	 * This error is added to the primary key attribute.
	 */
	public $maxItemsErrorMessage;
	
	
	/**
	 * @var boolean when events set this variable to true, the process is stopped at that point and won't go any further.
	 */
	private $_stopProcess;
	/**
	 * @var mixed reference to the form's request variable. $_requestVar or $_GET.
	 */
	private $_requestVar;
	/**
	 * @var FormManager the manager managing this form.
	 * Assigned during the initialization of FormManager.
	 */
	private $_manager;
	
	
	/**
	 * Constructor.
	 * Initiates properties
	 * @param array $properties the properties to initiate the class with
	 */
	public function __construct($properties=array())
	{
		foreach ($properties as $name => $value) 
		{
			$this->$name = $value;
		}

		if ($this->minItems !== null && $this->minItemsErrorMessage === null)
			$this->minItemsErrorMessage = 'You must have a minimum of '.$this->minItems.' items.';
		if ($this->maxItems !== null && $this->maxItemsErrorMessage === null)
			$this->maxItemsErrorMessage = 'You can have a maximum of '.$this->maxItems.' items.';
	}
	
	/**
	 * manager getter.
	 * @return FormManager
	 */
	public function getManager()
	{
		return $this->_manager;	
	}
	
	/**
	 * manager setter.
	 * @param FormManager $value
	 */
	public function setManager($value)
	{
		$this->_manager = $value;	
		
		if ($this->_manager->method == 'get')
			$this->_requestVar = &$_GET;
		else
			$this->_requestVar = &$_POST;
	}
	
	/**
	 * stopProcess setter
	 * @param boolean $value the value to set
	 */
	public function setStopProcess($value)
	{
		if ($value == true)
			$this->_stopProcess = true;
	}
	
	/**
	 * Checks if the models in this form were posted.
	 * @return boolean whether the models in this form were posted or not.
	 */
	public function checkIfPosted()
	{
		if (is_array($this->models))
		{
			foreach ($this->models as $key => $value)
			{
				if (is_array($value))
				{
					foreach ($value as $id => $model)
					{
						if (isset($this->_requestVar[get_class($model)]))
							return true;
						
						break;
					}
				}
				else {
					if (isset($this->_requestVar[get_class($value)]))
						return true;
				}
				break;
			}
		}
		else {
			if (isset($this->_requestVar[get_class($this->models)]))
				return true;
		}
		
		return false;
	}
	
	/**
	 * Deletes items present in models but not present in POST.
	 * 
	 * Recursive, deletes all sub-forms as well. 
	 * If $recursiveDelete is true, will delete all sub-models if parent model is deleted.
	 * 
	 * @param int $parentId the id of the parent model.
	 * @param FormManagerForm $parentForm the parent form to this form.
	 * @param boolean force whether to bypass the check and delete anyway.
	 * 
	 * @return boolean|null true if operations done, null if process stopped by events.
	 */
	public function delete($parentId=null, $parentForm=null, $force=false)
	{
		if ($parentId !== null)
			$paramName = str_replace('{formId}', $parentForm->id, str_replace('{itemId}', $parentId, $this->id));
		else
			$paramName = $this->id;

		if (isset($parentId))
		{
			// Parent could be array or models or a single model.
			if (is_array($parentForm->models))
				$models = &$this->models[$parentId];
			else
				$models = &$this->models;
		}
		else
			$models = &$this->models;

		if (is_array($models))
		{
			foreach ($models as $id => $model) 
			{
				$modelClass = get_class($model);
	
				if ($force
				|| !isset($this->_requestVar[$modelClass][$paramName]) 
				|| !isset($this->_requestVar[$modelClass][$paramName][$id])) 
				{
					
					$this->onBeforeDelete(new CEvent($this, array('model'=>$model, 'paramName'=>$paramName)));
					if ($this->_stopProcess)
						return;
						
					if ($this->recursiveDelete == true && $this->forms !== null)
					{
						foreach ($this->forms as $form)
						{
							$form->delete($id, $this, true);
							if ($this->_stopProcess)
								return;
						}
					}
					$model->delete();
	
					unset($models[$id]);
					
					$this->onAfterDelete(new CEvent($this, array('model'=>$model, 'paramName'=>$paramName)));
					if ($this->_stopProcess)
						return;
						
				}
				else
				{
					
					if ($this->forms !== null)
					{
						foreach ($this->forms as $form)
						{
							$form->delete($id, $this, false);
							if ($this->_stopProcess)
								return;
						}
					}
					
				}
			}
		}
		elseif (is_object($models)) 
		{
			if ($this->forms !== null)
			{
				foreach ($this->forms as $form)
				{
					$form->delete($models->primaryKey, $this, false);
					if ($this->_stopProcess)
						return;
				}
			}
		}

		return true;		
	}
	
	/**
	 * Validates a single model. Attributes are set here.
	 *
     * @param CActiveRecord $model the model.
     * @param string $paramName the id of the form from POST.
	 * @param array $attributes the model attributes from POST.
	 * @param string $postId the item id from POST (can be n0,n1, etc).
	 * 
	 * @return boolean|null boolean if valid or not, null if process stopped by events.
	 */
	protected function validateModel($model, $paramName, $attributes, $postId=null)
	{
		if ($postId === null)
			$paramNameForEvents = $paramName;
		else 
			$paramNameForEvents = get_class($model).'['.$paramName.']['.$postId.']';
		
		$this->onBeforeSetAttributes(new CEvent($this, array('model'=>$model, 'paramName'=>$paramNameForEvents)));
		if ($this->_stopProcess)
			return;

		$model->attributes = $attributes;

		// Setting UploadingBehavior attributes.
		foreach ($model->behaviors() as $behaviorName => $behavior)
		{
			if (in_array(get_class($model->$behaviorName), array('ActiveRecordUploadingBehavior', 'ModelUploadingBehavior')))
			{
				if ($postId !== null)
					$model->$behaviorName->attributePostName = '['.$paramName.']['.$postId.']'.$model->$behaviorName->attribute;
					
				if ($model->$behaviorName->allowDelete)
					$model->$behaviorName->delete = isset($attributes[$behaviorName]['delete']) ? $attributes[$behaviorName]['delete'] : 0;
				$model->$behaviorName->tempName = isset($attributes[$behaviorName]['tempName']) ? $attributes[$behaviorName]['tempName'] : '';
			}
		}

		$this->onAfterSetAttributes(new CEvent($this, array('model'=>$model, 'paramName'=>$paramNameForEvents)));
		if ($this->_stopProcess)
			return;

		$valid = $model->validate(($this->validateAttributes !== null ? $this->validateAttributes : null), ($this->validateCleanErrors !== null ? $this->validateCleanErrors : true));

		if (!$valid)
		{
			$this->onInvalid(new CEvent($this, array('model'=>$model, 'paramName'=>$paramNameForEvents)));
			if ($this->_stopProcess)
				return;
		}
		// Validating sub-forms to this model.
		if ($this->forms !== null)
		{
			foreach ($this->forms as $subForm)
			{
				if ($postId !== null)
					$valid = $subForm->validate($postId, $this) && $valid;
				else
					$valid = $subForm->validate() && $valid;
				
				if ($this->_stopProcess)
					return;
			}
		}
		
		return $valid;
	}
	
	/**
	 * Validates all models. For tabular style forms, new models are created as well.
	 * 
	 * Recursive, validates all sub-forms as well. 
	 * minItems and maxItems validations are possible also.
	 * 
	 * @param string $parentId the id of the parent model from POST (can be n0,n1, etc).
	 * @param FormManagerForm $parentForm the parent form to this form.
	 * 
	 * @return boolean|null boolean if valid or not, null if process stopped by events.
	 */
	public function validate($parentId=null, $parentForm=null)
	{
		$valid = true;
		
		if (!is_array($this->models))
		{
			$modelClass = get_class($this->models);
			$model = $this->models;
			
			if (($valid = $this->validateModel($model, $modelClass, (isset($this->_requestVar[$modelClass]) ? $this->_requestVar[$modelClass] : array()), null)) === null)
				return;
		}
		else {
			if ($parentId !== null)
				$paramName = str_replace('{formId}', $parentForm->id, str_replace('{itemId}', $parentId, $this->id));
			else
				$paramName = $this->id;

			// Cycling through requestVar because we need to know if there are new items created by javascript.
			$i = 0;
			foreach ($this->_requestVar as $key => $value) 
			{
				if (is_array($value) && array_key_exists($paramName, $value))
				{
					foreach ($value[$paramName] as $id => $item) 
					{
						// Ids beginning with "n" means it's a new item, note that some can be missing (could be n0, n1, n3, n7...).
						if (substr($id, 0, 1) == 'n')
						{
							if ($parentId !== null)
							{
								$this->models[$parentId][$id] = new $key;
								if ($this->parentIdAttribute !== null)
									$this->models[$parentId][$id]->{$this->parentIdAttribute} = 0; // Temporary to not trigger validation error.
							}
							else {
								$this->models[$id] = new $key;
								if ($this->parentIdAttribute !== null)
									$this->models[$id]->{$this->parentIdAttribute} = 0; // Temporary to not trigger validation error.
							}
						}
						if ($parentId !== null)
						{
							if (!isset($this->models[$parentId][$id]))
								break;
	
							$model = $this->models[$parentId][$id];
						}
						else
							$model = $this->models[$id];
						
						// Extra validations minItems and maxItems.
						
						if ($this->minItems !== null && $i == 0 && $this->minItems > 0 && count($value[$paramName]) < $this->minItems)
						{
							$model->addError($model->primaryKey, $this->minItemsErrorMessage);
							$this->validateCleanErrors = false;
						}
						if ($this->maxItems !== null && $i == $this->maxItems)
						{
							$model->addError($model->primaryKey, $this->maxItemsErrorMessage);
							$this->validateCleanErrors = false;
						}
						
						if (($modelValidated = $this->validateModel($model, $paramName, $item, $id)) === null)
							return;
						
						$valid = $modelValidated && $valid;
						
						$i++;
					}
				}
			}
			
			// If no items were sent, we still need to trigger a validation error for minItems, 
			// and we need to create an empty model to put the error in it.
			if ($this->minItems !== null && $this->minItems > 0 && $i == 0)
			{
				if ($parentId !== null)
					$model = $this->models[$parentId]['n0'] = new $this->minItemsModelClass;
				else 
					$model = $this->models['n0'] = new $this->minItemsModelClass;
				
				$model->addError($model->primaryKey, $this->minItemsErrorMessage);

				$valid = false;
			}
		}
		
		return $valid;
	}
	
	/**
	 * Saves a single model.
	 * 
     * @param CActiveRecord $model the model.
     * @param string $paramName the id of the form from POST.
	 * @param int $parentModelPk the id of the parent model after is has been saved.
	 * @param string $postId the item id from POST (can be n0,n1, etc).
	 * 
	 * @return boolean|null true if operations completed, null if process stopped by events.
	 */
	protected function saveModel($model, $paramName, $parentModelPk=null, $postId=null)
	{			
		if ($postId === null)
			$paramNameForEvents = $paramName;
		else 
			$paramNameForEvents = get_class($model).'['.$paramName.']['.$postId.']';
		
		$this->onBeforeSave(new CEvent($this, array('model'=>$model, 'paramName'=>$paramNameForEvents)));
		if ($this->_stopProcess)
			return;

		$saveAttributes = $this->saveAttributes !== null ? $subForm['saveAttributes'] : null;
		
		if ($model->isNewRecord && $this->parentIdAttribute !== null && $parentModelPk !== null)
			$model->{$this->parentIdAttribute} = $parentModelPk;

		// nestedSetBehavior requires special save
		
		$nestedSetBehaviorFound = false;
		foreach ($model->behaviors() as $behaviorName => $behavior)
		{
			if (get_class($model->$behaviorName) == 'NestedSetBehavior')
			{
				if ($model->isNewRecord)
				{
	                if ($model->tree->hasManyRoots == true)
	                    $model->saveNode(false, $saveAttributes);
	                else 
	                {
	                	$nestedModelClass = get_class($model);
	                    $root = $nestedModelClass::model()->roots()->find();
	                    $model->appendTo($root, false);
	                }
				}
				else
					$model->saveNode(false, $saveAttributes);
				
				$nestedSetBehaviorFound = true;
				
				break;
			}
		}
		if (!$nestedSetBehaviorFound)
			$model->save(false, $saveAttributes);

		$this->onAfterSave(new CEvent($this, array('model'=>$model, 'paramName'=>$paramNameForEvents)));
		if ($this->_stopProcess)
			return;

		// Saving sub-forms to this model.
		if ($this->forms !== null)
		{
			foreach ($this->forms as $form)
			{
				$form->save($model->primaryKey, $postId, $this);
				if ($this->_stopProcess)
					return;
			}
		}
		
		return true;
	}

	/**
	 * Saves all models.
	 * 
	 * Recursive, saves all sub-forms as well. 
	 * 
	 * @param int $parentModelPk the id of the parent model after is has been saved.
	 * @param string $parentId the id of the parent model from POST (can be n0,n1, etc).
	 * @param FormManagerForm $parentForm the parent form to this form.
	 * 
	 * @return boolean|null boolean true if operations completed, null if process stopped by events.
	 */
	
	public function save($parentModelPk=null, $parentId=null, $parentForm=null)
	{
		if (!is_array($this->models))
		{
			if ($this->saveModel($this->models, get_class($this->models), $parentModelPk, null) === null)
				return;
		}
		else {
			if ($parentId !== null)
				$paramName = str_replace('{formId}', $parentForm->id, str_replace('{itemId}', $parentId, $this->id));
			else
				$paramName = $this->id;
	
			if (isset($parentId))
			{
				if (isset($this->models[$parentId]))
					$models = $this->models[$parentId];
				else
					$models = array();
			}
			else
				$models = $this->models;

			foreach ($models as $id => $model)
			{
				if ($this->saveModel($model, $paramName, $parentModelPk, $id) === null)
					return;
			}
		}
		
		return true;
	}
	
	/**
	 * Event, called before attributes are set in the model (before validation).
	 * 
	 * Event parameters : 
	 * $model CActiveRecord the model.
	 * $paramName string the post name under which this model was posted, can represent an array ex : ModelClass[formId][itemId]
	 * 
	 * @param CEvent $event the event object
	 */
	public function onBeforeSetAttributes($event)
	{
	    $this->raiseEvent('onBeforeSetAttributes', $event);
	}
	/**
	 * Event, called after attributes are set in the model (before validation).
	 * 
	 * Event parameters : 
	 * $model CActiveRecord the model.
	 * $paramName string the post name under which this model was posted, can represent an array ex : ModelClass[formId][itemId]
	 * 
	 * @param CEvent $event the event object
	 */
	public function onAfterSetAttributes($event)
	{
	    $this->raiseEvent('onAfterSetAttributes', $event);
	}
	/**
	 * Event, called before deleting a model.
	 * 
	 * Event parameters : 
	 * $model CActiveRecord the model.
	 * $paramName string the post name under which this model was posted, can represent an array ex : ModelClass[formId][itemId]
	 * 
	 * @param CEvent $event the event object
	 */
	public function onBeforeDelete($event)
	{
	    $this->raiseEvent('onBeforeDelete', $event);
	}
	/**
	 * Event, called after deleting a model.
	 * 
	 * Event parameters : 
	 * $model CActiveRecord the model.
	 * $paramName string the post name under which this model was posted, can represent an array ex : ModelClass[formId][itemId]
	 * 
	 * @param CEvent $event the event object
	 */
	public function onAfterDelete($event)
	{
	    $this->raiseEvent('onAfterDelete', $event);
	}
	/**
	 * Event, called before saving a model.
	 * 
	 * Event parameters : 
	 * $model CActiveRecord the model.
	 * $paramName string the post name under which this model was posted, can represent an array ex : ModelClass[formId][itemId]
	 * 
	 * @param CEvent $event the event object
	 */
	public function onBeforeSave($event)
	{
	    $this->raiseEvent('onBeforeSave', $event);
	}
	/**
	 * Event, called after saving a model.
	 * 
	 * Event parameters : 
	 * $model CActiveRecord the model.
	 * $paramName string the post name under which this model was posted, can represent an array ex : ModelClass[formId][itemId]
	 * 
	 * @param CEvent $event the event object
	 */
	public function onAfterSave($event)
	{
	    $this->raiseEvent('onAfterSave', $event);
	}
	/**
	 * Event, called when a model is invalid.
	 * 
	 * Event parameters : 
	 * $model CActiveRecord the model.
	 * $paramName string the post name under which this model was posted, can represent an array ex : ModelClass[formId][itemId]
	 * 
	 * @param CEvent $event the event object
	 */
	public function onInvalid($event)
	{
	    $this->raiseEvent('onInvalid', $event);
	}
}
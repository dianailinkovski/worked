<?php
Yii::import('application.components.FormManagerForm');

/**
 * FormManager facilitates the back-end operations of managing a form.
 *
 * This class is especially useful for managing "tabular" style forms and forms that have a hiarchy relationship to one another.
 * You can have an infinite amount of nesting within your forms.
 * It has built-in support for UploadingBehavior and NestedSetBehavior.
 * A myriad of events are at your disposal if you need to interrupt the execution at some point during the processing to add in your own functionality.
 * Although this class can work independently, to help you with the front-end and javascript for listing / adding / deleting items and more, you should use the TabularInputWidget.
 *
 * Note that no property is mandatory in this class, however in your forms, "id" and "models" are (models can also be an empty array).
 *
 * Here's an example of what a controller would look like.
 * In this example, the top parent book is a single model, you might have actions creating/updating books with GET variable to get the model like you would normally do.
 * The chapters are children to a book parented with book_id, but because only one book is edited in the page at a time, all chapters share the same book_id.
 * The pages are children to the chapters and they can be parented to different chapters.
 * Because of this, the id of your form must contain {formId} and {itemId} because multiple forms might be created if there are multiple chapters.
 * The script needs this in order to differentiate the different forms and make proper parenting.
 * In your view file, any form with an array of models must send the fields as an associative array containing both the form id and the item id.
 * Ex : $form->textField($model,'['.$formId.']['.$itemId.']title');
 * Where $formId is the id of the form ex : chapterForm, chapterForm-1-pageForm and $itemId the id of an item.
 * Note that no model will save unless all models are validated.
 *
 * See the class properties for more details
 *
 * <pre>
 * 		// Assuming we got $book model, getting chapters and pages arrays.
 *
 * 		$chapters = Chapter::model()->with(array(
 * 			'pages'=>array('index'=>'id', 'order'=>'pages.rank ASC')
 * 		))->findAllByAttributes(array('book_id'=>$book->id), array('index'=>'id', 'order'=>'t.rank ASC'));
 *
 * 		$pages = array();
 * 		foreach ($chapters as $id => $chapter)
 * 			$pages[$id] = $chapter->pages;
 *
 * 		// This initializes the FormManager (and subsequent FormManagerForm).
 *
 * 		$formManager = new FormManager(array(
 * 			'redirect'=>$this->createUrl('admin'),
 * 			'forms' => array(
 * 				array(
 * 					'id'=>'bookForm',
 * 					'models'=>$book,
 * 					'forms' => array(
 * 						'id'=>'chapterForm',
 * 						'models'=>$chapters,
 * 						'parentIdAttribute'=>'book_id',
 * 						'forms' => array(
 * 							'id'=>'{formId}-{itemId}-pageForm',
 * 							'models'=>$pages,
 * 							'parentIdAttribute'=>'chapter_id',
 * 							'onInvalid'=>function($event) { // Example of an event
 * 								$model = $event->params['model'];
 * 								$paramName = $event->params['paramName'];
 * 								$formId = substr(explode('[', $paramName)[1], 0, -1);
 *
 * 								if ($model->isNewRecord && $formId = 'chapterForm-3-pageForm')
 * 									$event->sender->stopProcess = true; // Stops the processing, no more models will be validated after this one (you don't have to do this).
 * 							},
 * 						),
 * 					),
 * 				),
 * 			),
 * 		));
 *
 * 		// The process function does the actual validating, saving, etc.
 *
 * 		$formManager->process();
 *
 * 		// Rendering with models we get from the form manager.
 *
 * 		$this->render(($book->isNewRecord ? 'create' : 'update'),array(
 *  		'book'=>$formManager->getModels('bookForm'),
 * 			'chapters'=>$formManager->getModels('chapterForm'),
 * 			'pages'=>$formManager->getModels('{formId}-{itemId}-pageForm'),
 * 		));
 *
 * </pre>
 *
 * Book view file contains :
 *
 * <?php echo $form->errorSummary(array_merge(array($book), $chapters, array_reduce(array_map('array_values', $pages), 'array_merge', array()))); ?>
 *
 * <div class="row">
 *		<?php echo $form->labelEx($book,'title'); ?>
 *		<?php echo $form->textField($book,'title'); ?>
 *		<?php echo $form->error($book,'title'); ?>
 * </div>
 *
 * <?php $this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
 * 		'id'=>'chapterForm',
 * 		'form'=>$form,
 * 		'models'=>$chapters,
 * 		'renderData'=>array('pages'=>$pages),
 * 		'layout'=>array('_chapter'=>new Chapter),
 * 		'nestedWidgets'=>array('_chapter'=>'{formId}-{itemId}-pageForm'),
 * 		'orderAttribute'=>'rank',
 * )); ?>
 *
 * Chapters view file contains :
 *
 * <div class="row">
 * 		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']title'); ?>
 * 		<?php echo $form->textField($model,'['.$formId.']['.$itemId.']title'); ?>
 * 		<?php echo $form->error($model,'['.$formId.']['.$itemId.']title'); ?>
 * </div>
 *
 * <?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>
 *
 * <?php $this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
 * 		'id'=>$formId.'-'.$itemId.'-pageForm',
 * 		'form'=>$form,
 * 		'layout'=>array('_page'=>new Page),
 * 		'models'=>(isset($pages[$itemId]) ? $pages[$itemId] : array()),
 * 		'orderAttribute'=>'rank',
 * )); ?>
 *
 * Pages view file contains :
 *
 * <div class="row">
 * 		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']title'); ?>
 * 		<?php echo $form->textField($model,'['.$formId.']['.$itemId.']title'); ?>
 * 		<?php echo $form->error($model,'['.$formId.']['.$itemId.']title'); ?>
 * </div>
 *
 * <?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>
 *
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @see UploadingBehavior
 * @see NestedSetBehavior
 * @see FormManagerFrom
 * @see TabularInputWidget
 *
 * @package Other
 */

class FormManager extends CComponent
{
	/**
	 * @var array|FormManagerForm the forms.
	 * Can be an array of FormManagerForm parameters or a FormManagerForm object or an array of either (if multiple forms).
	 */
	public $forms;
	/**
	 * @var boolean whether or not to use the internal mechanism to check if the form was posted.
	 * There are scenarios where this can be problematic. In this case, set this to false and do your own logic in the controller
	 * and don't call FormManager::process() if it's not posted and call it if it is.
	 * Defaults to true.
	 */
	public $checkIfPosted=true;
	/**
	 * @var mixed the url it will redirect to after the form is successfully sent and validated and saved.
	 * @see CController::redirect()
	 */
	public $redirect;
	/**
	 * @var string the form method, post or get. Defaults to 'post'.
	 */
	public $method='post';


	/**
	 * @var boolean whether the form is considered posted or not. Defaults to false.
	 */
	private $_posted=false;
	/**
	 * @var boolean when events set this variable to true, the process is stopped at that point and won't go any further. Defaults to false.
	 */
	private $_stopProcess=false;
	/**
	 * @var array internal referencing to the forms, used for the getForm and getModels function.
	 */
	private $_forms;

	/**
	 * Constructor.
	 * Initiates properties and initiates all forms
	 * @param array $properties the properties to initiate the class with
	 */
	public function __construct($properties=array())
	{
		foreach ($properties as $name => $value)
		{
			$this->$name = $value;
		}
		if ($this->checkIfPosted == false)
			$this->_posted = true;

		if ($this->forms !== null)
			$this->initForm($this->forms); // recursive, will initiate all subforms as well
	}

	/**
	 * Initiate a form.
	 * Function is recursive and initiates all subforms.
	 * @param mixed $form the form to initiate
	 */
	private function initForm(&$forms)
	{
		if (is_array($forms))
		{
			if (!isset($forms['id'])) // no id so assuming it contains multiple forms
			{
				$formsArr = array();
				foreach ($forms as $form)
				{
					if (is_array($form))
						$newForm = new FormManagerForm($form);
					else
						$newForm = $form;

					$formsArr[] = $newForm;

					$this->_forms[$newForm->id] = $newForm;
					$newForm->manager = $this;

					if ($newForm->forms !== null)
						$this->initForm($newForm->forms);

					if (!$this->_posted)
						$this->_posted = $newForm->checkIfPosted();
				}
				$forms = $formsArr;
			}
			else {
				$forms = array(new FormManagerForm($forms));

				$this->_forms[$forms[0]->id] = $forms[0];
				$forms[0]->manager = $this;

				if ($forms[0]->forms !== null)
					$this->initForm($forms[0]->forms);

				if (!$this->_posted)
					$this->_posted = $forms[0]->checkIfPosted();
			}
		}
		else {
			$forms = array($forms);

			$this->_forms[$forms[0]->id] = $forms[0];
			$forms[0]->manager = $this;

			if ($forms[0]->forms !== null)
				$this->initForm($forms[0]->forms);

			if (!$this->_posted)
				$this->_posted = $forms[0]->checkIfPosted();
		}
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
	 * Get models from a particular form by specifying its id.
	 * This is just a shorthand to not have to do ex: $formManager->forms[0]->forms[1]->models.
	 * @param string $id the id of the form.
	 * @return array|CActiveRecord the model(s) or empty array if not found.
	 */
	public function getModels($id)
	{
		if (isset($this->_forms[$id]))
			return $this->_forms[$id]->models;
		else
			return array();
	}

	/**
	 * Get a form by specifying its id.
	 * This is just a shorthand to not have to do ex: $formManager->forms[0]->forms[0]->forms[1].
	 * @param string $id the id of the form.
	 * @return false|FormManagerForm false if not found.
	 */
	public function getForm($id)
	{
		if (isset($this->_forms[$id]))
			return $this->_forms[$id];
		else
			return false;
	}
	/**
	 * Process the form(s).
	 * Step 1 check for deleted form items (for tabular type forms) and deletes them regardless of validation
	 * (because it's weird for them to come back if the form is invalid).
	 * Step 2 validates all forms.
	 * Step 3 if all forms are valid, save everything (and add new items if new ones are present).
	 * Step 4 redirect the user to whatever url specified (if valid).
	 * You can interrupt and add functionality to the process at any time with events.
	 */
	public function process()
	{
		$this->onBeginProcess(new CEvent($this));
		if ($this->_stopProcess)
			return;

		if ($this->_posted)
		{
			$this->onPosted(new CEvent($this));
			if ($this->_stopProcess)
				return;

			if ($this->forms !== null)
			{
				foreach ($this->forms as $form)
				{
					if ($form->delete() !== true)
						return;
				}
			}

			$this->onBeforeValidateAll(new CEvent($this));
			if ($this->_stopProcess)
				return;

			$valid = true;
			if ($this->forms !== null)
			{
				foreach ($this->forms as $form)
				{
					if (($validateResult = $form->validate()) === null)
						return;

					$valid = $validateResult && $valid;
				}
			}
			$this->onAfterValidateAll(new CEvent($this));
			if ($this->_stopProcess)
				return;

			if ($valid)
			{
				$this->onBeforeSaveAll(new CEvent($this));
				if ($this->_stopProcess)
					return;

				if ($this->forms !== null)
				{
					foreach ($this->forms as $form)
					{
						if ($form->save() !== true)
							return;
					}
				}
				$this->onAfterSaveAll(new CEvent($this));
				if ($this->_stopProcess)
					return;

				if ($this->redirect !== null)
				{
					$this->onEndProcess(new CEvent($this));
					if ($this->_stopProcess)
						return;

					Yii::app()->controller->redirect($this->redirect);
				}
			}
			else {
				$this->onInvalidAll(new CEvent($this));
				if ($this->_stopProcess)
					return;
			}
		}
		$this->onEndProcess(new CEvent($this));
		if ($this->_stopProcess)
			return;
	}

	/**
	 * Event, called after validation when any form is invalid.
	 * @param CEvent $event the event object
	 */
	public function onInvalidAll($event)
	{
	    $this->raiseEvent('onInvalidAll', $event);
	}

	/**
	 * Event, called at the beginning of the process.
	 * @param CEvent $event the event object
	 */
	public function onBeginProcess($event)
	{
	    $this->raiseEvent('onBeginProcess', $event);
	}

	/**
	 * Event, called at the end of the process or just before redirecting if that's the case.
	 * @param CEvent $event the event object
	 */
	public function onEndProcess($event)
	{
	    $this->raiseEvent('onEndProcess', $event);
	}

	/**
	 * Event, called when the process begins and considers the form posted.
	 * @param CEvent $event the event object
	 */
	public function onPosted($event)
	{
	    $this->raiseEvent('onPosted', $event);
	}

	/**
	 * Event, called after everything is valid before saving.
	 * @param CEvent $event the event object
	 */
	public function onBeforeSaveAll($event)
	{
	    $this->raiseEvent('onBeforeSaveAll', $event);
	}

	/**
	 * Event, called after saving all forms before redirecting.
	 * @param CEvent $event the event object
	 */
	public function onAfterSaveAll($event)
	{
	    $this->raiseEvent('onAfterSaveAll', $event);
	}

	/**
	 * Event, called after the validation of all forms.
	 * @param CEvent $event the event object
	 */
	public function onAfterValidateAll($event)
	{
	    $this->raiseEvent('onAfterValidateAll', $event);
	}

	/**
	 * Event, called before the validation of all forms.
	 * @param CEvent $event the event object
	 */
	public function onBeforeValidateAll($event)
	{
	    $this->raiseEvent('onBeforeValidateAll', $event);
	}
}

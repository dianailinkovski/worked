<?php
Yii::import('application.components.actions.CrudAction');

/**
 * Deletes an entry.
 * 
 * Uses "id" GET variable.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Action
 */
class Delete extends CrudAction 
{
	/**
	 * @var string the model class to use.
	 */
	public $modelClass;

	/**
	 * @var string url to redirect to. Defaults to admin.
	 */
	public $redirect=array('admin');
	
    public function run($id) 
    {
    	$this->id = $id;

		$modelClass = $this->modelClass;
		if (array_key_exists('tree', $modelClass::model()->behaviors()))
			$this->loadModel($this->modelClass)->deleteNode();
		else
			$this->loadModel($this->modelClass)->delete();
    	
		if(Yii::app()->request->isPostRequest)
		{
			// If AJAX request (triggered by deletion via admin grid view), we should not redirect the browser.
			if(!isset($_GET['ajax']))
				$this->controller->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : $this->redirect);
		}
		else
			$this->controller->redirect($this->redirect);
    }
}
?>
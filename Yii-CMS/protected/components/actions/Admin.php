<?php
Yii::import('application.components.actions.CrudAction');

/**
 * Renders a grid view of a particular model class.
 * 
 * Supports nested set behavior extension operations.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Action
 */
class Admin extends CrudAction 
{
	/**
	 * @var string the model class to use.
	 */
	public $modelClass;
	
    public function run() 
    {
    	// Support for nested set behavior extension.
    	if (isset($_GET['moveNode']) && isset($_GET['to']) && isset($_GET['id']))
    	{
    		$modelClass = $this->modelClass;
    		$action = $_GET['moveNode'];

    	    $to = $modelClass::model()->findByPk((int) $_GET['to']);
	        $moved = $modelClass::model()->findByPk((int) $_GET['id']);
	
	        if (!is_null($to) && !is_null($moved)) {
	            try {
	                switch ($action) {
	                    case 'child':
	                        $moved->moveAsLast($to);
	                        break;
	                    case 'before':
	                        if($to->isRoot()) {
	                            $moved->moveAsRoot();
	                        } else {
	                            $moved->moveBefore($to);
	                        }
	                        break;
	                    case 'after':
	                        if($to->isRoot()) {
	                            $moved->moveAsRoot();
	                        } else {
	                            $moved->moveAfter($to);
	                        }
	                        break;
	                }
	            } catch (Exception $e) {
	                //Yii::app()->user->setFlash('CQTeeGridView', $e->getMessage());
	            }
	        }
    	}
    	// Support for ordering behavior.
        if (isset($_GET['sortNode']) && isset($_GET['siblingId']) && isset($_GET['nodeId']) && isset($_GET['siblingType']))
    	{
    		$modelClass = $this->modelClass;
    	    $model = $modelClass::model()->findByPk((int)$_GET['nodeId']);
	        $modelSibling = $modelClass::model()->findByPk((int)$_GET['siblingId']);
	        $model->moveTo($modelSibling, $_GET['siblingType']);
    	}
		$model = new $this->modelClass('search');
		$model->unsetAttributes();  // clear any default values
		
		if(isset($_GET[$this->modelClass]))
			$model->attributes=$_GET[$this->modelClass];

		$this->controller->render('admin',array(
			'model'=>$model,
		));
    }
}
?>
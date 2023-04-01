<?php
Yii::import('application.components.actions.CrudAction');

/**
 * Renders a creation form.
 * 
 * @see CrudAction.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Action
 */
class Create extends CrudAction 
{
    public function run() 
    {
		$this->createUpdateCommon();
    }
}
?>

<?php
Yii::import('contest.models.*');

/**
 * List contests
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class CurrentContestsWidget extends CWidget
{
    protected function renderContent()
    {
        $this->render('currentContestsWidget', array(
        	'contests'=>Contests::model()->findAll(array(
				'order' => 'start_date DESC',
        		'condition' => "status = 'active'" ,
			)),
        ));
    }
}
?>
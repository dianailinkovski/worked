<?php
Yii::import('message.models.*');

/**
 * Notifier of new messages.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class MessageNotificationWidget extends CWidget
{
	/**
	 * @var string to override the data-target attribute of the dialog link
	 */
	public $dataTarget;
	
    public function run()
    {
		Yii::app()->clientScript->registerCssFile(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('message.assets'), false, -1, YII_DEBUG).'/css/message.css');
		
		$currentDate = date('Y-m-d H:i:s');
        $this->render('messageNotification', array(
        	'newMessageCount'=>MessageAssoc::model()->with('message')->countByAttributes(array('seen'=>0, 'member_id'=>$this->controller->memberModel->id), array('condition'=>"message.datetime <= '".$currentDate."'")),
			'oldMessageCount'=>MessageAssoc::model()->with('message')->countByAttributes(array('seen'=>1, 'member_id'=>$this->controller->memberModel->id), array('condition'=>"message.datetime <= '".$currentDate."'")),
        ));
    }
}
?>
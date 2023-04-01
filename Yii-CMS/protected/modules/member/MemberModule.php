<?php
/**
 * Member module
 *
 * Manages members, with widgets for creating new account, login
 *
 * To have a member model throughout the whole site, add this to Controller:
 * 
 * public $memberModel;
 * 
 * public function beforeAction($action)
 * {
 *    if (!Yii::app()->user->isGuest)
 * 	  {
 *    	  Yii::import('member.models.*');
 *    	  $this->memberModel = Member::model()->findByPk(trim(Yii::app()->user->id, 'Member-'));
 *    }
 * 
 * 	  return parent::beforeAction($action);
 * }
 *
 * To have a different session handle for this module, change the user component in your controller:
 *
 * if (isset(Yii::app()->userFront))
 * 		Yii::app()->setComponent('user', Yii::app()->userFront);
 *
 * And add this information to the config file:
 *
 * 'userFront'=>array(
 * 		'allowAutoLogin'=>true,
 * 		'class'=>'RWebUser',
 * 		'stateKeyPrefix'=>'front',
 * 		'loginUrl'=>array('/member/default/login'),
 * ),
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Module
 */

class MemberModule extends CmsModule
{
	public function init()
	{
		$this->setImport(array(
			'member.models.*',
			'member.components.*',
		));
	}
	
	public static function getAdminMenu()
	{
		return array(
			array('label'=>'{sectionName}', 'url'=>array('/member/admin/admin'), 'icon'=>'edit')
		);
	}
}
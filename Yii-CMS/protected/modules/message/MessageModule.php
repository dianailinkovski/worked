<?php
/**
 * Message module
 *
 * Publish messages to website users, display using the widget
 *
 * Important: if a user triggers the message model while not connected (session expire, etc), 
 * a javascript script redirects the user to the login page, so "loginUrl" of the front-end "CWebUser" must be set.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Module
 */

class MessageModule extends CmsModule
{
	private $_assetsUrl;
	
	
	public function init()
	{
		$this->setImport(array(
			'message.models.*',
			'message.components.*',
		));
	}
	
	public static function getAdminMenu()
	{
		return array(
			array('label'=>'{sectionName}', 'url'=>array('/message/admin/admin'), 'icon'=>'edit')
		);
	}

    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                Yii::getPathOfAlias('message.assets'), false, -1, YII_DEBUG);
        return $this->_assetsUrl;
    }
}

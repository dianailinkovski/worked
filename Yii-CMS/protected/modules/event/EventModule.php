<?php
/**
 * Event module
 *
 * Manages events, module is instantiable, has RSS feed.
 *
 * In the config you must have 'archivesVarName' => array('fr'=>'archives', 'en'=>'archives')
 * to configure what you want to call the GET parameter
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Module
 */

class EventModule extends CmsModule
{
	public $instantiable = true;
	
	public $archivesVarName;
	
	
	private $_assetsUrl;
	
	
	public function init()
	{
		$this->setImport(array(
			'event.models.*',
			'event.components.*',
		));
	}
	
	public static function getAdminMenu()
	{
		return array(
			array('label'=>'{sectionName}', 'url'=>array('/event/admin/admin'), 'icon'=>'edit')
		);
	}
	
    public static function getUrlRules()
    {
    	$archivesVarName = Yii::app()->modules['event']['archivesVarName'][Yii::app()->language];
    	
    	return array(
    		'event/default/index/<archives:'.$archivesVarName.'>' => 'event/default/index',
    		'event/default/(index|detail)/<archives:'.$archivesVarName.'>/<n:[0-9A-Za-z-_]+>' => array('event/default/detail', 'parsingOnly'=>true),
    		'event/default/detail/<archives:'.$archivesVarName.'>/<n:[0-9A-Za-z-_]+>' => 'event/default/detail',
    		'event/default/(index|detail)/<archives:'.$archivesVarName.'>/*' => array('event/default/index', 'parsingOnly'=>true),
    		'event/default/(index|detail)/<n:[0-9A-Za-z-_]+>' => array('event/default/detail', 'parsingOnly'=>true),
    		'event/default/detail/<n:[0-9A-Za-z-_]+>' => 'event/default/detail',
    		'event/default/(index|detail)/*' => array('event/default/index', 'parsingOnly'=>true),
    	);
    }

    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                Yii::getPathOfAlias('event.assets'), false, -1, YII_DEBUG);
        return $this->_assetsUrl;
    }
}

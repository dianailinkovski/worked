<?php
/**
 * Contest module
 *
 * Manages contests, with customizable fields and presentation.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Module
 */

class ContestModule extends CmsModule
{
	public $notificationEmail; 
	public $archivesVarName; 
	
	public function init()
	{
		$this->setImport(array(
			'contest.models.*',
			'contest.components.*',
		));
	}
	
	public static function getAdminMenu()
	{
		return array(
			array('label'=>'{sectionName}', 'url'=>array('/contest/admin/admin'), 'icon'=>'edit')
		);
	}

	private $_assetsUrl;
 
    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                Yii::getPathOfAlias('contest.assets'), false, -1, YII_DEBUG);
        return $this->_assetsUrl;
    }
    
    public static function getUrlRules()
    {
    	$archivesVarName = Yii::app()->modules['contest']['archivesVarName'][Yii::app()->language];
    	
    	return array(
    		'contest/default/index/<archives:'.$archivesVarName.'>' => 'contest/default/index',
    		'contest/default/(index|detail)/<archives:'.$archivesVarName.'>/<n:[0-9A-Za-z-_]+>' => array('contest/default/detail', 'parsingOnly'=>true),
    		'contest/default/detail/<archives:'.$archivesVarName.'>/<n:[0-9A-Za-z-_]+>' => 'contest/default/detail',
    		'contest/default/(index|detail)/<archives:'.$archivesVarName.'>/*' => array('contest/default/index', 'parsingOnly'=>true),
    		'contest/default/(index|detail)/<n:[0-9A-Za-z-_]+>' => array('contest/default/detail', 'parsingOnly'=>true),
    		'contest/default/detail/<n:[0-9A-Za-z-_]+>' => 'contest/default/detail',
    		'contest/default/(index|detail)/*' => array('contest/default/index', 'parsingOnly'=>true),
    	);
    }
}

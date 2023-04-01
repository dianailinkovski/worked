<?php
/**
 * Job module
 *
 * Manages job postings with categories and possibility to post CV, has RSS feed
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Module
 */

class JobModule extends CmsModule
{
	public $cvEmail;

	private $_assetsUrl;

	
	public function init()
	{
		$this->setImport(array(
			'job.models.*',
			'job.components.*',
		));
	}

	public static function getAdminMenu()
	{
		return array(
			array('label'=>'{sectionName}', 'url'=>array('/job/admin/admin'), 'icon'=>'edit', 'subMenu' => array(
				array('label'=>Yii::t('jobModule.admin', 'Emplois'), 'url'=>array('/job/admin/admin')),
				array('label'=>Yii::t('jobModule.admin', 'Catégories'), 'url'=>array('/job/admincat/admin')),
				array('label'=>Yii::t('jobModule.admin', 'CV'), 'url'=>array('/job/admincv/admin')),
			))
		);
	}

    public static function getUrlRules()
    {
    	return array(
    		'job/default/(index|detail)/<t:[0-9A-Za-z-_]+>' => array('job/default/detail', 'parsingOnly'=>true),
    		'job/default/detail/<t:[0-9A-Za-z-_]+>' => 'job/default/detail',
    		'job/default/(index|detail)/*' => array('job/default/index', 'parsingOnly'=>true),
    	);
    }
    
    public function getAssetsUrl()
    {
    	if ($this->_assetsUrl === null)
    		$this->_assetsUrl = Yii::app()->getAssetManager()->publish(
    				Yii::getPathOfAlias('job.assets'), false, -1, YII_DEBUG);
    	return $this->_assetsUrl;
    }
}

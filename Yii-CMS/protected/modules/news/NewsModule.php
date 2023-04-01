<?php
/**
 * News module
 *
 * Manages news, module is instantiable, has RSS feed
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Module
 */

class NewsModule extends CmsModule
{
	public $instantiable = true;
	
	private $_assetsUrl;
	
	
	public function init()
	{
		$this->setImport(array(
			'news.models.*',
			'news.components.*',
		));
	}
	
	public static function getAdminMenu()
	{
		return array(
			array('label'=>'{sectionName}', 'url'=>array('/news/admin/admin'), 'icon'=>'newspaper-o')
		);
	}
	
    public static function getUrlRules()
    {
    	return array(
    		'news/default/(index|detail)/<n:[0-9A-Za-z-_]+>' => array('news/default/detail', 'parsingOnly'=>true),
    		'news/default/detail/<n:[0-9A-Za-z-_]+>' => 'news/default/detail',
    		'news/default/(index|detail)/*' => array('news/default/index', 'parsingOnly'=>true),
    	);
    }

    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                Yii::getPathOfAlias('news.assets'), false, -1, YII_DEBUG);
        return $this->_assetsUrl;
    }
}
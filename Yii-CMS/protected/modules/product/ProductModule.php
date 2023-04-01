<?php
/**
 * Product module
 *
 * Manages products
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Module
 */

class ProductModule extends CmsModule
{
	public $instantiable = false;
	
	private $_assetsUrl;
	
	
	public function init()
	{
		$this->setImport(array(
			'product.models.*',
			'product.components.*',
		));
	}
	
	public static function getAdminMenu()
	{
		return array(
			array('label'=>'{sectionName}', 'url'=>array('/product/admin/admin'), 'icon'=>'edit', 'subMenu' => array(
					array('label'=>'Produits', 'url'=>array('/product/admin/admin')),
					array('label'=>'Catégories', 'url'=>array('/product/admincategory/admin')),
					array('label'=>'Tags', 'url'=>array('/product/admintag/admin'))
				))
		);
	}

    public static function getUrlRules()
    {
    	return array(
    		'product/default/(category|listing|detail)/<c:[0-9A-Za-z-_]+>' => array('product/default/listing', 'parsingOnly'=>true),
    		'product/default/(category|listing|detail)/<c:[0-9A-Za-z-_]+>/<n:[0-9A-Za-z-_]+>' => array('product/default/detail', 'parsingOnly'=>true),
    		'product/default/listing/<c:[0-9A-Za-z-_]+>' => 'product/default/listing',
    		'product/default/detail/<c:[0-9A-Za-z-_]+>/<n:[0-9A-Za-z-_]+>' => 'product/default/detail',
    		'product/default/(category|listing|detail)/*' => array('product/default/category', 'parsingOnly'=>true),
    	);
    }

    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                Yii::getPathOfAlias('product.assets'), false, -1, YII_DEBUG);
        return $this->_assetsUrl;
    }
}
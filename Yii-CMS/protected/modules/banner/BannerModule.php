<?php
class BannerModule extends CmsModule
{
	private $_assetsUrl;
	
	
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'banner.models.*',
			'banner.components.*',
		));
	}
	
	public static function getAdminMenu()
	{
		return array(
			array('label'=>'{sectionName}', 'url'=>array('/banner/admin/admin'), 'icon'=>'edit')
		);
	}

    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                Yii::getPathOfAlias('banner.assets'), false, -1, YII_DEBUG);
        return $this->_assetsUrl;
    }
}

<?php
Yii::import('banner.models.*');
 
class BannerMessageWidget extends CWidget
{
    public function run()
    {
    	$model = Banner::model()->findByAttributes(array('active'=>1));

    	if ($model != null
    		&& (!isset(Yii::app()->request->cookies['closeBanner']) || Yii::app()->request->cookies['closeBanner'] != $model->id)
    		&& ($model->presence == 'all' || Yii::app()->controller->id.'/'.Yii::app()->controller->action->id == 'site/index')
    		)
    	{
	    	Yii::app()->clientScript->registerCssFile(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('banner.assets'), false, -1, YII_DEBUG).'/css/banner.css');
	    	Yii::app()->clientScript->registerCoreScript('jquery');

			Yii::app()->clientScript->registerScript('banner-message', "

			$(document).ready(function() {
			    $('#banner-message').fadeIn('slow');
			    $('#banner-message a.banner-message-close-notify').click(function() {
			        $('#banner-message').fadeOut('slow');

				    var d = new Date();
				    d.setTime(d.getTime() + (180*24*60*60*1000));
				    var expires = 'expires='+d.toUTCString();
				    document.cookie = 'closeBanner' + '=' + ".$model->id." + '; ' + expires;

			        return false;
			    });
			});", CClientScript::POS_READY);

	        $this->render('bannerMessageWidget', array(
	        	'model'=>$model,
	        ));
   		}
    }
}
?>
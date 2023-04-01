<?php
/**
 * Bloc flickr
 *
 * Show a gallery of photos pulled from Flickr.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Bloc
 */

class FlickrBloc extends CmsBloc
{
	private $_assetsUrl;


	public function afterAddItem()
	{
		return "
		$('select.blocFlickrUserId').filter(function(){
			if ($(this).parents('.tabularInputWidget:first').attr('id') != id
				|| $(this).parents('.tabularPortlet:first').attr('id') != id+'_'+itemId)
				return false;
			else
				return true;
		}).each(function(){
			$(this).change(function(){
				var setSelect = $(this).parent().next().find('select.blocFlickrSetId');
				$.post('".Yii::app()->controller->createUrl('/admin/blocflickrsets')."', {'user_id':$(this).val()}, function(data) {
					setSelect.children().remove();
					for (var key in data){
						setSelect.append($('<option>', { value: key, html: data[key] }));
					}
				});
			});
		});
		";
	}

	public function afterInit()
	{
		return "
		$('select.blocFlickrUserId').filter(function(){
			if ($(this).parents('.tabularInputWidget:first').attr('id') != id)
				return false;
			else
				return true;
		}).each(function(){
			var setSelect = $(this).parent().next().find('select.blocFlickrSetId');
			var setSelectHidden = $(this).parent().next().find('input.blocFlickrSetIdHidden');

			$.post('".Yii::app()->controller->createUrl('/admin/blocflickrsets')."', {'user_id':$(this).val()}, function(data) {
				setSelect.children().remove();
				for (var key in data){
					setSelect.append($('<option>', { value: key, html: data[key] }));
					setSelect.val(setSelectHidden.val());
				}
			});

			$(this).change(function(){
				var setSelect = $(this).parent().next().find('select.blocFlickrSetId');
				$.post('".Yii::app()->controller->createUrl('/admin/blocflickrsets')."', {'user_id':$(this).val()}, function(data) {
					setSelect.children().remove();
					for (var key in data){
						setSelect.append($('<option>', { value: key, html: data[key] }));
					}
				});
			});
		});
		";
	}

    public function getAssetsUrl()
    {
    	if ($this->_assetsUrl === null)
    		$this->_assetsUrl = Yii::app()->getAssetManager()->publish(
    			Yii::getPathOfAlias('application.components.blocs.flickr.assets'), false, -1, YII_DEBUG);
    	return $this->_assetsUrl;
    }
}
<?php
/**
 * Bloc cloud document
 *
 * List contents of a folder from a cloud hosting source.
 *
 * Currently works only with Dropbox. 
 * Uses kloudless picker to choose a folder on the admin side and uses the Dropbox Api to list documents on the front-end side.
 *
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Bloc
 */

class ClouddocumentBloc extends CmsBloc
{
	private $_assetsUrl;


	/**
	* Initialize kloudless with the config param
	*/
	public function __construct()
	{
		Yii::app()->clientScript->registerScriptFile('https://static-cdn.kloudless.com/p/platform/sdk/kloudless.explorer.js', CClientScript::POS_HEAD);
		
		Yii::app()->clientScript->registerScript('kloudlessInit', "
		var kloudlessExplorer = window.Kloudless.explorer({
		    app_id: '".Yii::app()->params['kloudlessApiId']."',
		    multiselect: false,
		    computer: true,
		    link: true,
		    services: ['dropbox'],
		    types: ['folders']
		});

		kloudlessExplorer.on('success', function (files) {
		    if (typeof files[0]['path'] == 'string') {
				$(kloudlessExplorerPathField).val(files[0]['path']);
			}
		});
		var kloudlessExplorerPathField;
		", CClientScript::POS_READY);
	}

	/**
	* Bind button and set proper path for kloudless explorer
	* @return string the javascript
	*/
	public function afterAddItem()
	{
		return "
		$('.kloudlessChoosify').filter(function(){
			if ($(this).parents('.tabularInputWidget:first').attr('id') != id)
				return false;
			else
				return true;
		}).each(function(){
			$(this).click(function(){
				kloudlessExplorer.choosify(document.getElementById($(this).attr('id')));
				kloudlessExplorerPathField = $(this).next().children('input');
			});
			$(this).trigger('click'); // fixes a bug
		});
		";
	}

    public function getAssetsUrl()
    {
    	if ($this->_assetsUrl === null)
    		$this->_assetsUrl = Yii::app()->getAssetManager()->publish(
    			Yii::getPathOfAlias('application.components.blocs.clouddocument.assets'), false, -1, YII_DEBUG);
    	return $this->_assetsUrl;
    }
}
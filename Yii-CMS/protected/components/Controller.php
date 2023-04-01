<?php
/**
 * The controller the front-end controllers extend from
 * 
 * Global operations for front-end controllers.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Controller
 */

class Controller extends CController
{
	/**
	 * @var array the breadcrumbs items
	 * @see CBreadcrumbs::$links
	 */
	public $breadcrumbs = array();
	/**
	 * @var string the separator used inbetween title items
	 * @see Helper::titleFromBreadcrumbs()
	 */
	public $titleSeparator = ' | ';
	/**
	 * @var string the view file path rendered on the left side of column2 layout
	 */
	public $sidebarViewFile = '//layouts/_defaultSidebar';
	/**
	 * @var array the data passed to the view file rendered on the left side of column2 layout
	 */
	public $sidebarData = array();
	/**
	 * @var string if the rightColumn layout is used, this will determine its parent view file path (usually column1 or column2)
	 */
	public $rightColumnLayoutParent = '';
	/**
	 * @var string if the rightColumn layout is used, this will determine its content view file path
	 */
	public $rightColumnLayoutType = '';
	/**
	 * @var array the data passed to the view file in rightColumn layout
	 */
	public $rightColumnLayoutTypeData = array();

	
	public function init() 
	{
		$app = Yii::App();

		// Register dropbox autoload
		spl_autoload_unregister(array('YiiBase','autoload'));
		require_once 'protected/vendors/dropbox-sdk-php/lib/Dropbox/autoload.php';
		spl_autoload_register(array('YiiBase','autoload'));
		
		// There are two WebUser to not have conflicts, one for the front-end and one for the back-end, this sets it to the front-end
		if (isset(Yii::app()->userFront))
			Yii::app()->setComponent('user', Yii::app()->userFront);

		// Register JS and CSS files
		$app->clientScript->registerCoreScript('jquery');
		$app->clientScript->registerScriptFile('/javascript/bootstrap.min.js', CClientScript::POS_HEAD);
		$app->clientScript->registerScriptFile('/javascript/ie10-viewport-bug-workaround.js', CClientScript::POS_HEAD);
		$app->clientScript->registerScriptFile('/javascript/global.js', CClientScript::POS_HEAD);

		// Makes it so it throws and error if an alias exist for the current route and hasn't been used
		$app->urlManager->forceAliases();
	}
}

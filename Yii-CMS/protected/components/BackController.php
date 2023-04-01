<?php
Yii::import('application.helpers.AdminHelper');
Yii::import('application.helpers.CArray'); // For image extension

/**
 * The controller the back-end controllers extend from.
 *
 * Global operations for back-end controllers.
 *
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Controller
 */
class BackController extends CController
{
	public $layout='//adminLayouts/standard';
	/**
	 * @var array the side menu items
	 * @see CMenu::$items
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs items
	 * @see CBreadcrumbs::$links
	 */
	public $breadcrumbs = array();
	/**
	 * @var string the page title displayed in the page
	 */	
	public $title;
	/**
	 * @var array the main menu items
	 * @see CMenu::$items
	 */
	public $mainMenu = array();
	/**
	 * @var array config for the ckeditor shared across the pages
	 */
	public $ckEditorConfig;
	/**
	 * @var array config for the ckeditor (simple version) shared across the pages
	 */
	public $ckEditorConfigSimple;
	/**
	 * @var string path to the ckeditor library file
	 */
	public $ckeditor;
	/**
	 * @var string path to the ckeditor library folder
	 */
	public $ckBasePath;
	/**
	 * @var Google_Client the google client object
	 */
	public $googleClient;
	/**
	 * @var array tabs to show
	 * @see CmsModule::getAdminTabs()
	 */
	public $tabs;


	public function init()
	{
		$app = Yii::app();

		// register dropbox autoload
		/*
		spl_autoload_unregister(array('YiiBase','autoload'));
		require_once 'protected/vendors/dropbox-sdk-php/lib/Dropbox/autoload.php';
		spl_autoload_register(array('YiiBase','autoload'));
		*/
		$app->languageManager->restrictLanguage(Yii::app()->sourceLanguage);

		$app->urlManager->cancelAliasResolution = true;

		$app->user->loginUrl = array('/admin/login');

		// There are two WebUser to not have conflicts, one for the front-end and one for the back-end, this sets it to the back-end
		Yii::app()->setComponent('user', Yii::app()->userBack);

		// Checking access
		if (isset($this->module->id) && in_array($this->module->id, $app->cms->modules))
		{
			if ($this->module->instantiable == true && isset($_GET['section_id']))
			{
				if (!($app->cms->currentSectionId = CmsSection::model()->findByPk($_GET['section_id'])->id))
					throw new CHttpException(404,'The requested section does not exist.');

				if (!$app->user->checkAccess('adminSectionId-'.$app->cms->currentSectionId))
					$app->user->loginRequired();
			}
			else
				if (!$app->user->checkAccess('adminModule-'.strtolower(substr(get_class($this->module), 0, strrpos(get_class($this->module), 'Module')))))
					$app->user->loginRequired();
		}

		// Setting up ckeditor
		$ckContentCss = array('/css/admin/ck-bootstrap.css', '/css/framework.css', '/css/project.css', '/css/blocs.css', '/css/admin/ckeditor.css');
		if (isset(Yii::app()->params['googleFont']))
			array_unshift($ckContentCss, Yii::app()->params['googleFont']);

        $this->ckeditor = 'vendors/ckeditor/ckeditor.php';
        $this->ckBasePath = Yii::app()->baseUrl.'/vendors/ckeditor/';
        $this->ckEditorConfig = array(
            'width'=>'100%',
            'height'=>'400px',
            'language' => $app->language,
            'uiColor'=>'#cae0ec',
            'bodyId' => 'content',
            'bodyClass' => 'ckeditor section-bloc bloc-editor',
            'forcePasteAsPlainText' => true,
            'disableReadonlyStyling' => true,
            'startupShowBorders' => false,
            'resize_enabled' => true,
            'filebrowserUploadUrl' => $this->createUrl('/admin/ckeditorupload', array('type'=>'file')),
            'filebrowserImageUploadUrl' => $this->createUrl('/admin/ckeditorupload', array('type'=>'image')),
            'contentsCss' => $ckContentCss,
            'format_tags' => 'p;h2;h3;h4',
            'stylesSet' => 'my_styles:/javascript/ckstyles.js',
            'toolbar' => 'Full',
            'toolbar_Full' => array(
                array('Bold','Italic','Strike','Subscript','Superscript'),
                array('NumberedList','BulletedList','Outdent','Indent','Table'),
                array('Link','Unlink','Anchor','Image','MediaEmbed'),
                array('Format','Styles'),
                array('Cut','Copy','PasteText'),
                array('Undo','Redo','-','SelectAll','RemoveFormat','Source')
            ),
        );
        $this->ckEditorConfigSimple = array(
            'width'=>'100%',
            'height'=>'200px',
            'uiColor'=>'#cae0ec',
            'language' => $app->language,
            'bodyId' => 'content',
            'bodyClass' => 'ckeditor section-bloc bloc-editor',
            'forcePasteAsPlainText' => true,
            'disableReadonlyStyling' => true,
            'startupShowBorders' => false,
            'resize_enabled' => true,
            'contentsCss' => $ckContentCss,
            'format_tags' => 'p;h2;h3;h4',
            'stylesSet' => 'my_styles:/javascript/ckstyles.js',
            'toolbar' => 'Full',
            'toolbar_Full' => array(
				array('Bold','Italic','Strike','Subscript','Superscript'),
				array('Link','Unlink'),
				array('Undo','Redo')
			),
		);

		// Loading up js libraries
		//$app->clientScript->registerCoreScript('jquery');
		//$app->clientScript->registerCoreScript('jquery.ui');
		//$app->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/jqueryui/backTheme/jquery-ui.css');
		//$app->clientScript->registerScript('jqueryDatepickerLanguage', "$.datepicker.setDefaults($.datepicker.regional['".Yii::app()->language."']);", CClientScript::POS_READY);
		//$app->clientScript->registerScriptFile($app->clientScript->getCoreScriptUrl().'/jui/js/jquery-ui-i18n.min.js');

		// Styling form buttons with jQuery
		/*
		$app->clientScript->registerScript('inputButtons', "
			$('input:submit, input:button').button();
		", CClientScript::POS_READY);
		*/
		// Setting up the menu
		foreach (CmsSection::model()->findAll() as $section)
		{
			if (in_array($section->module, $app->cms->modules))
			{
				$className = $section->module.'Module';
				$moduleMenu = $className::getAdminMenu();

				foreach ($moduleMenu as $moduleMenuItem)
				{
					$moduleMenuItem['label'] = str_replace('{sectionName}', $section->name, $moduleMenuItem['label']);
					$moduleMenuItem['url']['section_id'] = $section->id;
					$moduleMenuItem['icon'] = $moduleMenuItem['icon'];

					if (isset($moduleMenuItem['subMenu']))
					{
						foreach ($moduleMenuItem['subMenu'] as $key => $moduleSubMenuItem)
						{
							$moduleMenuItem['subMenu'][$key]['url']['section_id'] = $section->id;
						}
					}
					$this->mainMenu[] = $moduleMenuItem;
				}
			}
		}
		
		return parent::init();
	}

	/*
	 * Not allowing guests
	 */
	public function beforeAction($action)
	{
		$app = Yii::app();
		if ($app->userBack->isGuest && $app->controller->route != 'admin' && $app->controller->action->id != 'login')
			$app->user->loginRequired();

		return parent::beforeAction($action);
	}
}

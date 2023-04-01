<?php
Yii::import('application.components.behaviors.UploadingBehavior.UploadingBehavior');

/**
 * CModel version of uploading behavior.
 * 
 * Manage file uploads, supports modifying/deleting the file, image resizing, multiple image sizes, preview of image, upload care extension.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * 
 * Required extension for image processing : http://www.yiiframework.com/extension/image 
 * Set it up in the config file like it says in the documentation.
 * 
 * Optional uploadcare extension : https://uploadcare.com/
 * For nice upload widget. Watch the quotas. CDN Storage is not used.
 * 
 * When form is sent, it sends the file to a temporary location and shows a preview (if $formats and $showPreviewImage are set) 
 * and a checkbox to delete the file (if $allowDelete is true).
 * 
 * If the model validates, it then sends the file to the real folder and deletes the temp file. 
 * If the user closes the browser or a problem occurs, the temp file will be deleted after a certain period of time (see $cacheTime).
 * 
 * Uses CUploadedFile for handling the file unless uploadcare is used, then it will use the widget to upload the file to uploadcare's
 * server and then download it from there to the local temp folder.
 * 
 * If you use the formats parameter (for images only) it will create files with the sizes and suffixes you want plus another image 
 * for the preview image that is shown in the form (if $showPreviewImage is true) with a suffix of '_p' by default.
 *  
 * All you need to do to make it work is attach the behavior to a CModel (and set up the validation rules), 
 * fill the delete and tempName variables in your controller and call the makeField function in your view file.
 * 
 * Don't forget to set your form's enctype to "multipart/form-data".
 * 
 * You can set default (overridable) settings by defining a "uploadingBehavior" parameter in the application's parameters.
 * Useful to turn uploadcare on/off for the whole application.
 * 
 * If you use uploadcare, you'll probably want to set the $uploadcarePath parameter.
 * 
 * Here is an example :
 * 
 * Attach the behavior to a CModel :
 * 
 * <pre>
 * public function behaviors() 
 * {
 * 	    return array(
 * 	        'imageHandler' => array(
 * 	        	'class' => 'application.components.behaviors.UploadingBehavior.ModelUploadingBehavior',
 * 	        	'attribute' => 'image',
 * 				'dir' => 'files/_user/news',
 * 				'tempDir' => 'files/_user/news/_temp',
 * 				'formats' => array(
 * 					's'=>array(350, 263),
 * 					'm'=>array(555, 416),
 * 					'l'=>array(700, 525),
 * 				)
 * 	        )
 * 	    );
 * }
 * </pre>
 * 
 * Validation rules : 
 * 
 * <pre>
 * array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty'=>true),
 * array('image', 'length', 'max'=>255),
 * </pre>
 * 
 * Always set allowEmpty to true. If you want the field to be required, add a required validation instead and set $allowDelete to false.
 * 
 * Fill the tempName and delete variables in the controller (if allowDelete is true). Usually done after filling the model's attributes :
 * 
 * <pre>
 * $model->imageHandler->delete = isset($_POST['News']['imageHandler']['delete']) ? $_POST['News']['imageHandler']['delete'] : 0;
 * $model->imageHandler->tempName = isset($_POST['News']['imageHandler']['tempName']) ? $_POST['News']['imageHandler']['tempName'] : '';
 * </pre>
 * 
 * Render the field in the view file (inside a form) :
 * 
 * <pre>
 * $model->imageHandler->makeField($form);
 * </pre>
 * 
 * Where $form is a CActiveForm. If omitted it will use CHtml::activeFileField().
 * 
 * @see UploadingBehavior
 * 
 * @package Behavior
 */
class ModelUploadingBehavior extends CModelBehavior
{
	/**
	 * @var string the directory where to upload. Required.
	 */
	public $dir;
	/**
	 * @var string the directory where to put temporary files. Required.
	 */
	public $tempDir;
	/**
	 * @var string the model attribute to use. Required.
	 */
	public $attribute;
	/**
	 * @var array extra directories (other than dir and tempDir) that you want created during upload if they don't exist (such as parent directories).
	 */
	public $mkdir=array();
	/**
	 * @var int time in seconds for which the temp files are kept (they are deleted at the next upload operation after this time period).
	 * Defaults to 10 days.
	 */
	public $cacheTime = 864000;
	/**
	 * @var array the formats of images to create. For images only.
	 * Example : 'formats' => array(
	 * 			  's'=>array(350, 263),
	 * 			  'm'=>array(555, 416),
	 * 			  'l'=>array(700, 525),
	 * 		  )
	 */
	public $formats;
	/**
	 * @var boolean whether to upscale images or not. For images only.
	 * Defaults to false.
	 */
	public $onlyResizeIfBigger=false;
	/**
	 * @var boolean this attribute must be set in the controller if the file must be deleted and allowDelete is true (see example).
	 */
	public $delete;
	/**
	 * @var boolean whether or not to allow deleting the file.
	 */
	public $allowDelete;
	/**
	 * @var string this parameter must be set in the controller and is used by the widget (see example).
	 */
	public $tempName;
	/**
	 * @var string if your POST name for the attribute is not standard, set it here. For arrays the format is [key1][key2][keyn...]attributeName, no class name.
	 */
	public $attributePostName;
	/**
	 * @var array the image size of the preview image. For images only.
	 * Example : array(460, 460).
	 * Defaults to 460x460.
	 */
	public $previewImageSize=array(460, 460);
	/**
	 * @var boolean whether or not to show a preview image. For images only.
	 * Defaults to true.
	 */
	public $showPreviewImage=true;
	/**
	 * @var string the suffix to give the preview image file. For images only.
	 * Defaults to "p".
	 */
	public $previewImageSuffix='p';
	/**
	 * @var array the uploadcare keys. 
	 * If set the behavior will use upload care.
	 * Example : 'uploadcare' => array(
	 * 	   			   'publicKey' => 'XXXXXXXXX',
     * 	   			   'privateKey' => 'XXXXXXXXX',
	 * 			  )
	 * @see https://uploadcare.com/
	 */
	public $uploadcare;
	/**
	 * @var string the path to upload care classes.
	 */
	public $uploadcarePath='protected/vendors/uploadcare-php/src/Uploadcare';
	
	/**
	 * @var UploadBehavior the UploadBehavior used.
	 */
	private $_component;
	

	/**
	 * @see UploadingBehavior.
	 */
	public function __construct()
	{
		$this->_component = new UploadingBehavior($this);
	}
	
	/**
	 * @see UploadingBehavior.
	 */
	public function attach($owner)
	{
		parent::attach($owner);
		
		$this->_component->attach();
	}
	
	/**
	 * @see UploadingBehavior.
	 */
	public function beforeValidate($event)
	{
		$this->_component->beforeValidate();
	}
	
	/**
	 * @see UploadingBehavior.
	 */
	public function afterValidate($event)
	{
		$owner = $this->owner;

		if (!$owner->hasErrors())
			$this->_component->afterValidate();
			
		$this->_component->cleanTempFiles();
	}
	
	/**
	 * @see UploadingBehavior.
	 */
	public function makeField($form=null, $attributePostName=null, $fileFieldHtmlOptions=array(), $checkboxHtmlOptions=array(), $previewImageHtmlOptions=array())
	{
		return $this->_component->makeField($form, $attributePostName, $fileFieldHtmlOptions, $checkboxHtmlOptions, $previewImageHtmlOptions);
	}
}
<?php
use \Uploadcare;

/**
 * The common class that ActiveRecordUploadingBehavior and ModelUploadingBehavior use.
 * 
 * This is where the actual uploading operations are done.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Behavior
 * 
 * @see ActiveRecordUploadingBehavior
 * @see ModelUploadingBehavior
 */
class UploadingBehavior extends CComponent
{
	/**
	 *	@var int current time unix timestamp.
	 */
	private $_currentTime;
	/**
	 *	@var CList the model's validators.
	 */
	private $_modelValidators;
	/**
	 *	@var Uploadcare\Api the upload care API instance.
	 */
	private $_uploadcareApi;
	/**
	 *	@var CBehavior the actual behavior attached to the model (ActiveRecordUploadingBehavior or ModelUploadingBehavior).
	 */
	private $_behavior;
	/**
	 *	@var array the file validators of owner.
	 */
	private $_fileValidators=array();
	
	
	/**
	 *	Initiating behavior and getting global paramters from application's params.
	 *	@var ActiveRecordUploadingBehavior|ModelUploadingBehavior the behavior attached to the model that uses this class.
	 */
	public function __construct($behavior)
	{
		$this->_behavior = $behavior;
		
		if (isset(Yii::app()->params['uploadingBehavior']))
		{
			$globalParams = Yii::app()->params['uploadingBehavior'];
			foreach ($globalParams as $key => $value)
			{
				$behavior->$key = $value;
			}
		}
	}
	
	public function attach()
	{
		$behavior = $this->_behavior;
		$attribute = $behavior->attribute;
		$owner = $behavior->owner;
		$this->_currentTime = time();
		$this->_modelValidators = $owner->getValidatorList();

		// Initiating uploadcare.
		if ($behavior->uploadcare !== null) 
		{
			$behavior->uploadcarePath = rtrim($behavior->uploadcarePath, '/').'/';
			require_once $behavior->uploadcarePath.'Api.php';
			require_once $behavior->uploadcarePath.'Widget.php';
			require_once $behavior->uploadcarePath.'Uploader.php';
			require_once $behavior->uploadcarePath.'File.php';

			$this->_uploadcareApi = new Uploadcare\Api($behavior->uploadcare['publicKey'], $behavior->uploadcare['privateKey']);

			// Removing file validations because for some reason it changes the value of the attribute between here and beforeValidate (we re-add them later).
			$indexesToRemove = array();
			foreach ($this->_modelValidators as $validator)
			{
				if ($validator instanceof CFileValidator)
				{
					if (in_array($behavior->attribute, $validator->attributes))
					{
						$this->_fileValidators[] = $validator;
						$indexesToRemove[] = $this->_modelValidators->indexOf($validator);
					}
				}
			}
			foreach ($indexesToRemove as $indexToRemove)
			{
				$this->_modelValidators->removeAt($indexToRemove);
			}
		}

		if (!$behavior->attributePostName)
			$behavior->attributePostName = $behavior->attribute;
		
		if (!isset($behavior->formats))
			$behavior->showPreviewImage = false;
			
		// allowDelete true or false if attribute is required.
		if (!isset($behavior->allowDelete))
		{
			$behavior->allowDelete = true;
			
			foreach ($this->_modelValidators as $validator)
			{
				if ($validator instanceof CRequiredValidator)
				{
					if (in_array($behavior->attribute, $validator->attributes))
					{
						$behavior->allowDelete = false;
						break;
					}
				}
			}
		}
	}
	
	public function beforeValidate()
	{
		$behavior = $this->_behavior;
		$owner = $behavior->owner;
		$ownerClass = get_class($owner);
		$attribute = $behavior->attribute;

		if (!$behavior->delete)
		{
			// Delete command not given, getting uploaded file.
			if ($behavior->uploadcare !== null)
			{
				if ($owner->$attribute != '')
				{
					$fileId = $owner->$attribute;
	               	$file = $this->_uploadcareApi->getFile($fileId);
	               	$fileUrl = $file->getUrl();
	               	$uploadedFile = $file->data['original_filename'];
				}
				else {
					if (get_parent_class($behavior) == 'CActiveRecordBehavior' && !$owner->isNewRecord)
						$owner->$attribute = $ownerClass::model()->findByPk($owner->primaryKey)->$attribute;
					
					$uploadedFile = '';
				}
			}
			else {
				if (($uploadedFile = CUploadedFile::getInstance($owner, $behavior->attributePostName)) === null && !$owner->isNewRecord)
					$owner->$attribute = $ownerClass::model()->findByPk($owner->primaryKey)->$attribute;
			}

			if (($behavior->uploadcare === null && is_object($uploadedFile)) 
				|| ($behavior->uploadcare !== null && !empty($uploadedFile)))
			{
				// A file has been uploaded. Creating destination folders if they don't exist and getting a unique name for the file.

				foreach ($behavior->mkdir as $dir) 
				{
					if (!is_dir($dir))
						mkdir($dir);
				}
				if (!is_dir($behavior->dir))
					mkdir($behavior->dir);
				if (!is_dir($behavior->tempDir))
					mkdir($behavior->tempDir);
					
				$fileNameClean = strtolower(preg_replace('/[^A-Za-z0-9_\-\.]|\.(?=.*\.)/', '', str_replace(' ', '_', $this->removeAccents($uploadedFile))));
				if (strpos($fileNameClean, '.') === 0)
					$fileNameClean = 'file'.$fileNameClean;

				$i = -1;
				while (true)
				{
					$i++;
					if ($i > 0)
						$fileName = $this->fileSuffix($fileNameClean, $i);
					else
						$fileName = $fileNameClean;

					$fileTempName = $this->_currentTime.'_'.$fileName;

					if (file_exists($behavior->dir.'/'.$fileName))
						continue;
					
					$glob = glob($behavior->tempDir.'/*'.$fileName);
					if (!empty($glob))
						continue;

					if (isset($behavior->formats))
					{
						if (file_exists($behavior->dir.'/'.$this->fileSuffix($fileName, $behavior->previewImageSuffix)))
							continue;
						
						$continue = false;
						foreach ($behavior->formats as $format => $dimensions)
						{
							if (file_exists($behavior->dir.'/'.$this->fileSuffix($fileName, $format)))
							{
								$continue = true;
								break;
							}
						}
						if ($continue)
							continue;
					}
					break;
				}

               	// Sending file to temporary folder regardless of validation (we need it for preview and other things).
               	if ($behavior->uploadcare !== null) 
               	{
               		// Manually wrapping the file in CUploadedFile object to allow for validations to occur.
               		$bytes = file_put_contents($behavior->tempDir.'/'.$fileTempName, file_get_contents($fileUrl));
               		$finfo = new finfo;
					$mimeType = $finfo->file($behavior->tempDir.'/'.$fileTempName, FILEINFO_MIME);
 					$uploadedFile = new CUploadedFile($fileTempName, $behavior->tempDir.'/'.$fileTempName, $mimeType, $bytes, UPLOAD_ERR_OK);

					// Re-adding validators that we removed before for bugfix.
					foreach ($this->_fileValidators as $validator)
						$owner->validatorList->add($validator);
 				}
               	else
					$uploadedFile->saveAs($behavior->tempDir.'/'.$fileTempName);

				$owner->$attribute = $uploadedFile; // Must set uploadedFile and not string of file name otherwis:e the file type validation bugs out.

               	$behavior->tempName = $fileTempName;

				if (isset($behavior->formats))
				{	
					try {
						// Trying to make preview thumbnail (because we must show it even if there is an error).
						$image = Yii::app()->image->load($behavior->tempDir.'/'.$fileTempName);

						$imageSize = getimagesize($behavior->tempDir.'/'.$fileTempName);

						if (!$behavior->onlyResizeIfBigger || $imageSize[0] > $behavior->previewImageSize[0] || $imageSize[1] > $behavior->previewImageSize[1])
							$image->resize($behavior->previewImageSize[0], $behavior->previewImageSize[1])->quality(90)->sharpen(15);
						
						$image->save($behavior->tempDir.'/'.$this->fileSuffix($fileTempName, $behavior->previewImageSuffix));
					}
					catch (Exception $e) {
						$behavior->showPreviewImage = false; // Making of preview failed probably wrong file format, don't show it.
					}
				}
			}
			elseif ($behavior->tempName != '')
				$owner->$attribute = $behavior->tempName; // No file uploaded. Re-using tempName if present (image was uploaded previously).
		}
		elseif (get_parent_class($behavior) == 'CActiveRecordBehavior' && $behavior->uploadcare !== null && !$owner->isNewRecord && $owner->$attribute == '')
			$owner->$attribute = $ownerClass::model()->findByPk($owner->primaryKey)->$attribute; // With uploadcare, we must re-set the attribute if deleting or it bugs.
	}

	public function afterValidate()
	{
		$behavior = $this->_behavior;
		$owner = $behavior->owner;
		$ownerClass = get_class($owner);
		$attribute = $behavior->attribute;

		if (get_parent_class($behavior) == 'CActiveRecordBehavior' && $behavior->uploadcare === null && !$owner->isNewRecord && $owner->$attribute == '')
			$owner->$attribute = $ownerClass::model()->findByPk($owner->primaryKey)->$attribute; // Without uploadcare, we must re-set the attribute here or it will be erased by the file validator.

		if ($behavior->delete)
		{
			// Delete command given and model validated so we delete the files.
			if ($owner->$attribute != '')
			{
				if (isset($behavior->formats))
				{
					foreach ($behavior->formats as $format => $dimensions)
						if (file_exists(($file = $behavior->dir.'/'.$this->fileSuffix($owner->$attribute, $format))))
							unlink($file);
				
					if (file_exists(($file = $behavior->dir.'/'.$this->fileSuffix($owner->$attribute, $behavior->previewImageSuffix))))
						unlink($file);
				}
				else {
					if (file_exists(($file = $behavior->dir.'/'.$owner->$attribute)))
						unlink($file);
				}
			}
			if ($behavior->tempName != '')
			{
				if (isset($behavior->formats))
				{
					if (file_exists(($file = $behavior->tempDir.'/'.$this->fileSuffix($behavior->tempName, $behavior->previewImageSuffix))))
						unlink($file);
				}
				if (file_exists(($file = $behavior->tempDir.'/'.$behavior->tempName)))
					unlink($file);
			}
				
			$owner->$attribute = '';
		}
		elseif ($behavior->tempName != '')
		{
			// tempName present, meaning a new file was uploaded and validated so we move files and create new image sizes.
			if (file_exists($behavior->tempDir.'/'.$behavior->tempName))
			{
				$fileName = substr($behavior->tempName, strpos($behavior->tempName, '_')+1);
				
				if (isset($behavior->formats))
				{
					$image = Yii::app()->image->load($behavior->tempDir.'/'.$behavior->tempName);
					$imageSize = getimagesize($behavior->tempDir.'/'.$behavior->tempName);
					
					foreach ($behavior->formats as $format => $dimensions)
					{
						if (!$behavior->onlyResizeIfBigger || $imageSize[0] > $dimensions[0] || $imageSize[1] > $dimensions[1])
							$image->resize($dimensions[0], $dimensions[1])->quality(90)->sharpen(15);

						$image->save($behavior->dir.'/'.$this->fileSuffix($fileName, $format));
					}
					rename($behavior->tempDir.'/'.$this->fileSuffix($behavior->tempName, $behavior->previewImageSuffix), $behavior->dir.'/'.$this->fileSuffix($fileName, $behavior->previewImageSuffix));
					unlink($behavior->tempDir.'/'.$behavior->tempName);
				}
				else
					rename($behavior->tempDir.'/'.$behavior->tempName, $behavior->dir.'/'.$fileName);

				// Deleting old files if we're updating an already existing file.
				if (get_parent_class($behavior) == 'CActiveRecordBehavior' && !$owner->isNewRecord && (($oldFile = $owner->findByPk($owner->primaryKey)->$attribute) != ''))
				{
					if (isset($behavior->formats))
					{
						foreach ($behavior->formats as $format => $dimensions)
							if (file_exists(($file = $behavior->dir.'/'.$this->fileSuffix($oldFile, $format))))
								unlink($file);
								
						if (file_exists(($file = $behavior->dir.'/'.$this->fileSuffix($oldFile, $behavior->previewImageSuffix))))
							unlink($file);
					}
					else {
						if (file_exists(($file = $behavior->dir.'/'.$oldFile)))
							unlink($file);
					}
				}
				$owner->$attribute = $fileName; // Setting the actual attribute with the file name.
			}
			else
				$owner->$attribute = ''; // Just in case it was cleared by someone else (shouldn't happen).
		}
	}
	
	/**
	 * Clean temp files if $cacheTime is passed.
	 */
	public function cleanTempFiles()
	{
		$behavior = $this->_behavior;
		$owner = $behavior->owner;
		
		foreach (glob($behavior->tempDir.'/*.*') as $file)
		{
			$fileName = substr($file, strrpos($file, '/')+1);
			if ((int)substr($fileName, 0, strpos($fileName, '_')) - $this->_currentTime + $behavior->cacheTime < 0)
				unlink($file);
		}
	}
	
	/**
	 * Echos or returns the file field, temp name hidden field, the delete checkbox and preview image and set up the uploadcare widget if used.
	 * 
	 * @param CActiveForm $form the form in which the widget is called. Can be null if CActiveForm is not used. Defaults to null in parent class.
	 * 
	 * @param boolean $return whether to return the html or just echo it. Defaults to false in parent class.
	 * 
	 * @param string $attributePostName the fields name if non standard. Such as if using array notation. The format is [key1][key2][keyn...]attributeName, no class name. Defaults to null in parent class.
	 * 
	 * @param array $fileFieldHtmlOptions html options for file field. Defaults to empty array in parent class.
	 * 
	 * @param array $checkboxHtmlOptions html options for checkbox field. Defaults to empty array in parent class.
	 * 
	 * @param array $previewImageHtmlOptions html options for preview image. Defaults to empty array in parent class.
	 * 
	 * @return string the html.
	 */
	public function makeField($form, $attributePostName, $fileFieldHtmlOptions, $checkboxHtmlOptions, $previewImageHtmlOptions)
	{
		$behavior = $this->_behavior;
		$owner = $behavior->owner;
		$attribute = $behavior->attribute;
		$ownerClass = get_class($owner);
		$html = '';

		if (isset($attributePostName))
			$postAttributeArrays = substr($attributePostName, 0, strrpos($attributePostName, ']')+1);
		else
			$postAttributeArrays = '';
		
		foreach ($owner->behaviors() as $ownerBehaviorName => $ownerBehaviorParams)
		{
			if (($ownerBehaviorParams['class'] == 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior' 
				|| $ownerBehaviorParams['class'] == 'application.components.behaviors.UploadingBehavior.ModelUploadingBehavior')
				&& $ownerBehaviorParams['attribute'] == $behavior->attribute)
			{
				$behaviorName = $ownerBehaviorName;
				break;
			}
		}
		
		$html .= '<div class="uploadFieldWrap">';
		if ($owner->$attribute != '')
		{
			if ($behavior->showPreviewImage)
			{
				$html .= CHtml::image(Yii::app()->request->baseUrl.'/'
					.($owner->$behaviorName->tempName != '' ? 
					$owner->$behaviorName->tempDir.'/'.$this->fileSuffix($owner->$behaviorName->tempName, $behavior->previewImageSuffix) 
					: $owner->$behaviorName->dir.'/'.$this->fileSuffix($owner->$attribute, $behavior->previewImageSuffix)
					), '', $previewImageHtmlOptions);
			}
			if ($behavior->allowDelete)
			{
				$html .= CHtml::checkbox($ownerClass.$postAttributeArrays.'['.$behaviorName.'][delete]', $behavior->delete, $checkboxHtmlOptions)
					.'<span>'.Yii::t('admin', 'Supprimer ou').'&nbsp;&nbsp;</span>';
			}
			$html .= CHtml::hiddenField($ownerClass.$postAttributeArrays.'['.$behaviorName.'][tempName]', $behavior->tempName);
		}
		if ($behavior->uploadcare !== null)
		{
			Yii::app()->clientScript->registerScript('UploadingBehaviorUploadCarePublicKey', "UPLOADCARE_PUBLIC_KEY = '".$this->_uploadcareApi->getPublicKey()."'; UPLOADCARE_LOCALE = '".(isset($behavior->uploadcare['language']) ? $behavior->uploadcare['language'] : Yii::app()->language)."';", CClientScript::POS_HEAD);
			Yii::app()->clientScript->registerScriptFile($this->_uploadcareApi->widget->getScriptSrc(), CClientScript::POS_HEAD);

			$options = array('role' => 'uploadcare-uploader');
			$uploadcareArr = $behavior->uploadcare;
			unset($uploadcareArr['publicKey']);
			unset($uploadcareArr['privateKey']);
			unset($uploadcareArr['language']);
			$options = array_merge($options, $uploadcareArr);

			if (!isset($options['data-images-only']) && isset($behavior->formats))
				$options['data-images-only'] = 'true';

			if (!isset($options['data-crop']) && isset($behavior->formats))
				$options['data-crop'] = 'true';
			
			$html .= CHtml::hiddenField($ownerClass.$postAttributeArrays.'['.$attribute.']', '', $options);
		}
		else if ($form !== null)
			$html .= $form->fileField($owner, $postAttributeArrays.$attribute, $fileFieldHtmlOptions);
		else
			$html .= CHtml::activeFileField($owner, $postAttributeArrays.$attribute, $fileFieldHtmlOptions);
		
		$html .= '</div>';

		return $html;
	}
	
	/**
	 * Add a suffix to a file (following an underscore, before the file extension).
	 * 
	 * @param $name string the name of the file.
	 * @param $suffix string the wanted suffix.
	 * 
	 * @return string the modified file name.
	 */
    private function fileSuffix($name, $suffix)
    {
    	return preg_replace('/^(.+)(\.\w+)$/u', '$1_'.$suffix.'$2', $name);
    }
    
	/**
	 * Replace accentuated characters with their non accentuated version.
	 * 
	 * Note that this might not catch everything and if you need clean the string of all special characters you still need to filter it.
	 * 
	 * @param string $str string to operate on.
	 * 
	 * @return string the string without accents.
	 */
	private function removeAccents($str) 
	{
	    $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
	    $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	    return str_replace($a, $b, $str);
	}
}
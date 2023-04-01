<?php
/**
 * Use an attribute to generate another filtered one for URLs.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Behavior
 */

class GenerateUrlBehavior extends CActiveRecordBehavior
{
	/**
	 * @var string the attribute to put the filtered result into. Required.
	 */
	public $attribute;
	
	/**
	 * @var string the source attribute from which to generate the url string. Required.
	 */
	public $sourceAttribute;

	
	public function attach($owner)
	{
		parent::attach($owner);
	}
	
	/**
	 * Generate the filtered url string.
	 */
	public function beforeSave($event)
	{
		$owner = $this->getOwner();

		$id = (!$owner->isNewRecord ? $owner->primaryKey : null);
		
		foreach (Yii::app()->languageManager->languages as $l => $lang) 
		{
		    if ($l === Yii::app()->sourceLanguage) {
		    	$suffix = '';
		    	$language = null;
		    } else {
		    	$suffix = '_'.$l;
		    	$language = $l;
		    }
			$owner->{$this->attribute.$suffix} = AdminHelper::generateUrlStr($owner->{$this->sourceAttribute.$suffix}, $owner, $this->attribute, $id, $language);
		}
	}
}
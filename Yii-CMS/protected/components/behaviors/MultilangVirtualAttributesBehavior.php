<?php
/**
 * Make virtual attributes multilingual with a suffix the same way that MultilingualBehavior does.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Behavior
 */
class MultilangVirtualAttributesBehavior extends CActiveRecordBehavior
{
	/**
	 * @var array list of attributes to operate on (strings). Required.
	 */
	public $attributes;
	
	/**
	 * @var array holds the values of the translated attributes.
	 */
	private $_translatedAttributes=array();

	
	public function attach($owner)
	{
		parent::attach($owner);
		
		// Set up translatedAttributes.
		foreach ($this->attributes as $attribute)
		{
			foreach (Yii::app()->languageManager->suffixes as $suffix) 
			{
	    		$this->_translatedAttributes[] = $attribute.$suffix;
			}
		}
		foreach ($this->_translatedAttributes as $varName)
		{
			$this->$varName = null;
		}
		
		// Copying validation rules.
	    $rules = $owner->rules();
        $validators = $owner->getValidatorList();
        foreach (array_keys(Yii::app()->languageManager->languages) as $l) 
        {
        	if ($l != Yii::app()->sourceLanguage) 
        	{
				foreach($this->attributes as $attr) 
				{
					foreach($rules as $rule) 
					{
						$ruleAttributes = array_map('trim', explode(',', $rule[0]));
						if(in_array($attr, $ruleAttributes))
							$validators->add(CValidator::createValidator($rule[1], $this, $attr.'_'.$l, array_slice($rule, 2)));
					}
				}
        	}
        }
	}
	
	/**
	 * Setter. Returns translated attribute if called for.
	 */
	public function __set($name, $value) 
	{
		try { parent::__set($name, $value); } 
		catch (CException $e) {
			if (in_array($name, $this->_translatedAttributes))
				$this->$name = $value;
			else throw $e;
		}
	}
}
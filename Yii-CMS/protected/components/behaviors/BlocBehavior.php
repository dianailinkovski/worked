<?php
/**
 * Common operations for blocs
 * 
 * There are virtual fields that are transfered to Bloc entry.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Behavior
 */

class BlocBehavior extends CActiveRecordBehavior
{
	/**
	 * @var int order of the bloc.
	 */
	public $rank;
	/**
	 * @var boolean whether to have an anchor for this bloc.
	 */
	public $title_anchor;
	/**
	 * @var boolean whether to have a title for this bloc.
	 */
	public $title_page;
	/**
	 * @var null|int the parent id (optional).
	 */
	public $parent_id;
	/**
	 * @var string unique id of the set of blocs that this bloc belongs to.
	 */
	public $unique_id;
	/**
	 * @var boolean whether the bloc is being deleted by its parent in Bloc, if true then BeforeDelete is not needed.
	 */
	public $deleting=false;	
	
	/**
	 * @var array the owner validators.
	 */
	private $_validators;
	/**
	 * @var array indexes of the newly added validations.
	 */
	private $_validatorIndexes=array();

	
	public function attach($owner)
	{
		parent::attach($owner);

		$this->_validators = $owner->getValidatorList();
	}

	/**
	 * Anchor or title requires title field.
	 */
	public function beforeValidate($event)
	{
		$owner = $this->getOwner();
		$this->_validatorIndexes = array();
		
		if ($this->title_anchor || $this->title_page)
		{
			foreach (Yii::app()->languageManager->suffixes as $suffix)
			{
				$this->_validatorIndexes[] = $this->_validators->add(CValidator::createValidator('required', $owner, 'title'.$suffix));
			}
		}
		$this->_validatorIndexes = array_reverse($this->_validatorIndexes);
	}
	
	/**
	 * Removing validation.
	 */
	public function afterValidate($event)
	{
		$owner = $this->getOwner();

		foreach ($this->_validatorIndexes as $index)
		{
			$this->_validators->removeAt($index);
		}
	}
	
	/**
	 * Deleting related Bloc entry.
	 */
	public function beforeDelete($event)
	{
		$owner = $this->getOwner();

		if (!$this->deleting)
		{
			$event->isValid = false;
			Bloc::model()->findByAttributes(array('bloc_id'=>$owner->id, 'bloc_type'=>substr($owner->tableName(), 5)))->delete();
		}
		//return $owner->beforeDelete($event);
	}
	
	/**
	 * Saving related Bloc entry.
	 */
	public function afterSave($event)
	{
		$owner = $this->getOwner();
					
		if ($owner->isNewRecord)
		{
			$contentBloc = new Bloc;
			if (Yii::app()->languageManager->multilang)
				$contentBloc->multilang();
			$contentBloc->parent_id = $owner->parent_id;
			$contentBloc->bloc_type = mb_substr($owner->tableName(), 5);
			$contentBloc->bloc_id = $owner->primaryKey;
		}
		else
		{
			if (Yii::app()->languageManager->multilang)
				$contentBloc = Bloc::model()->multilang()->findByAttributes(array('bloc_id'=>$owner->primaryKey, 'bloc_type'=>mb_substr($owner->tableName(), 5)));
			else
				$contentBloc = Bloc::model()->findByAttributes(array('bloc_id'=>$owner->primaryKey, 'bloc_type'=>mb_substr($owner->tableName(), 5)));
		}
		$contentBloc->rank = $owner->rank;
		$contentBloc->title_anchor = $owner->title_anchor;
		$contentBloc->title_page = $owner->title_page;
		$contentBloc->unique_id = $owner->unique_id;
		$contentBloc->last_modified = date('Y-m-d H:i:s');
		
		foreach (Yii::app()->languageManager->languages as $l => $fullLanguage)
		{
			if($l === Yii::app()->sourceLanguage) $suffix = '';
    			else $suffix = '_'.$l;

		    $contentBloc->{'title'.$suffix} = $owner->{'title'.$suffix};
		    
		    if ($owner->isNewRecord)
		   		$contentBloc->{'title_url'.$suffix} = AdminHelper::generateUrlStr($owner->{'title'.$suffix}, $contentBloc, 'title_url', null, $l);
			else
		   		$contentBloc->{'title_url'.$suffix} = AdminHelper::generateUrlStr($owner->{'title'.$suffix}, $contentBloc, 'title_url', $contentBloc->id, $l);
		}
		$contentBloc->save(false);
	}
}
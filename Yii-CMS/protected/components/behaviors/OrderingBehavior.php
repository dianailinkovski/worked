<?php
/**
 * Implements a simple ordering system for the items in the table.
 * 
 * $attribute will be set incrementally starting at 1.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Behavior
 */

class OrderingBehavior extends CActiveRecordBehavior
{
	/**
	 * @var string the attribute on which to operate.
	 */
	public $attribute;

	
	public function attach($owner)
	{
		parent::attach($owner);
	}

	/**
	 * Set attribute to max if new record.
	 */
	public function beforeValidate($event) 
	{		
		$owner = $this->getOwner();
		$table = $owner->tableName();
		
		if ($owner->isNewRecord && $owner->{$this->attribute} === null)
		{
			$maxOrder = Yii::app()->db->createCommand()
									->select('MAX(`'.$this->attribute.'`)')
									->from($table)
									->queryRow();
			$owner->{$this->attribute} = $maxOrder['MAX(`'.$this->attribute.'`)']+1;
		}
	}
	
	/**
	 * Lower higher records when deleting a record.
	 */
	public function beforeDelete($event)
	{
		$owner = $this->getOwner();
		$table = $owner->tableName();
		
		$rank = $owner->{$this->attribute};
		
		$command=Yii::app()->db->createCommand('UPDATE `'.$table.'` SET `'.$this->attribute.'`=`'.$this->attribute.'`-1 WHERE `'.$this->attribute.'`>:rank');
		$command->bindParam(':rank', $rank);
		$command->execute();
	}
	
	/**
	 * Move attached model to target model's position.
	 * 
	 * @param CACtiveRecord $model the target model.
	 * @param string $type "prev" or "next" whether to put the model before or after the target.
	 */
	public function moveTo($model, $type)
	{
		$owner = $this->getOwner();
		$table = $owner->tableName();
		$attribute = $this->attribute;
		$ownerAttribute = $owner->$attribute;
		$modelAttribute = $model->$attribute;
		$ownerPk = $owner->primaryKey;

		$command=Yii::app()->db->createCommand('UPDATE `'.$table.'` SET `'.$attribute.'`=`'.$attribute.'`-1 WHERE `'.$attribute.'` > :rank');
		$command->bindParam(':rank',$ownerAttribute);
		$command->execute();

		if ($type == 'prev')
		{
			$command=Yii::app()->db->createCommand('UPDATE `'.$table.'` SET `'.$attribute.'`=`'.$attribute.'`+1 WHERE `'.$attribute.'` >'.($owner->$attribute > $model->$attribute ? '' : '=').':rank');
			$command->bindParam(':rank',$modelAttribute);
			$command->execute();

			$rank = $owner->$attribute > $model->$attribute ? $model->$attribute + 1 : $model->$attribute;
			$command=Yii::app()->db->createCommand('UPDATE `'.$table.'` SET `'.$attribute.'`=:rank WHERE `id` = :id');
			$command->bindParam(':id',$ownerPk);
			$command->bindParam(':rank',$rank);
			$command->execute();
		}
		else {
			$command=Yii::app()->db->createCommand('UPDATE `'.$table.'` SET `'.$attribute.'`=`'.$attribute.'`+1 WHERE `'.$attribute.'` >= :rank');
			$command->bindParam(':rank',$modelAttribute);
			$command->execute();
			
			$rank = $model->$attribute;
			$command=Yii::app()->db->createCommand('UPDATE `'.$table.'` SET `'.$attribute.'`=:rank WHERE `id` = :id');
			$command->bindParam(':id',$ownerPk);
			$command->bindParam(':rank',$rank);
			$command->execute();
		}
	}
}
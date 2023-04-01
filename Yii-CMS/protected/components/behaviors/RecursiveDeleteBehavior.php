<?php
/**
 * When deleting a parent model, will recursively delete all children models (and children of children, etc).
 * 
 * Especially useful if operations are needed on the children model during their deletion.
 * 
 * Does not currently support MANY_MANY relations.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Behavior
 */
class RecursiveDeleteBehavior extends CActiveRecordBehavior
{
	public function attach($owner)
	{
		parent::attach($owner);
	}

	public function beforeDelete($event)
	{
		$owner = $this->getOwner();
		$this->processModel($owner, false);
	}
	
	/**
	 * Recursive function that deletes all children models to the $model.
	 * 
	 * @param CActiveRecord $model the model to process.
	 * @param boolean $delete whether to delete the $model (the top model's deletion is handled by beforeDelete).
	 */
	protected function processModel($model, $delete=true)
	{
		foreach ($model->relations() as $relationName => $relation)
		{
			$type = $relation[0];
			$className = $relation[1];
			$foreignKey = $relation[2];
			
			if ($type == CActiveRecord::HAS_MANY)
			{
				foreach ($model->$relationName as $subModel)
				{
					if (!empty($subModel))
						$this->processModel($subModel);
				}
			}
			elseif ($type == CActiveRecord::HAS_ONE)
			{
				if (!empty($model->$relationName))
					$this->processModel($model->$relationName);
			}
		}
		if ($delete)
			$model->delete();
	}
}
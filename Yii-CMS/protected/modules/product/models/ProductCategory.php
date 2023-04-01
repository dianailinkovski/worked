<?php

class ProductCategory extends CActiveRecord
{
	public $parentId; // for AliasLocationWidget;
	
	public $location; // for AliasLocationWidget;
	
	public $locationWidget; // for AliasLocationWidget;


	public function tableName()
	{
		return 'product_category';
	}

	public function rules()
	{
		return array(
			array('name', 'required'),
			array('name, image', 'length', 'max'=>255),
			array('description', 'length', 'max'=>500),
			array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty'=>true),
			array('id, name, description, image', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'products' => array(self::MANY_MANY, 'Product', 'product_category_product(category_id, product_id)'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => 'ID',
			'name' => 'Nom',
			'name_url' => 'Nom (url)',
			'description' => 'Description',
			'image' => 'Image',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;
		
		$criteria->order = $this->tree->hasManyRoots
                            ? $this->tree->rootAttribute.','.$this->tree->leftAttribute
                            : $this->tree->leftAttribute;

		foreach ($this->attributes as $attribute => $value) 
		{
			if (!empty($value))
				$criteria->compare($attribute, $this->{$attribute}, true);
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => false,
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('name', 'description', 'name_url'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	    		'forceOverwrite' => true,
	        ),
	        'imageHandler' => array(
	        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
	        	'attribute' => 'image',
				'dir' => 'files/_user/product_category',
				'tempDir' => 'files/_user/product_category/_temp',
				'cacheTime' => 10 * 24 * 60 * 60, // 10 days
				'formats' => array(
					'm'=>array(555, 416),
				)
	        ),
	        'tree'=>array(
	            'class'=>'ext.nested-set-behavior.NestedSetBehavior',
	            'leftAttribute'=>'lft',
	            'rightAttribute'=>'rgt',
	            'levelAttribute'=>'level',
		    ),
	        'url' => array(
	            'class' => 'application.components.behaviors.GenerateUrlBehavior',
	            'attribute' => 'name_url',
	        	'sourceAttribute' => 'name',
	        ),
	    );
	}
	
	public function defaultScope()
	{
	    return $this->ml->localizedCriteria();
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

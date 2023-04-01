<?php
class ProductTag extends CActiveRecord
{
	public function tableName()
	{
		return 'product_tag';
	}

	public function rules()
	{
		return array(
			array('name, color', 'required'),
			array('name', 'length', 'max'=>255),
			array('color', 'length', 'max'=>6),
			array('id, name, color', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'products' => array(self::MANY_MANY, 'Product', 'product_tag_product(tag_id, product_id)'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => 'ID',
			'name' => 'Nom',
			'color' => 'Couleur',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('color',$this->color,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('name'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	    		'forceOverwrite' => true,
	        )
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

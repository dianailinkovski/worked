<?php
class ProductImage extends CActiveRecord
{
	public function tableName()
	{
		return 'product_image';
	}

	public function rules()
	{
		return array(
			array('file, rank, product_id', 'required'),
			array('rank, product_id', 'length', 'max'=>10),
			array('file', 'file', 'types'=>'jpg, gif, png', 'allowEmpty'=>true),
			array('id, file, rank, product_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'file' => 'Fichier',
			'rank' => 'Rank',
			'product_id' => 'Product',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('file',$this->file);
		$criteria->compare('rank',$this->rank,true);
		$criteria->compare('product_id',$this->product_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'fileHandler' => array(
	        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
	        	'attribute' => 'file',
				'dir' => 'files/_user/product_image',
				'tempDir' => 'files/_user/product_image/_temp',
				'cacheTime' => 10 * 24 * 60 * 60, // 10 days
				'formats' => array(
					'm'=>array(555, 416),
				)
	        )
	    );
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

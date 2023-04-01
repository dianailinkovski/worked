<?php
class Product extends CActiveRecord
{
	public function tableName()
	{
		return 'product';
	}

	public function rules()
	{
		return array(
			array('refnum, name, price_regular', 'required'),
			array('out_of_stock, in_store_only, taxes', 'boolean'),
			array('refnum, name', 'length', 'max'=>255),
			array('summary', 'length', 'max'=>500),
			array('price_regular, price_sale, weight, width, height, length', 'length', 'max'=>10),
			array('price_regular, price_sale', 'numerical', 'min'=>0, 'max'=>9999999.99),
			array('weight, width, height, length', 'numerical', 'min'=>0, 'max'=>4294967295),
			array('sale_start, sale_end', 'date', 'format'=>'yyyy-MM-dd HH:mm:ss'),
			array('sale_start, sale_end, price_sale', 'default', 'setOnEmpty'=>true, 'value'=>null),
			array('id, refnum, name, price_regular, price_sale, sale_start, sale_end, out_of_stock, in_store_only', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'categories' => array(self::MANY_MANY, 'ProductCategory', 'product_category_product(product_id, category_id)'),
			'images' => array(self::HAS_MANY, 'ProductImage', 'product_id'),
			'tags' => array(self::MANY_MANY, 'ProductTag', 'product_tag_product(product_id, tag_id)'),
			'blocs' => array(self::HAS_MANY, 'Bloc', 'parent_id', 'condition'=>"unique_id='product_tab1'"),
			'blocs2' => array(self::HAS_MANY, 'Bloc', 'parent_id', 'condition'=>"unique_id='product_tab2'"),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => 'ID',
			'refnum' => '# Référence',
			'name' => 'Nom',
			'name_url' => 'Nom (url)',
			'price_regular' => 'Prix régulier',
			'price_sale' => 'Prix vente',
			'sale_start' => 'Vente début',
			'sale_end' => 'Vente fin',
			'out_of_stock' => 'Stock épuisé',
			'in_store_only' => 'En magasin seulement',
			'summary' => 'Description sommaire',
			'width' => 'Largeur (cm)',
			'height' => 'Hauteur (cm)',
			'length' => 'Longueur (cm)',
			'weight' => 'Poids (grammes)',
			'taxes' => 'Taxes',
		));
	}

	public function search()
	{
		$out_of_stock = str_replace(array('Oui', 'Non'), array(1, 0), $this->out_of_stock);

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('refnum',$this->refnum,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('price_regular',$this->price_regular,true);
		$criteria->compare('price_sale',$this->price_sale,true);
		$criteria->compare('sale_start',$this->sale_start,true);
		$criteria->compare('sale_end',$this->sale_end,true);
		$criteria->compare('out_of_stock',$out_of_stock);
		$criteria->compare('in_store_only',$this->in_store_only);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('name', 'summary', 'name_url'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	    		'forceOverwrite' => true,
	        ),
	        'url' => array(
	            'class' => 'application.components.behaviors.GenerateUrlBehavior',
	            'attribute' => 'name_url',
	        	'sourceAttribute' => 'name',
	        ),
			'activerecord-relation'=>array(
			    'class'=>'ext.activerecord-relation-behavior.EActiveRecordRelationBehavior',
			),
	        'recursiveDelete' => array(
	            'class' => 'application.components.behaviors.RecursiveDeleteBehavior',
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

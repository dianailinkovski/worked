<?php
class BlocFeature extends CActiveRecord
{
	public function tableName()
	{
		return 'bloc_feature';
	}

	public function rules()
	{
		return array(
			array('layout', 'required'),
			array('layout', 'numerical', 'min'=>1, 'max'=>2),
			array('id', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'features' => array(self::HAS_MANY, 'BlocFeatureFeature', 'bloc_feature_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title_anchor' => 'Inclure dans l’index de contenu',
			'title_page' => 'Afficher le titre dans la page',
			'title' => 'Titre du bloc',
			'layout'=>'Type d’affichage',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('layout',$this->layout,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function behaviors() 
	{
	    return array(
	        'bloc' => array(
	            'class' => 'application.components.behaviors.BlocBehavior',
	        ),
			'multilangVirtualAttributes'=>array(
				'class'=>'application.components.behaviors.MultilangVirtualAttributesBehavior',
				'attributes'=>array('title'),
			),
	        'recursiveDelete' => array(
	            'class' => 'application.components.behaviors.RecursiveDeleteBehavior',
	        ),
	    );
	}
}

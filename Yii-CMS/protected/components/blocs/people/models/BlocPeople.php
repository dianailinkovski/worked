<?php
class BlocPeople extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bloc_people';
	}

	public function rules()
	{
		return array(
			array('columns', 'required'),
			array('columns', 'length', 'max'=>1),
			array('columns', 'numerical', 'max'=>3, 'min'=>1),
			array('id', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'people' => array(self::HAS_MANY, 'BlocPeoplePeople', 'bloc_people_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title_anchor' => 'Inclure dans l’index de contenu',
			'title_page' => 'Afficher le titre dans la page',
			'title' => 'Titre du bloc',
			'columns' => 'Mise en page',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function behaviors() 
	{
	    return array(
	        'bloc' => array(
	            'class' => 'application.components.behaviors.BlocBehavior',
	        ),
	        'recursiveDelete' => array(
	            'class' => 'application.components.behaviors.RecursiveDeleteBehavior',
	        ),
			'multilangVirtualAttributes'=>array(
				'class'=>'application.components.behaviors.MultilangVirtualAttributesBehavior',
				'attributes'=>array('title'),
			),
	    );
	}
}
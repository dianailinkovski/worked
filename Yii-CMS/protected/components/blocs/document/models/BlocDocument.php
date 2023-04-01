<?php
class BlocDocument extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bloc_document';
	}

	public function rules()
	{
		return array(
			array('id', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'documents' => array(self::HAS_MANY, 'BlocDocumentDocument', 'bloc_document_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title_anchor' => 'Inclure dans l’index de contenu',
			'title_page' => 'Afficher le titre dans la page',
			'title' => 'Titre du bloc',
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
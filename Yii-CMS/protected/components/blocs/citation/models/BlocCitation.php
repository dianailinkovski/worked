<?php
class BlocCitation extends CActiveRecord
{
	public function tableName()
	{
		return 'bloc_citation';
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
			'blocCitationCitations' => array(self::HAS_MANY, 'BlocCitationCitation', 'bloc_citation_id'),
			'citations' => array(self::HAS_MANY, 'BlocCitationCitation', 'bloc_citation_id'),
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
	    );
	}
}

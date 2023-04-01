<?php
class BlocCitationCitation extends CActiveRecord
{
	public function tableName()
	{
		return 'bloc_citation_citation';
	}

	public function rules()
	{
		return array(
			array('bloc_citation_id, citation, name, rank', 'required'),
			array('bloc_citation_id', 'length', 'max'=>10),
			array('name', 'length', 'max'=>255),
			array('id, bloc_citation_id, citation, name', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'bloc' => array(self::BELONGS_TO, 'BlocCitation', 'bloc_citation_id'),
			'blocCitationCitationLangs' => array(self::HAS_MANY, 'BlocCitationCitationLang', 'bloc_citation_citation_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => 'ID',
			'bloc_citation_id' => 'Bloc Citation',
			'citation' => 'Citation',
			'name' => 'Nom et autres détails sur la personne',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('bloc_citation_id',$this->bloc_citation_id,true);
		$criteria->compare('citation',$this->citation,true);
		$criteria->compare('name',$this->name,true);

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
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('citation', 'name'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	        	'forceOverwrite' => true,
	        ),
	    );
	}
	
	public function defaultScope()
	{
	    return array_merge(array(
			'order'=>'rank ASC',
		), $this->ml->localizedCriteria());
	}
}

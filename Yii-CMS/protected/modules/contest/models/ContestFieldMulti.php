<?php
class ContestFieldMulti extends CActiveRecord
{
	public function tableName()
	{
		return 'contest_field_multi';
	}

	public function rules()
	{
		return array(
			array('contest_field_id, title, rank', 'required'),
			array('contest_field_id, rank', 'length', 'max'=>10),
			array('title', 'length', 'max'=>500),
			array('id, contest_field_id, title, rank', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'contestField' => array(self::BELONGS_TO, 'ContestField', 'contest_field_id'),
			'contestFieldMultiLangs' => array(self::HAS_MANY, 'ContestFieldMultiLang', 'contest_field_multi_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'contest_field_id' => 'Contest Field',
			'title' => 'Libellé du choix de réponse',
			'rank' => 'Rank',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('contest_field_id',$this->contest_field_id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('rank',$this->rank,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.models.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('title'), 
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	    		'forceOverwrite' => true,
	        ),
	    );
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function defaultScope()
	{
	    return $this->ml->localizedCriteria();
	}
}

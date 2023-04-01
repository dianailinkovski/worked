<?php
class ContestField extends CActiveRecord
{
	public function tableName()
	{
		return 'contest_field';
	}

	public function rules()
	{
		return array(
			array('contest_id, title, type, rank', 'required'),
			array('required, result', 'numerical', 'integerOnly'=>true),
			array('contest_id, rank', 'length', 'max'=>10),
			array('title', 'length', 'max'=>500),
			array('type', 'length', 'max'=>60),
			array('id, contest_id, title, type, required, result', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'contest' => array(self::BELONGS_TO, 'Contest', 'contest_id'),
			'multi' => array(self::HAS_MANY, 'ContestFieldMulti', 'contest_field_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'contest_id' => 'Contest',
			'title' => 'Libellé du champ',
			'type' => 'Type de champ',
			'required' => 'Ce champ doit être obligatoire',
			'result' => 'Afficher dans les résultats',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('contest_id',$this->contest_id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('required',$this->required);

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
	
	public function beforeSave()
	{
		if ($this->type == 'title')
		{
			$this->required = 0;
			$this->result = 0;
		}
		return parent::beforeSave();	
	}
}

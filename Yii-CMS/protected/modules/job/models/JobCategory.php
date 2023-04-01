<?php
class JobCategory extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'job_category';
	}

	public function rules()
	{
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>255),
			array('id, name', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'jobs' => array(self::HAS_MANY, 'Job', 'category_id'),
			'jobCategoryLangs' => array(self::HAS_MANY, 'JobCategoryLang', 'job_category_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'name' => 'Nom',
			'name_url' => 'Nom Url',
		));
	}
	
	public function beforeDelete()
	{
		if (!empty($this->jobs))
			return false;
		
		return parent::beforeDelete();	
	}
	
	public function beforeSave() 
	{
		$id = (!$this->isNewRecord ? $this->primaryKey : null);
		
		foreach (Yii::app()->languageManager->languages as $l => $lang) 
		{
		    if ($l === Yii::app()->sourceLanguage) {
		    	$suffix = '';
		    	$language = null;
		    } else {
		    	$suffix = '_'.$l;
		    	$language = $l;
		    }
			$this->{'name_url'.$suffix} = AdminHelper::generateUrlStr($this->{'name'.$suffix}, $this, 'name_url', $id, $language);
		}
		
		return parent::beforeSave();
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors()
	{
		return array(
			'ml' => array(
				'class' => 'application.models.behaviors.MultilingualBehavior',
				'localizedAttributes' => array('name', 'name_url'),
				'languages' => Yii::app()->languageManager->languages,
				'defaultLanguage' => Yii::app()->sourceLanguage,
				'forceOverwrite' => true,
			),
		);
	}
	public function defaultScope()
	{
		return $this->ml->localizedCriteria();
	}
}
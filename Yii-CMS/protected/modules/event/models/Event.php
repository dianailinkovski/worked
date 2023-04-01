<?php
class Event extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'event';
	}

	public function rules()
	{
		return array(
			array('title, date_start, date_end, location, summary, section_id', 'required'),
			array('date_start, date_end', 'date', 'format'=>'yyyy-MM-dd HH:mm:ss'),
			array('title, image, title_url, image_label, location', 'length', 'max'=>255),
			array('summary', 'length', 'max'=>500),
			array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty'=>true),
			array('section_id', 'length', 'max'=>10),
			array('section_id', 'numerical'),
			array('title_url', 'reservedTitleWords'),
			array('location_map', 'safe'),
			array('id, title, date_created, date_start, date_end, summary, image, image_label, title_url, location, section_id', 'safe', 'on'=>'search'),
		);
	}

	public function reservedTitleWords($attribute, $params)
	{
		$languageManager = Yii::app()->languageManager;
		
		if (preg_match('/(_('.implode('|', array_keys($languageManager->languages)).'))$/', $attribute, $language))
		{
			if ($language[2] != $languageManager->defaultLanguage && $this->$attribute == Yii::app()->modules['event']['archivesVarName'][$language[2]])
				$this->addError('title_'.$language[2], 'Ceci est un mot réservé, vous ne pouvez pas l’utiliser comme titre.');
		}
		else {
			if ($this->$attribute == Yii::app()->modules['event']['archivesVarName'][$languageManager->defaultLanguage])
				$this->addError('title', 'Ceci est un mot réservé, vous ne pouvez pas l’utiliser comme titre.');
		}
		
		if ($this->$attribute == 'page')
			$this->addError('title', 'Ceci est un mot réservé, vous ne pouvez pas l’utiliser comme titre.');
	}

	public function relations()
	{
		return array(
			'section' => array(self::BELONGS_TO, 'CmsSection', 'section_id'),
			'eventLangs' => array(self::HAS_MANY, 'EventLang', 'event_id'),
			'blocs' => array(self::HAS_MANY, 'Bloc', 'parent_id', 'condition'=>"unique_id='event'"),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title' => Yii::t('eventModule.common', 'Titre de l’événement'),
			'date_created' => Yii::t('eventModule.common', 'Date de publication'),
			'date_start' => Yii::t('eventModule.common', 'Date de début'),
			'date_end' => Yii::t('eventModule.common', 'Date de fin'),
			'summary' => Yii::t('eventModule.common', 'Description sommaire'),
			'image' => Yii::t('eventModule.common', 'Image reliée'),
			'image_label' => Yii::t('eventModule.common', 'Annotation de l’image'),
			'title_url' => Yii::t('eventModule.common', 'Url'),
			'location' => Yii::t('eventModule.common', 'Nom de l’emplacement'),
			'location_map' => Yii::t('eventModule.common', 'Google Map de l’emplacement (code iFrame)'),
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('section_id',$this->section_id,true);
		$criteria->compare('date_created',$this->date_created,true);
		$criteria->compare('date_start',$this->date_start,true);
		$criteria->compare('date_end',$this->date_end,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('title_url',$this->title_url,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('image_label',$this->image_label,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('title', 'title_url', 'summary', 'image_label', 'location'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	    		'forceOverwrite' => true,
	        ),
	        'imageHandler' => array(
	        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
	        	'attribute' => 'image',
				'dir' => 'files/_user/event',
				'tempDir' => 'files/_user/event/_temp',
				'cacheTime' => 10 * 24 * 60 * 60, // 10 days
				'formats' => array(
					's'=>array(350, 263),
					'm'=>array(555, 416),
					'l'=>array(700, 525),
				)
	        ),
	        'url' => array(
	            'class' => 'application.components.behaviors.GenerateUrlBehavior',
	            'attribute' => 'title_url',
	        	'sourceAttribute' => 'title',
	        ),
	    );
	}

	public function defaultScope()
	{
	    return $this->ml->localizedCriteria();
	}
}
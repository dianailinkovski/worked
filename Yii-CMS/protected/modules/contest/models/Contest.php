<?php
class Contest extends CActiveRecord
{
	public function tableName()
	{
		return 'contest';
	}

	public function rules()
	{
		return array(
			array('title, summary, status, start_date, multiple_entries, send_notification_email', 'required'),
			array('start_date, end_date', 'date', 'format'=>'yyyy-MM-dd HH:mm:ss'),
			array('title, image, title_url', 'length', 'max'=>255),
			array('multiple_entries, send_notification_email', 'boolean'),
			array('summary', 'length', 'max'=>500),
			array('max_participation', 'length', 'max'=>10),
			array('max_participation', 'numerical'),
			array('status', 'length', 'max'=>60),
			array('max_participation, start_date, end_date', 'default', 'setOnEmpty'=>true, 'value' => null),
			array('id, title, summary, start_date, end_date, image, max_participation, status', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'entries' => array(self::HAS_MANY, 'ContestEntry', 'contest_id'),
			'fields' => array(self::HAS_MANY, 'ContestField', 'contest_id'),
			'blocsIntroduction' => array(self::HAS_MANY, 'Bloc', 'parent_id', 'condition'=>"unique_id='contest_intro'"),
			'blocsConfirmation' => array(self::HAS_MANY, 'Bloc', 'parent_id', 'condition'=>"unique_id='contest_confirmation'"),
			'blocsConclusion' => array(self::HAS_MANY, 'Bloc', 'parent_id', 'condition'=>"unique_id='contest_conclusion'"),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Titre',
			'summary' => 'Sommaire',
			'start_date' => 'Date de début',
			'multiple_entries' => 'Permettre plusieurs participations par adresse IP',
			'end_date' => 'Date de fin',
			'image' => 'Image reliée',
			'max_participation' => 'Nombre maximum de participants (total)',
			'status' => 'Statut',
			'send_notification_email' => 'Activer l’envoi des notifications par courriel',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('max_participation',$this->max_participation,true);
		$criteria->compare('status',$this->status,true);

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
	            'class' => 'application.models.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('title', 'title_url', 'summary'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	    		'forceOverwrite' => true,
	        ),
	        'generateUrl' => array(
	            'class' => 'application.models.behaviors.GenerateUrlBehavior',
	            'attribute' => 'title_url',
	            'sourceAttribute' => 'title',
	        ),
	        'imageHandler' => array(
	        	'class' => 'application.models.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
	        	'attribute' => 'image',
				'dir' => 'files/_user/contest',
				'tempDir' => 'files/_user/contest/_temp',
				'cacheTime' => 10 * 24 * 60 * 60, // 10 days
				'formats' => array(
					'm'=>array(450, 338),
					'l'=>array(750, 563),
				)
	        )
	    );
	}
	
	public function defaultScope()
	{
	    return $this->ml->localizedCriteria();
	}
}

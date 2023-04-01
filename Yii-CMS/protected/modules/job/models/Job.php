<?php
class Job extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'job';
	}

	public function rules()
	{
		return array(
			array('category_id, title, type, publication_date, postulation_end_date', 'required'),
			array('publication_date, start_date', 'date', 'format'=>'yyyy-MM-dd'),
			array('start_date', 'default', 'setOnEmpty'=>true, 'value' => null),
			array('postulation_end_date', 'date', 'format'=>'yyyy-MM-dd HH:mm:ss'),
			array('type, category_id', 'numerical', 'integerOnly'=>true),
			array('nb_available', 'numerical', 'max'=>65535, 'integerOnly'=>true),
			array('title, title_url', 'length', 'max'=>255),
			array('active', 'boolean'),
			array('id, category_id, title, publication_date, start_date, type, title_url', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'category' => array(self::BELONGS_TO, 'JobCategory', 'category_id'),
			'cvs' => array(self::MANY_MANY, 'JobCv', 'job_job_cv(job_id, job_cv_id)'),
			'jobLangs' => array(self::HAS_MANY, 'JobLang', 'job_id'),
			'blocs' => array(self::HAS_MANY, 'Bloc', 'parent_id', 'condition'=>"unique_id='job'"),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'category_id' => 'Catégorie',
			'title' => 'Titre',
			'publication_date' => 'Date de publication de l’offre d’emploi',
			'start_date' => 'Date de début de l’emploi',
			'type' => 'Type',
			'title_url' => 'Title Url',
			'postulation_end_date' => 'Date et heure limite pour postuler',
			'nb_available' => 'Nombre de postes à combler',
			'active' => Yii::t('eventModule.common', 'Afficher sur le site'),
		));
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
			$this->{'title_url'.$suffix} = AdminHelper::generateUrlStr($this->{'title'.$suffix}, $this, 'title_url', $id, $language);
		}
		
		return parent::beforeSave();
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('publication_date',$this->publication_date,true);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('title_url',$this->title_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors()
	{
		return array(
			'ml' => array(
				'class' => 'application.models.behaviors.MultilingualBehavior',
				'localizedAttributes' => array('title', 'title_url'),
				'languages' => Yii::app()->languageManager->languages,
				'defaultLanguage' => Yii::app()->sourceLanguage,
				'forceOverwrite' => true,
			)
		);
	}
	
	public function defaultScope()
	{
		return $this->ml->localizedCriteria();
	}
}

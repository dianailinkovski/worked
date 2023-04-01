<?php
class News extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'news';
	}

	public function rules()
	{
		return array(
			array('title, date, section_id', 'required'),
			array('date', 'date', 'format'=>'yyyy-MM-dd HH:mm:ss'),
			array('title, image, title_url, image_label, source, source_url', 'length', 'max'=>255),
			array('section_id', 'length', 'max'=>10),
			array('section_id', 'numerical'),
			array('source_url', 'url'),
			array('summary', 'length', 'max'=>500),
			array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty'=>true),
			array('id, section_id, title, date, summary, image, title_url', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'newsLangs' => array(self::HAS_MANY, 'NewsLang', 'news_id'),
			'section' => array(self::BELONGS_TO, 'CmsSection', 'section_id'),
			'blocs' => array(self::HAS_MANY, 'Bloc', 'parent_id', 'condition'=>"unique_id='news'"),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title' => 'Titre',
			'date' => 'Date de publication',
			'summary' => 'Résumé',
			'image' => 'Image',
			'image_label' => 'Annotation image',
			'title_url' => 'Title Url',
			'source' => 'Titre de la source',
			'source_url' => 'Url de la source',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('section_id',$this->section_id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('title_url',$this->title_url,true);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('source_url',$this->source_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('title', 'title_url', 'summary', 'image_label', 'source', 'source_url'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	    		'forceOverwrite' => true,
	        ),
	        'imageHandler' => array(
	        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
	        	'attribute' => 'image',
				'dir' => 'files/_user/news',
				'tempDir' => 'files/_user/news/_temp',
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

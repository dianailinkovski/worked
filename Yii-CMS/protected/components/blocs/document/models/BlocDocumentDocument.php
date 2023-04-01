<?php
class BlocDocumentDocument extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bloc_document_document';
	}

	public function rules()
	{
		return array(
			array('title, rank, file, datetime', 'required'),
			array('file, title, mime_type', 'length', 'max'=>255),
			array('file', 'file', 'allowEmpty'=>true),
			array('description', 'length', 'max'=>500),
			array('id, title, description', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'bloc' => array(self::BELONGS_TO, 'BlocDocument', 'bloc_document_id'),
			'blocDocumentDocumentLangs' => array(self::HAS_MANY, 'BlocDocumentDocumentLang', 'bloc_document_document_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'file' => 'Fichier',
			'datetime' => 'Date de publication',
			'title' => 'Titre du document',
			'description' => 'Description sommaire',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
		$arr = array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('title', 'description', 'file'), 
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	    		'forceOverwrite' => false,
	        ),
		);

	    foreach (Yii::app()->languageManager->suffixes as $suffix)
	    {
	    	if ($suffix == '')
	    	{
		    	$arr['fileHandler'] = array(
			        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
			        	'attribute' => 'file',
			        	'allowDelete' => false,
						'dir' => 'files/_user/bloc_document',
						'tempDir' => 'files/_user/bloc_document/_temp',
						'cacheTime' => 10 * 24 * 60 * 60, // 10 days
			    );
	    	}
	    	else {
		    	$arr['fileHandler'.$suffix] = array(
			        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
			        	'attribute' => 'file'.$suffix,
			        	'allowDelete' => true,
						'dir' => 'files/_user/bloc_document',
						'tempDir' => 'files/_user/bloc_document/_temp',
						'cacheTime' => 10 * 24 * 60 * 60, // 10 days
			    );
	    	}
	    }
	    
	    return $arr;
	}

	// Multilang required for the multilang file field
	public function beforeFind()
	{
		if (is_subclass_of(Yii::app()->controller, 'BackController') && Yii::app()->languageManager->multilang)
			$this->multilang();
		
		return parent::beforeFind();	
	}
	
	// Getting mime type, assigning it this way makes it execute after the ones in uploading behavior
	public function afterValidate()
	{
		$this->onBeforeSave = function($event){
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$event->sender->mime_type = finfo_file($finfo, 'files/_user/bloc_document/'.$event->sender->file);
			finfo_close($finfo);
		};
		return parent::afterValidate();	
	}

	public function defaultScope()
	{
	    return array_merge(array(
			'order'=>'rank ASC',
		), $this->ml->localizedCriteria());
	}
}
<?php
class BlocImage extends CActiveRecord
{
	public function tableName()
	{
		return 'bloc_image';
	}

	public function rules()
	{
		return array(
			array('image', 'required'),
			array('image, image_title', 'length', 'max'=>255),
			array('image', 'file', 'types'=>'jpg, jpeg, png', 'allowEmpty'=>true),
			array('id, image, image_title', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'blocImageLangs' => array(self::HAS_MANY, 'BlocImageLang', 'bloc_image_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title_anchor' => 'Inclure dans l’index de contenu',
			'title_page' => 'Afficher le titre dans la page',
			'title' => 'Titre du bloc',
			'image' => 'Image à insérer',
			'image_title' => 'Brève description de l’image (optimisation SEO)',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('image_title',$this->image_title,true);

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
		$arr = array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('image_title', 'image'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	        	'forceOverwrite' => false,
	        ),
	        'bloc' => array(
	            'class' => 'application.components.behaviors.BlocBehavior',
	        ),
			'multilangVirtualAttributes'=>array(
				'class'=>'application.components.behaviors.MultilangVirtualAttributesBehavior',
				'attributes'=>array('title'),
			),
		);
		
		foreach (Yii::app()->languageManager->suffixes as $suffix)
	    {
	    	if ($suffix == '')
	    	{
		        $arr['imageHandler'] = array(
		        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
		        	'attribute' => 'image',
					'dir' => 'files/_user/bloc_image',
					'tempDir' => 'files/_user/bloc_image/_temp',
					'cacheTime' => 10 * 24 * 60 * 60, // 10 days
					'formats' => array(
						's'=>array(560, 420),
						'm'=>array(770, 578),
						'l'=>array(1024, 768),
					),
		        	'allowDelete' => false,
					'onlyResizeIfBigger' => true
		        );
	    	}
	    	else {
		        $arr['imageHandler'.$suffix] = array(
		        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
		        	'attribute' => 'image'.$suffix,
					'dir' => 'files/_user/bloc_image',
					'tempDir' => 'files/_user/bloc_image/_temp',
					'cacheTime' => 10 * 24 * 60 * 60, // 10 days
					'formats' => array(
						's'=>array(560, 420),
						'm'=>array(770, 578),
						'l'=>array(1024, 768),
					),
		        	'allowDelete' => true,
					'onlyResizeIfBigger' => true
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
	
	public function defaultScope()
	{
	    return $this->ml->localizedCriteria();
	}
}

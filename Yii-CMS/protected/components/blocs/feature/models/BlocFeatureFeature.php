<?php
class BlocFeatureFeature extends CActiveRecord
{
	public function tableName()
	{
		return 'bloc_feature_feature';
	}

	public function rules()
	{
		$purifier = new CHtmlPurifier();
		$purifier->options = Yii::app()->params['htmlPurifierOptions'];

		return array(
			array('bloc_feature_id, rank, title, description', 'required'),
			array('description','filter','filter'=>array($purifier,'purify')),
			array('bloc_feature_id, rank', 'length', 'max'=>10),
			array('image, title', 'length', 'max'=>255),
			array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty'=>true),
			array('title, description', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'bloc' => array(self::BELONGS_TO, 'BlocFeature', 'bloc_feature_id'),
			'blocFeatureFeatureLangs' => array(self::HAS_MANY, 'BlocFeatureFeatureLang', 'bloc_feature_feature_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => 'ID',
			'bloc_feature_id' => 'Bloc Feature',
			'image' => 'Image reliée',
			'title' => 'Titre de la caractéristique',
			'description' => 'Détails de la caractéristique',
			'rank' => 'Rank',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('bloc_feature_id',$this->bloc_feature_id,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('rank',$this->rank,true);

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
	            'localizedAttributes' => array('title', 'description'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	        	'forceOverwrite' => true,
	        ),
	        'imageHandler' => array(
	        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
	        	'attribute' => 'image',
				'dir' => 'files/_user/bloc_feature',
				'tempDir' => 'files/_user/bloc_feature/_temp',
				'cacheTime' => 10 * 24 * 60 * 60, // 10 days
				'formats' => array(
					'm'=>array(500, 400),
				),
				'onlyResizeIfBigger' => true,
	        )
	    );
	}
	
	public function defaultScope()
	{
	    return array_merge(array(
			'order'=>'rank ASC',
		), $this->ml->localizedCriteria());
	}
}

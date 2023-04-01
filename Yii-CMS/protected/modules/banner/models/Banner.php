<?php
class Banner extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return News the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'banner';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$purifier = new CHtmlPurifier();
		$purifier->options = Yii::app()->params['htmlPurifierOptions'];

		return array(
			array('active, text, location, color, presence', 'required'),
			array('text','filter','filter'=>array($purifier,'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, text', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'bannerLangs' => array(self::HAS_MANY, 'BannerLang', 'banner_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return Helper::multilangLabels($this, array(
			'id' => '#',
			'active' => 'Actif',
			'text' => 'Texte',
			'location' => 'Emplacement',
			'presence' => 'Présence',
			'color' => 'Couleur',
		));
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.models.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('text'), //attributes of the model to be translated
	            'languages' => Yii::app()->languageManager->languages, // array of your translated languages. Example : array('fr' => 'Français', 'en' => 'English')
	            'defaultLanguage' => Yii::app()->sourceLanguage, //your main language. Example : 'fr'
	    		'forceOverwrite' => true,
	        )
	    );
	}
	public function defaultScope()
	{
	    return $this->ml->localizedCriteria();
	}
}

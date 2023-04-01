<?php
class BlocAchievement extends CActiveRecord
{
	public function tableName()
	{
		return 'bloc_achievement';
	}

	public function rules()
	{
		$purifier = new CHtmlPurifier();
		$purifier->options = Yii::app()->params['htmlPurifierOptions'];
		return array(
			array('user_id, name, description', 'required'),
			array('set_id', 'safe'),
			array('name', 'length', 'max'=>255),
			array('description','filter','filter'=>array($purifier,'purify')),
			array('id, user_id, set_id, name, description', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'blocAchievementLangs' => array(self::HAS_MANY, 'BlocAchievementLang', 'bloc_achievement_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title_anchor' => 'Inclure dans l’index de contenu',
			'title_page' => 'Afficher le titre dans la page',
			'title' => 'Titre du bloc',
			'user_id' => 'Utilisateur flickr',
			'set_id' => 'Album flickr à afficher',
			'name' => 'Titre de la réalisation',
			'description' => 'Description de l’album',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('set_id',$this->set_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);

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
	            'localizedAttributes' => array('name', 'description'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	        	'forceOverwrite' => true,
	        ),
	        'bloc' => array(
	            'class' => 'application.components.behaviors.BlocBehavior',
	        ),
			'multilangVirtualAttributes'=>array(
				'class'=>'application.components.behaviors.MultilangVirtualAttributesBehavior',
				'attributes'=>array('title'),
			),
	    );
	}
	
	public function defaultScope()
	{
	    return $this->ml->localizedCriteria();
	}
}

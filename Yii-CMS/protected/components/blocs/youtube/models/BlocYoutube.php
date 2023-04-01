<?php
class BlocYoutube extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bloc_youtube';
	}

	public function rules()
	{
		return array(
			array('link', 'required'),
			array('id, link', 'safe', 'on'=>'search'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title_anchor' => 'Inclure dans l’index de contenu',
			'title_page' => 'Afficher le titre dans la page',
			'title' => 'Titre du bloc',
			'link' => 'Lien de la vidéo Youtube (ex. : http://www.youtube.com/watch?v=UtVNqifKaHf)',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('link',$this->link,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
	    return array(
			 'ml' => array(
	            'class' => 'application.models.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('link'),
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

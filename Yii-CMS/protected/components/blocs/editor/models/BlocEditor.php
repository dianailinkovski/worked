<?php
class BlocEditor extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bloc_editor';
	}

	public function rules()
	{
		$purifier = new CHtmlPurifier();
		$purifier->options = Yii::app()->params['htmlPurifierOptions'];

		return array(
			array('html','filter','filter'=>array($purifier,'purify')),
			array('id, html', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title_anchor' => 'Inclure dans l’index de contenu',
			'title_page' => 'Afficher le titre dans la page',
			'title' => 'Titre du bloc',
			'html' => 'Contenu texte',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('html',$this->html,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('html'),
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

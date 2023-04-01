<?php
class ContactUsWidgetForm extends CFormModel
{
	public $email;

	public $personName;
	
	public $message;
	
	public function rules()
	{
		return array(
			array('personName', 'required', 'message'=>Yii::t('common', 'Vous devez entrer votre nom complet.')),
			array('email', 'required', 'message'=>Yii::t('common', 'Vous devez entrer votre adresse courriel.')),
			array('message', 'required', 'message'=>Yii::t('common', 'Vous devez entrer un message.')),
			array('message', 'length', 'max'=>2000),
			array('email, personName', 'length', 'max'=>255),
			array('email', 'email')
		);
	}

	public function attributeLabels()
	{
		return array(
			'email' => Yii::t('common', 'Entrez votre adresse courriel'),
			'personName' => Yii::t('common', 'Entrez votre nom complet'),
			'message' => Yii::t('common', 'Entrez votre message'),
		);
	}
}
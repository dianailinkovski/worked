<?php
class NewsletterSubscription extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'newsletter_subscription';
	}

	public function rules()
	{
		return array(
			array('datetime', 'required'),
			array('email', 'required', 'message'=>Yii::t('newsletterModule.common', 'Vous devez saisir votre adresse courriel.')),
			array('email', 'length', 'max'=>255),
			array('email', 'email', 'message'=>Yii::t('newsletterModule.common', 'L’adresse courriel saisie n’est pas valide.')),
			array('email', 'unique', 'message'=>Yii::t('newsletterModule.common', 'Cette adresse courriel fait déjà partie de la liste d’envoi.')),
			array('id, email, language', 'safe', 'on'=>'search'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => '#',
			'email' => Yii::t('newsletterModule.common', 'Email'),
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('language',$this->language,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
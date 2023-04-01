<?php
class MemberRecoverForm extends CFormModel
{
	public $email;

	public $memberModel;
	

	public function rules()
	{
		return array(
			array('email', 'required'),
			array('email', 'emailExists'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'email'=>Yii::t('memberModule.common', 'Votre adresse courriel'),
		);
	}
	
	public function emailExists($attribute, $params)
	{
		if (!($this->memberModel = Member::model()->findByAttributes(array('email'=>$this->email), "activation_hash = ''")))
			$this->addError('email', Yii::t('memberModule.common', 'Cette adresse courriel n’est pas enregistrée.'));
	}
}

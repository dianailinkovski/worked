<?php

class AdminForm extends CFormModel
{
	public $frequency;
	
	public function rules()
	{
		return array(
			array('frequency', 'required'),
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'frequency'=>'Fréquence d’envoi',
		);
	}
}
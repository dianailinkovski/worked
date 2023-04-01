<?php
class Member extends CActiveRecord
{
	public $confirm_password;

	public $current_password;

	public $requireCurrentPassword=false;


	private $_newRecord=false;

	private $_storedPassword;


	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'member';
	}

	public function rules()
	{
		return array(
			array('email, last_name, first_name', 'required'),
			array('email, last_name, first_name', 'length', 'max'=>255),
			array('recover_hash', 'length', 'max'=>32),
			array('email, last_name, first_name', 'safe', 'on'=>'newAccountForm'),
			array('password, confirm_password', 'required', 'on'=>'newAccountForm'),
			array('last_login_date', 'default', 'setOnEmpty'=>true, 'value'=>null),
			array('last_login_date, recover_time', 'date', 'format'=>'yyyy-MM-dd HH:mm:ss'),
			array('password, confirm_password', 'length', 'min'=>6, 'max'=>32),
			array('current_password', 'safe'),
			array('email', 'email'),
			array('email', 'unique'),
			array('password', 'passwordValidate'),
			array('email, last_name, first_name, created_at', 'safe', 'on'=>'search'),
		);
	}	

	public function attributeLabels()
	{
		return array(
			'id' => '#',
			'email' => Yii::t('memberModule.common', 'Adresse Courriel'),
			'password' => Yii::t('memberModule.common', 'Nouveau mot de passe'),
			'confirm_password' => Yii::t('memberModule.common', 'Confirmation nouveau mot de passe'),
			'current_password' => Yii::t('memberModule.common', 'Mot de passe actuel'),
			'first_name' => Yii::t('memberModule.common', 'Prénom'),
			'last_name' => Yii::t('memberModule.common', 'Nom'),
			'created_at' => Yii::t('memberModule.common', 'Date de création'),
			'last_login_date' => Yii::t('memberModule.common', 'Date de dernière connexion'),
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('last_login_date',$this->last_login_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	// Allow saving with current password or with empty if existing model otherwise add validation rule to require password
	public function passwordValidate($attribute, $params)
	{	
		if (!$this->isNewRecord && $this->password == $this->_storedPassword){}
		elseif ($this->password != $this->confirm_password)
		{
			$error = Yii::t('memberModule.common', 'Mot de passe et confirmation de mot de passe doivent être similaires.');
			$this->addErrors(array('password'=>$error));
		}
		elseif ($this->password != '' && $this->requireCurrentPassword && md5($this->current_password) != $this->_storedPassword)
		{
			$error = Yii::t('memberModule.common', 'Votre mot de passe actuel est incorrect.');
			$this->addErrors(array('current_password'=>$error));
		}
	}
	
	public function beforeValidate()
	{
		if (!$this->isNewRecord)
			$this->_storedPassword = self::model()->findByPk($this->id)->password;
		
		if ($this->isNewRecord && $this->password == '')
		{
			$validators = $this->getValidatorList();
			$validators->add(CValidator::createValidator('required', $this, 'password'));
		}
		return parent::beforeValidate();
	}
	
	public function beforeSave() 
	{
		if (!$this->isNewRecord && $this->password == $this->_storedPassword){}
		elseif (!$this->isNewRecord && $this->password == '')
			$this->password = $this->_storedPassword;
		else
			$this->password = md5($this->password);

		if ($this->isNewRecord)
			$this->_newRecord = true;

		return parent::beforeSave();
	}

	public function afterSave() 
	{
		if ($this->_newRecord)
			Yii::app()->authManager->assign('Member', 'Member-'.$this->id);

		return parent::afterSave();
	}

	public function beforeDelete()
	{
		Yii::app()->authManager->revoke('Member', 'Member-'.$this->id);
		return parent::beforeDelete();
	}
}
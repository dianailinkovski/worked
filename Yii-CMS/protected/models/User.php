<?php
class User extends CActiveRecord
{
	public $confirm_password;
	
	private $_storedPassword;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'user';
	}

	public function rules()
	{
		return array(
			array('username', 'required'),
			array('username', 'unique'),
			array('password, confirm_password', 'length', 'min'=>6, 'max'=>32),
			array('password', 'passwordValidate'),
			array('id, username, password', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => '#',
			'username' => 'Nom d’utilisateur',
			'password' => 'Mot de passe',
			'confirm_password' => 'Confirmation de mot de passe',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function passwordValidate($attribute, $params)
	{	
		// Allow saving with current password or with empty if existing model otherwise add validation rule to require password
		
		if (!$this->isNewRecord && $this->password == $this->_storedPassword){}
		elseif ($this->password != $this->confirm_password)
		{
			$error = Yii::t('memberModule.common', 'Mot de passe et confirmation de mot de passe doivent être similaires.');
			$this->addErrors(array('password'=>$error));
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

		return parent::beforeSave();
	}

	public function afterSave()
	{
		Yii::app()->authManager->assign('Admin', 'Admin-'.$this->id);	
		return parent::afterSave();
	}

	public function beforeDelete()
	{
		Yii::app()->authManager->revoke('Admin', 'Admin-'.$id);
		return parent::beforeDelete();
	}
}
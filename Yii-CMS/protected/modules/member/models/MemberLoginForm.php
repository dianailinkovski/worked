<?php
class MemberLoginForm extends CFormModel
{
	public $email;

	public $password;

	public $rememberMe;


	private $_identity;
	

	public function rules()
	{
		return array(
			array('email, password', 'required'),
			array('rememberMe', 'boolean'),
			array('password', 'authenticate'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'password'=>Yii::t('memberModule.common', 'Mot de passe Label'),
			'email'=>Yii::t('memberModule.common', 'Courriel'),
			'rememberMe'=>Yii::t('memberModule.common', 'Se rapeller de moi la prochaine fois'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new MemberIdentity($this->email, $this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password', Yii::t('memberModule.common', 'Courriel ou mot de passe invalide.'));
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new MemberIdentity($this->email, $this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===MemberIdentity::ERROR_NONE)
		{
			$duration = $this->rememberMe ? 3600*24*365 : 0; // 1 year
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}

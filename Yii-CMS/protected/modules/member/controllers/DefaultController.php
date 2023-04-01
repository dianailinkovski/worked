<?php
class DefaultController extends Controller
{
	public $layout='//layouts/column1';

	public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'roles'=>array('Member'),
            ),
            array('allow',
                'actions'=>array('login', 'recover', 'recoverpassword', 'recoverconfirm', 'recoverexpire', 'recoversuccess'),
            ),
            array('deny',
            ),
        );
    }

	public function actionAccount()
	{
		$member = $this->memberModel;
		$member->password = '';
		$member->requireCurrentPassword = true;
		
		if (isset($_POST['Member']))
		{
			$member->attributes = $_POST['Member'];

			if ($member->save())
				$this->refresh();
		}
		$this->render('account', array(
			'model'=>$member
		));
	}
	
	public function actionLogin() 
	{
		$memberLoginForm=new MemberLoginForm;

		if(isset($_POST['MemberLoginForm']))
		{
			$memberLoginForm->attributes=$_POST['MemberLoginForm'];

			if($memberLoginForm->validate() && $memberLoginForm->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		$this->render('login', array(
			'memberLoginForm'=>$memberLoginForm,
		));
	}

	public function actionLogout()
	{
		Yii::app()->user->logout(false);
		$this->redirect(Yii::app()->homeUrl);
	}
	
	public function actionRecover()
	{
		$memberRecoverForm = new MemberRecoverForm();
		
		if (isset($_POST['MemberRecoverForm']))
		{
			$memberRecoverForm->attributes = $_POST['MemberRecoverForm'];
			
			if ($memberRecoverForm->validate())
			{
				$time = date('Y-m-d H:i:s');
				$hash = md5(rand(0, 2147483648));
				$language = Yii::app()->language;
				
				$memberModel = $memberRecoverForm->memberModel;
				$memberModel->recover_hash = $hash;
				$memberModel->recover_time = $time;
				
				if (!$memberModel->save())
					throw new CHttpException(500);

				$mailer = Yii::createComponent('application.extensions.mailer.EMailer');
			       		
				$mailer->Host = Yii::app()->params['mail']['Host'];
				$mailer->IsSMTP();
				$mailer->SMTPAuth = true;
				$mailer->Username = Yii::app()->params['mail']['Username'];
				$mailer->Password = Yii::app()->params['mail']['Password'];
				$mailer->Port = Yii::app()->params['mail']['Port'];
						
				$mailer->From = Yii::app()->params['mail']['From'];
				$mailer->FromName = Yii::app()->params['mail']['FromName'];
	
				$mailer->IsHTML(true);
				$mailer->CharSet = 'UTF-8';
				$mailer->AltBody = "Your e-mail program does not support HTML, the content of this email could not be displayed.";
					
				$mailer->AddAddress($memberRecoverForm->email);

				$mailer->Subject = Yii::t('memberModule.common', 'Récupération de mot de passe');
				$mailer->Body = Yii::t('memberModule.common', 'recovery_email', array('{linkUrl}'=>$this->createAbsoluteUrl('/member/default/recoverpassword', array('uid'=>$hash))));
					
	        	if ($mailer->Send())
	        		$this->redirect($this->createUrl('/member/default/recoverconfirm'));
	        	else
	        		throw new CHttpException(500);
			}
		}
		
		$this->render('recover', array(
			'memberRecoverForm'=>$memberRecoverForm,
		));
	}
	
	public function actionRecoverpassword($uid)
	{
		if (!($memberModel = Member::model()->findByAttributes(array('recover_hash'=>$uid), 'recover_time > :time', array(':time'=>date('Y-m-d H:i:s', time() - (60 * 60))))))
			$this->redirect($this->createUrl('/member/default/recoverexpire'));
		
		$memberModel->password = '';
		
		if (isset($_POST['Member']))
		{
			$memberModel->attributes = $_POST['Member'];
			
			$validators = $memberModel->getValidatorList();
			$validators->add(CValidator::createValidator('required', $memberModel, 'password'));

			if ($memberModel->validate())
			{
				$memberModel->recover_hash = '';
				$memberModel->recover_time = null;
				$memberModel->save();
				$this->redirect($this->createUrl('/member/default/recoversuccess'));
			}
		}
		
		$this->render('recoverPassword', array(
			'memberModel'=>$memberModel,
		));
	}
	
	public function actionRecoverconfirm()
	{
		$this->render('recoverConfirm', array(
		));
	}
	
	public function actionRecoverexpire()
	{
		$this->render('recoverExpire', array(
		));
	}
	
	public function actionRecoversuccess()
	{
		$this->render('recoverSuccess', array(
		));
	}
}
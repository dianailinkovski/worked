<?php
Yii::import('member.models.*');

/**
 * New account form.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class NewAccountFormWidget extends CWidget
{
    protected function renderContent()
    {
		$member = new Member;
		
		$member->scenario = 'newAccountForm';
		
		if (isset($_GET['ahash']))
		{
			if (($existingMember = Member::model()->findByAttributes(array('activation_hash'=>$_GET['ahash']))) 
				&& $existingMember->activation_time >= date('Y-m-d H:i:s', strtotime('-1 day')))
			{
				$existingMember->activation_hash = '';
				$existingMember->activation_time = null;
				$existingMember->save();

				$originalLayout = $this->controller->layout;
				$this->controller->layout='//layouts/email';
				
				$to = $existingMember->email;
				$subject = Yii::t('memberModule.common', 'Bienvenue');
				$body = $this->controller->render('member.components.views.emailWelcome', array(), true);
				$this->controller->layout = $originalLayout;
				
				Helper::sendMail($to, $subject, $body);
				
				// Login user
				Yii::app()->authManager->assign('Member', 'Member-'.$existingMember->id);
				
				$identity = new MemberIdentity($existingMember->email, '');
				$identity->authenticate(true);
				Yii::app()->user->login($identity, 0);
				
				$this->controller->redirect($this->controller->createUrl('/member/default/account', array('new'=>1)));
			}
			else {
				$this->controller->redirect($this->controller->createUrl('/site/index', array('message'=>'newaccountexpired')));
			}
		}
		if (isset($_POST['Member']))
		{
			if (($existingMember = Member::model()->findByAttributes(array('email'=>$_POST['Member']['email']), "activation_hash <> ''")))
				$member = $existingMember;
			
			$member->scenario = 'newAccountForm';

			$member->attributes = $_POST['Member'];
			$member->activation_hash = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 32);
			$member->activation_time = date('Y-m-d H:i:s');

			if ($member->save())
			{			
				$originalLayout = $this->controller->layout;
				$this->controller->layout='//layouts/email';
				
				$to = $member->email;
				$subject = Yii::t('memberModule.common', 'Votre nouveau compte a besoin d’être activé');
				$body = $this->controller->render('member.components.views.emailActivation', array('member'=>$member), true);
				$this->controller->layout = $originalLayout;
				
				Helper::sendMail($to, $subject, $body);
				
				$this->controller->redirect($this->controller->createUrl('/site/index', array('message'=>'newaccount')));
			}
		}
		$this->render('newAccountFormWidget', array(
			'member'=>$member,
		));
    }
}
?>
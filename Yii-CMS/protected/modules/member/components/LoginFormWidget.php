<?php
Yii::import('member.models.*');
Yii::import('member.components.MemberIdentity');

/**
 * Member login form.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class LoginFormWidget extends CWidget
{
    public function run()
    {
		$memberLoginForm=new MemberLoginForm;
		
		if(isset($_POST['MemberLoginForm']))
		{
			$memberLoginForm->attributes=$_POST['MemberLoginForm'];

			if($memberLoginForm->validate() && $memberLoginForm->login())
			{
				$this->controller->redirect(Yii::app()->user->returnUrl);
			}
		}
		$this->render('loginFormWidget', array(
			'memberLoginForm'=>$memberLoginForm,
		));
    }
}
?>
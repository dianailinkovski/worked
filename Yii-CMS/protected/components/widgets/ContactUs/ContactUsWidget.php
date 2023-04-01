<?php
Yii::import('application.components.widgets.ContactUs.models.*');

/**
 * Contact Us Widget
 *
 * Render a form to send an email
 *
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class ContactUsWidget extends CWidget
{
    /**
     * @var boolean wether to use the logged in user to pre-fill the form. Defaults to false.
     */
	public $getEmailFromConnectedMember=false;
    /**
     * @var string the view file to render. Defaults to application.components.widgets.ContactUs.views.emailContactUsWidget.
     */
	public $emailViewFile='application.components.widgets.ContactUs.views.emailContactUsWidget';
    /**
     * @var string the subject of the email. Required.
     */
	public $emailSubject;
    /**
     * @var string the email to send to. Required.
     */
	public $emailTo;
    

    public function run()
    {
    	$formModel = new ContactUsWidgetForm;
    	
    	if ($this->getEmailFromConnectedMember && isset($this->controller->memberModel->email))
    		$formModel->email = $this->controller->memberModel->email;
    	
    	if (isset($_POST['ContactUsWidgetForm']) && Yii::app()->request->isAjaxRequest)
    	{
    		$formModel->attributes = $_POST['ContactUsWidgetForm'];

    		$valid = CActiveForm::validate($formModel);
    		if ($valid == '[]')
    		{
    			$message = $this->controller->renderPartial($this->emailViewFile, array(
    				'message' => $formModel->message,
    				'email' => $formModel->email,
					'personName' => $formModel->personName,
    			), true);
    			
    			if (Helper::sendMail($this->emailTo, $this->emailSubject, $message) !== true)
    			{
    				echo "\n".'<div id="contact-form-json">'."\n";
    				echo CJSON::encode(array(
    					'status'=>'error',
    				));
    				echo "\n".'</div>'."\n";
    			}
    			else
    			{
    				echo "\n".'<div id="contact-form-json">'."\n";
    				echo CJSON::encode(array(
    					'status'=>'success',
    				));
    				echo "\n".'</div>'."\n";
    			}
    			Yii::app()->end();
    		}
    		else {
    			echo "\n".'<div id="contact-form-json">'."\n";
    			echo $valid;
    			echo "\n".'</div>'."\n";
    			Yii::app()->end();
    		}
    	}
    	
        $this->render('contactUsWidget', array(
        	'formModel'=>$formModel,
        ));
    }
}
?>
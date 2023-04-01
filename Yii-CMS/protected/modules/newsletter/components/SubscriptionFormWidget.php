<?php
Yii::import('newsletter.models.*');
Yii::import('newsletter.messages.*');

/**
 * Show an ajax newsletter subscription form.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class SubscriptionFormWidget extends CWidget
{
    public function run()
    {
    	$formModel = new NewsletterSubscription;

    	if (isset($_POST['NewsletterSubscription']) && Yii::app()->request->isAjaxRequest)
    	{
    		$formModel->attributes = $_POST['NewsletterSubscription'];
    		$formModel->language = Yii::app()->language;
    		$formModel->datetime = date('Y-m-d H:i:s');

    		$valid = CActiveForm::validate($formModel);
    		if ($valid == '[]')
    		{
				$formModel->save(false);
				
    			echo "\n".'<div id="newsletter-subscription-form-json">'."\n";
    			echo CJSON::encode(array(
    				'status'=>'success',
    			));
    			echo "\n".'</div>'."\n";

    			Yii::app()->end();
    		}
    		else {
    			echo "\n".'<div id="newsletter-subscription-form-json">'."\n";
    			echo $valid;
    			echo "\n".'</div>'."\n";
    			Yii::app()->end();
    		}
    	}
    	
        $this->render('subscriptionFormWidget', array(
        	'formModel'=>$formModel,
        ));
    }
}
?>
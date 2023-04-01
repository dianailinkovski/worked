<?php
class DefaultController extends Controller
{
	public $layout='//layouts/column1';

	public function actionUnsuscribe($token)
	{
		$entry = unserialize(base64_decode($token));

		if (!($model = NewsletterSubscription::model()->findByAttributes(array('id'=>(int)$entry['id'], 'email'=>$entry['email']))))
			throw new CHttpException(403);
		else
			$model->delete();
					
		$this->render('unsuscribe');
	}
}

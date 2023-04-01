<?php
class InstallController extends CController
{
	public function actionIndex()
	{
		Yii::app()->installer->install();
		
		$this->render('success');
	}
}
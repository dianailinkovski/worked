<?php
class SettingsController extends BackController
{
	public $layout='//adminLayouts/withMenu';
	
	public $sectionLabel;
	
	public function init()
	{
		if (!Yii::app()->userBack->checkAccess('adminSettings'))
			Yii::app()->user->loginRequired();
		
		$this->sectionLabel = Yii::t('admin', 'Settings');
		return parent::init();
	}

	public function actionIndex()
	{		
 		$flickrUsers = FlickrUser::model()->findAll('', array('index'=>'id'));

		$formManager = new FormManager(array(
			'checkIfPosted'=>false,
			'redirect'=>$this->createUrl('index'),
			'forms' => array(
				'id'=>'flickrUsersForm',
				'models'=>$flickrUsers,
			),
 		));
		
		if (isset($_POST['sent']))
			$formManager->process();

 		$this->render('index',array(
  		    'flickrUsers'=>$formManager->getModels('flickrUsersForm'),
 		));
	}
}
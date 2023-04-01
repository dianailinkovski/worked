<?php

class DefaultController extends Controller
{
	public $layout='//layouts/column2';

	public function init()
	{
		Yii::app()->clientScript->registerCssFile($this->module->assetsUrl.'/css/contest.css');

		return parent::init();
	}
	
	public function actionIndex($archives=null, $page=null)
	{
		if (Yii::app()->user->getState('siteVersion') == 'mobile') {
			Yii::app()->clientScript->registerCssFile('/css/mobile/blocs.css');
			$this->layout='//mobileLayouts/column1';
		}
		
		$contestProvider = new CActiveDataProvider('Contest', array(
			'criteria' => array(
				'order' => 'start_date DESC',
				'condition' => 'status=:status',
				'params' => array(
					'status' => $archives === null ? 'active' : 'archived',		
				),
			),
			'pagination' => array(
				'pageSize' => 10
			)
		));
		
		if (isset($page))
		{
			$page = (int)$page - 1;
			$contestProvider->pagination->currentPage = $page;
		}
		
		$languageManager = Yii::app()->languageManager;
		$languageManager->translatedGetVars['archives'] = array();
		foreach (array_keys($languageManager->languages) as $language)
		{
			$languageManager->translatedGetVars['archives'][$language] = $this->module->archivesVarName[$language];
		}
		
		$this->render('index', array(
			'contestProvider'=>$contestProvider,
			'archives'=>$archives,
		));
	}

	public function actionDetail($n, $confirmation=0)
	{
		if (Yii::app()->user->getState('siteVersion') == 'mobile') {
			Yii::app()->clientScript->registerCssFile('/css/mobile/blocs.css');
			$this->layout='//mobileLayouts/column1';
		}
		
		if (!($contest = Contest::model()->with(array('fields'=>array('order'=>'fields.rank ASC'), 'fields.multi'=>array('order'=>'multi.rank ASC')))->find('i18nContest.l_title_url=:n', array('n'=>$n))))
			throw new CHttpException(404,'The requested page does not exist.');
		
		$currentDate = date('Y-m-d H:i:s');
			
		$contestMultilang = Contest::model()->multilang()->find('i18nContest.l_title_url=:n', array('n'=>$n));
		
		Yii::app()->languageManager->translatedGetVars['n'] = array();
		foreach (array_keys(Yii::app()->languageManager->languages) as $language)
		{
			Yii::app()->languageManager->translatedGetVars['n'][$language] = $contestMultilang->{'title_url_'.$language};
		}
		
		// In case detail page is accessed directly and contest is inactive.
		if ($contest->status == "inactive")
			$this->redirect($this->createUrl('index'));

		if ($confirmation)
		{
			$this->render('confirmation', array(
				'contest'=>$contest,
			));
		}
		elseif (($contest->end_date !== null && $contest->end_date < $currentDate) || $contest->status == "archived")
		{
			$this->render('conclusion', array(
				'contest'=>$contest,
			));
		}
		else {
			$entry = new EntryForm;
			$validators = $entry->getValidatorList();
			$required = 'verify_code, ';
			$files = '';
			$length = '';
			$email = '';
			$entry->verify_code = null;
			$entry->attributeLabels['verify_code'] = Yii::t('contestModule.common', 'Code de sécurité');
			$validators->add(CValidator::createValidator('captcha', $this, 'verify_code', array('message'=>Yii::t('contestModule.common', 'Le code saisi est invalide. Veuillez entrer le code à nouveau.'), 'allowEmpty'=>!CCaptcha::checkRequirements(), 'captchaAction'=>'/site/captcha')));
			
			foreach ($contest->fields as $field)
			{
				$entry->{$field->id} = null;
				$entry->attributeLabels[$field->id] = CHtml::encode($field->title);
				
				if ($field->required)
					$required .= $field->id.', ';

				$length .= $field->id.', ';
				
				if ($field->type == 'email')
					$email .= $field->id.', ';
	
				if ($field->type == 'file')
				{
					$files = $field->id.', '; 
					
					$behaviors = array(
			        	'class' => 'application.models.behaviors.UploadingBehavior.ModelUploadingBehavior',
			        	'attribute' => $field->id,
						'dir' => 'files/_user/contest_entries',
						'tempDir' => 'files/_user/contest_entries/_temp',
						'cacheTime' => 10 * 24 * 60 * 60, // 10 days
						'uploadcare' => null, 
						'allowDelete' => false,
		        	);

					$entry->{$field->id.'Handler'} = $entry->attachBehavior($field->id.'Handler', $behaviors);
					$entry->behaviors = array($behaviors);
				}
			}
			if ($required != '')
				$validators->add(CValidator::createValidator('required', $this, substr($required, 0, -2)));
			if ($length != '')
				$validators->add(CValidator::createValidator('length', $this, substr($length, 0, -2), array('max'=>1000)));
			if ($email != '')
				$validators->add(CValidator::createValidator('email', $this, substr($email, 0, -2)));
			if ($files != '')
				$validators->add(CValidator::createValidator('file', $this, substr($files, 0, -2), array('allowEmpty'=>true)));
			
			if (isset($_POST['EntryForm']))
			{
				foreach ($_POST['EntryForm'] as $id => $attribute)
				{
					if (is_array($attribute))
						$entry->{$id} = serialize($attribute);
					else
						$entry->{$id} = $attribute;
				}

				if ($entry->validate())
				{
					$entryModel = new ContestEntry;
					$entryModel->contest_id = $contest->id;
					$entryModel->ip = $_SERVER['REMOTE_ADDR'];
					$entryModel->save();

					foreach ($contest->fields as $field)
					{
						if ($field->type == 'checkbox')
							$entry->{$field->id} = unserialize($entry->{$field->id});
							
						if (is_array($entry->{$field->id}))
						{
							foreach ($entry->{$field->id} as $entryValue)
							{
								$model = new ContestEntryItem;
								$model->contest_entry_id = $entryModel->id;
								$model->contest_field_id = $field->id;
								$model->content = $entryValue;
								$model->save();
							}
						} 
						else {
							$model = new ContestEntryItem;
							$model->contest_entry_id = $entryModel->id;
							$model->contest_field_id = $field->id;
							$model->content = $entry->{$field->id};
							$model->save();
						}
					}
					
					if ($contest->send_notification_email == 1)
					{
					    $message = $this->renderPartial('_notificationEmail', array(
		    				'entry' => $entryModel,
					    	'contest'=>$contest,
		    			), true);
		    			
		    			Helper::sendMail($this->module->notificationEmail, 'Nouvelle participation à "'.CHtml::encode($contest->title).'"', $message);
					}
	    			
					$this->redirect(array('detail', 'n'=>$n, 'confirmation'=>1));
				}
				foreach ($contest->fields as $field)
				{
					if ($field->type == 'checkbox')
						$entry->{$field->id} = unserialize($entry->{$field->id});
				}
			}
			$this->render('detail', array(
				'contest'=>$contest,
				'currentDate'=>$currentDate,
				'entry'=>$entry,
			));
		}

	}

	protected function afterRender($view, &$output)
	{
		parent::afterRender($view,$output);
		//Yii::app()->facebook->addJsCallback($js); // use this if you are registering any $js code you want to run asyc
		Yii::app()->facebook->initJs($output); // this initializes the Facebook JS SDK on all pages
		Yii::app()->facebook->renderOGMetaTags(); // this renders the OG tags
		return true;
	}
}

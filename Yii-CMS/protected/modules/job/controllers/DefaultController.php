<?php

class DefaultController extends Controller
{
	public $layout='//layouts/column2';

	public function init()
	{
		Yii::app()->clientScript->registerCssFile($this->module->assetsUrl.'/css/job.css');
	
		return parent::init();
	}
	
	public function actionIndex($confirm=0)
	{	
		$this->layout = '//layouts/column1';
		
		if ($confirm)
			$this->render('cvconfirm');
		else {
			$formModel = new JobForm;
			$cvModel = new JobCv;
			$job = new Job;
			
			$jobProvider = new CActiveDataProvider('Job', array(
				'criteria' => array(
					'condition' => 'active = 1',
					'order' => 'category_id ASC, title ASC',
				),
				'pagination'=>false,
			));
			
			$jobCategory = JobCategory::model()->findAll();
			
			foreach ($jobProvider->getData() as $data)
			{
				$formModel->{$data['id']} = null;
			}
	
			if (isset($_POST['JobForm']))
			{
				$listJob = '<ul>';
				
				$oneChecked = false;
				foreach ($_POST['JobForm'] as $key => $item)
				{
					if (is_numeric($key) && $item == '1')
					{
						$oneChecked = true;
						$jobChecked = Job::model()->findByPk($key);
						$listJob .= '<li>'.$jobChecked->title.' ('.$jobChecked->category->name.')</li>';
					}
				}
				$listJob .= '</ul>';
				$cvModel->date = date('Y-m-d H:i:s');
				$cvModel->cv = $_POST['JobCv']['cv'];
				
				if (!$oneChecked)
					$cvModel->addError('cv', Yii::t('jobModule.common', 'Vous devez cocher au moins un emploi'));
				elseif ($cvModel->save())
				{
					foreach ($_POST['JobForm'] as $attribute => $value)
					{
						if (is_numeric($attribute) && $value == '1')
						{
							$jobId = (int)$attribute;
							
							Yii::app()->db->createCommand('INSERT INTO job_job_cv (job_id, job_cv_id) VALUES (:job_id, :job_cv_id)')->execute(array(':job_id'=>$jobId, ':job_cv_id'=>$cvModel->id));
						}
					}
					
					$subject = 'Postulation pour une offre d’emploi';
					$body = '
					<p>Bonjour,<br/><br/>
					Vous avez reçu une candidature pour un ou plusieurs emplois à partir du site Web d`Arianne Phosphate Inc.<br/>
					'.$listJob.'<br/>
					<a href="'.$this->createAbsoluteUrl('admincv/read', array('id'=>$cvModel->id)).'">Voir les détails de la candidature</a><br/><br/>
					</p>
					';
					$attachment = "./".$cvModel->cvHandler->dir."/".Helper::encodeFileName($cvModel->cv);

					if(($mailerError = Helper::sendMail($this->module->cvEmail, $subject, $body, $attachment)) !== true)
						throw new CHttpException(500, $mailerError);
					
					
					$this->redirect($this->createUrl('index', array('confirm'=>1)));
				}
			}
			$this->render('index', array(
				'jobProvider'=>$jobProvider,
				'categories' =>$jobCategory,
				'formModel' => $formModel,
				'cvModel' => $cvModel,
			));
		}
	}

	public function actionDetail($t, $cvFormSuccess=null)
	{
		if (Yii::app()->user->getState('siteVersion') == 'mobile') {
			Yii::app()->clientScript->registerCssFile('/css/mobile/blocs.css');
			$this->layout='//mobileLayouts/column1';
		}
		
		if (!is_dir('files/_user/job'))
			mkdir('files/_user/job');
		if (!is_dir('files/_user/job/cv'))
			mkdir('files/_user/job/cv');
				
		$this->sidebarViewFile = '/layouts/_sidebar';
		
		if (!($job = Job::model()->find('i18nJob.l_title_url=:t', array('t'=>$t))))
			throw new CHttpException(404,'The requested page does not exist.');
			
		$jobMultilang = Job::model()->multilang()->find('i18nJob.l_title_url=:t', array('t'=>$t));
		
		Yii::app()->languageManager->translatedGetVars['t'] = array();
		foreach (array_keys(Yii::app()->languageManager->languages) as $language)
		{
			Yii::app()->languageManager->translatedGetVars['t'][$language] = $jobMultilang->{'title_url_'.$language};
		}

		$this->sidebarData['currentJobId'] = $job->id;	// Pour identification de l'emploi actuellement affiché.
		$this->sidebarData['jobs'] = Job::model()->findAll(array('condition'=>'category_id=:category_id AND active = 1', 'order'=>'title ASC', 'params'=>array(':category_id'=>$job->category_id)));

		$modelCv = new JobCv();
		
		if (isset($_POST['JobCv']))
		{
			$modelCv->attributes = $_POST['JobCv'];
			$modelCv->date = date('Y-m-d h:i:s');
				
			if ($modelCv->save())
			{
				Yii::app()->db->createCommand('INSERT INTO job_job_cv (job_id, job_cv_id) VALUES (:job_id, :job_cv_id)')->execute(array(':job_id'=>$job->id, ':job_cv_id'=>$cvModel->id));
				
				$subject = 'Postulation pour l’offre d’emploi '.CHtml::encode($job->title);
				$body = '
				<p>Bonjour,<br/><br/>
				Vous avez reçu une postulation pour le poste cité en objet.<br/><br/>
				<a href="http://'.$_SERVER['HTTP_HOST'].'/files/_user/jobcv/'.Helper::encodeFileName($modelCv->cv).'" title="Cliquez ici pour télécharger le curriculum vitae">Cliquez ici pour télécharger le curriculum vitae</a></p>
				';
	
				if(($mailerError = Helper::sendMail($this->module->cvEmail, $subject, $body)) !== true)
					throw new CHttpException(500, $mailerError);
				
				$this->redirect($this->createUrl('detail', array('cvFormSuccess'=>1, 't'=>$t))."#msg");
			}
		}
		$this->render('detail', array(
			'job'=>$job,
			'modelCv'=>$modelCv,
			'cvFormSuccess'=>$cvFormSuccess,
		));
	}
	
	protected function afterRender($view, &$output)
	{
		parent::afterRender($view,$output);
		Yii::app()->facebook->initJs($output); // this initializes the Facebook JS SDK on all pages
		Yii::app()->facebook->renderOGMetaTags(); // this renders the OG tags
		return true;
	}
	
	public function actionRss()
	{
		// disabling web log
		foreach (Yii::app()->log->routes as $route)
		{
			if ($route instanceof CWebLogRoute)
				$route->enabled = false;
		}
	
		Yii::import('ext.feed.*');
	
		$feed = new EFeed();
			
		$feed->title = Yii::app()->name.' | '.Yii::t('jobModule.common', 'Offres demplois');
		$feed->description = Yii::app()->name.' | '.Yii::t('jobModule.common', 'Offres demplois');
	
		$feed->addChannelTag('language', Yii::app()->language);
		$feed->addChannelTag('pubDate', date(DATE_RSS, time()));
		$feed->addChannelTag('link', $this->createAbsoluteUrl('index'));
	
		if (($jobs = Job::model()->findAll(array('order'=>'publication_date DESC', 'limit'=>25, 'condition'=>"publication_date <= '".date('Y-m-d H:i:s')."' AND active = 1"))))
		{
			foreach ($jobs as $job)
			{
				$item = $feed->createNewItem();
					
				$item->title = $job->title;
				$item->link = $this->createAbsoluteUrl('/job/default/detail', array('t'=>$job->title_url));
				$item->date = $job->publication_date;
				$type = '';
				switch($job->type){
					case '1': $type="Permanent"; break;
					case '2': $type="Temps partiel"; break;
					case '3': $type="Saisonnier"; break;
				}
				$item->description = "
					<p>
						Type de l’emploi : ".$type."<br/>
						Date de début : ".((isset($job->start_date) and $job->start_date != '0000-00-00') ? Helper::formatDate($job->start_date, "reg") : Yii::t('jobModule.common', 'Indéterminée'))."<br/>
						Date et heure limite pour postuler : ".((isset($job->postulation_end_date)) ? substr(Helper::formatDate($job->postulation_end_date, "reg+time"), 0, -3) : Yii::t('jobModule.common', 'Indéterminée'))."
					</p>		
				".strip_tags($job->description);
	
				$feed->addItem($item);
			}
		}
		$feed->generateFeed();
		Yii::app()->end();
	}
}

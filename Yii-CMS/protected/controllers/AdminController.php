<?php

class AdminController extends BackController
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		if (!Yii::app()->user->checkAccess('Admin'))
			$this->redirect($this->createUrl('/admin/login'));
			
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		// disabling web log because it creates visual problems
	    foreach (Yii::app()->log->routes as $route)
        {
            if ($route instanceof CWebLogRoute)
                $route->enabled = false;
        }

		$this->layout = '//adminLayouts/mainEmpty';

		/*
		// return from google
		if (isset($_GET['code'])) 
		{
			$this->googleClient->authenticate($_GET['code']);
			Yii::app()->session['google_access_token'] = $this->googleClient->getAccessToken();
			$this->redirect(Yii::app()->user->getReturnUrl($this->createUrl('/admin')));
		}
		// if google returns an error, redirect anyway because it's optional
		if (isset($_GET['error']))
			$this->redirect(Yii::app()->user->getReturnUrl($this->createUrl('/admin')));
		*/
		$model=new AdminLoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['AdminLoginForm']))
		{
			$model->attributes=$_POST['AdminLoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
			{
				/*
				$this->googleClient->setScopes(array('https://www.googleapis.com/auth/drive', 'https://www.googleapis.com/auth/drive.readonly'));
				
				if (!isset(Yii::app()->session['google_access_token']) || is_null(Yii::app()->session['google_access_token']))
					$this->redirect($this->googleClient->createAuthUrl());
				else
					*/
					$this->redirect(Yii::app()->user->getReturnUrl($this->createUrl('/feed/admin')));
			}
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}
	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		if (!Yii::app()->user->checkAccess('Admin'))
			throw new CHttpException(404,'You do not have access to this section.');
		
		//unset(Yii::app()->session['google_access_token']);
		//$this->googleClient->revokeToken();
			
		Yii::app()->user->logout(false);
		$this->redirect($this->createUrl('/admin/login'));
	}
	
	public function actionCkeditorupload($type)
	{
		$funcNum = $_GET['CKEditorFuncNum'];
		$uploadFolder = Yii::app()->params['ckeditorUploadDir'];
		$uploadedFile = CUploadedFile::getInstanceByName('upload');
		$url = '';
		$message = '';

		if (is_object($uploadedFile))
		{
			if ($uploadedFile->size <= 0)
			{
				$message = Yii::t('admin', 'The file is of zero length.');
			}
			else if ($type == 'image' && ($uploadedFile->type != 'image/pjpeg' && $uploadedFile->type != 'image/jpeg' && $uploadedFile->type != 'image/png'))
			{
				$message = Yii::t('admin', 'The image must be in either JPG or PNG format. Please upload a JPG or PNG instead.');
			}
			else if ($type == 'type' && ($uploadedFile->type != 'application/pdf' && $uploadedFile->type != 'application/x-pdf'))
			{
				$message = Yii::t('admin', 'The file must be in one of the following formats : PDF.');
			}
			else
			{
				$fileNameClean = strtolower(preg_replace('/[^A-Za-z0-9_\-\.]|\.(?=.*\.)/', '', str_replace(' ', '_', Helper::removeAccents($uploadedFile))));
				if (strpos($fileNameClean, '.') === 0)
					$fileNameClean = 'file'.$fileNameClean;
				
				$i = -1;
				while (true)
				{
					$i++;
					
					if ($i > 0)
						$fileName = Helper::fileSuffix($fileNameClean, $i);
					else
						$fileName = $fileNameClean;

					if (file_exists($uploadFolder.'/'.$fileName))
						continue;
						
					break;
				}
				if ($type == 'image')
				{
					$tempFile = $uploadFolder.'/'.Helper::fileSuffix($fileName, '_t');
					$uploadedFile->saveAs($tempFile);

					$image = Yii::app()->image->load($tempFile);
					$image->quality(90)->sharpen(15);
					
					if ($image->width > 1000 || $image->height > 1000)
						$image->resize(1000, 1000);
				
					$image->save($uploadFolder.'/'.$fileName);
					unlink($tempFile);
				}
				else
					$uploadedFile->saveAs($uploadFolder.'/'.$fileName);

				$url = Yii::app()->request->baseUrl.'/'.$uploadFolder.'/'.$fileName;
			}
		}
		else 
			$message = Yii::t('admin', 'There was an error uploading your file.');
		
		echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(".$funcNum.", '".$url."', '".$message."');</script>";
	}
	
	public function actionGridviewsort($modelName, $attributeName)
	{
		$rank = (int)$_POST['rank'];
		$id = (int)$_POST['id'];
		
		$item = $modelName::model()->findByPk($id);

		if ($item->$attributeName < $rank)
			Yii::app()->db->createCommand('UPDATE '.$modelName::model()->tableName().' SET '.$attributeName.' = '.$attributeName.'-1 WHERE '.$attributeName.' > :rank1 AND '.$attributeName.' <= :rank2')->execute(array(':rank1'=>$item->$attributeName, ':rank2'=>$rank));
        else
        	Yii::app()->db->createCommand('UPDATE '.$modelName::model()->tableName().' SET '.$attributeName.' = '.$attributeName.'+1 WHERE '.$attributeName.' < :rank1 AND '.$attributeName.' >= :rank2')->execute(array(':rank1'=>$item->$attributeName, ':rank2'=>$rank));
        
        $item->$attributeName = $rank;
        $item->save();
        
        Yii::app()->end();
	}
	
	/**
	 * Ajax repsonse for alias structure widget
	 */
	public function actionAliaspathajax()
	{
		if (!Yii::app()->user->checkAccess('Admin'))
			throw new CHttpException(404,'You do not have access to this section.');
			
		if (!YII_DEBUG && !Yii::app()->request->isAjaxRequest) {
	        throw new CHttpException('403', 'Forbidden access.');
	    }
		if (empty($_GET['id'])) {
	       throw new CHttpException('404', 'Missing "id" GET parameter.');
	    }
	    $path = AdminHelper::pathFromId($_GET['id']);
	    header('Content-type: application/json;');
	    
	    echo json_encode(array('path'=>$path));
	}
	
	
	public function actionBlocflickrsets()
	{
		if (!Yii::app()->user->checkAccess('Admin'))
			throw new CHttpException(404,'You do not have access to this section.');
			
		if (!YII_DEBUG && !Yii::app()->request->isAjaxRequest) {
	        throw new CHttpException('403', 'Forbidden access.');
	    }
		if (empty($_POST['user_id'])) {
	       return;
	    }
		$curl = curl_init("https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key=cd80122ae0a0f805b279d80715dd7861&user_id=".urlencode($_POST['user_id'])."&format=json&nojsoncallback=1");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($curl);
		curl_close($curl);
		$mObject = json_decode($data, false); // stdClass object
		
		$returnArray = array(''=>'');		
		if (isset($mObject->photosets->photoset) && !empty($mObject->photosets->photoset))
		{
			foreach ($mObject->photosets->photoset as $photoset)
			{
				$returnArray[$photoset->id] = CHtml::encode($photoset->title->_content);
			}
		}
	    header('Content-type: application/json;');
	    
	    echo json_encode($returnArray);
	}
}
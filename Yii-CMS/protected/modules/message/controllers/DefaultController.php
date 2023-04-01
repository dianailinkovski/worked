<?php

class DefaultController extends Controller
{
	public $layout=false;
	
	public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'roles'=>array('Member'),
            ),
            array('deny',
                'deniedCallback'=>function () {
            		echo '<script type="text/javascript">window.location.replace("'.$this->createUrl(CHtml::normalizeUrl(Yii::app()->user->loginUrl)).'");</script>';
        		}
            ),
        );
    }

	public function actionModal()
	{
		if (Yii::app()->request->isAjaxRequest)
		{	
			$messages = MessageAssoc::model()->with('message')->findAllByAttributes(array(
					'member_id'=>$this->memberModel->id,
				), 
				array(
					'condition'=>"message.datetime <= '".date('Y-m-d H:i:s')."'",
					'order'=>'message.datetime DESC',
				)
			);
			Yii::app()->db->createCommand('UPDATE message_assoc AS t INNER JOIN message AS t2 ON t.message_id = t2.id SET t.seen = 1 WHERE t.member_id=:member_id AND t2.datetime <= :datetime')->execute(array('member_id'=>$this->memberModel->id, 'datetime'=>$currentDateTime));
			
			$this->render('modal', array(
				'messages'=>$messages,
			));
			
			Yii::app()->end();
		}
		else
			throw new CHttpException(500);
	}
	
	public function actionDelete()
	{
		if (Yii::app()->request->isAjaxRequest)
		{	
			if (!isset($_POST['id']))
				throw new CHttpException(400);
			if (($model = MessageAssoc::model()->findByAttributes(array('message_id'=>(int)$_POST['id'], 'member_id'=>$this->memberModel->id))))
				$model->delete();
			
			Yii::app()->end();
		}
		else
			throw new CHttpException(500);
	}
}
<?php
class DefaultController extends Controller
{
	public function actionIndex($id) 
	{
		if (!($page = ContentPage::model()->findByPk($id)))
			throw new CHttpException(404);

		// No sidebar for 1st level pages without children
		if (Yii::app()->cms->currentAlias->isLeaf() && Yii::app()->cms->currentAlias->level == 2) 
		{
			$this->layout = '//layouts/column1';
			$this->rightColumnLayoutParent = '//layouts/column1';
		} 
		else {
			$this->layout = '//layouts/column2';
			$this->rightColumnLayoutParent = '//layouts/column2';
		}
		
		if (isset($this->module->layouts) && $page->layout != 'standard')
		{
			if ($page->layout == 'right_column_1')
			{
				$this->layout = '//layouts/rightColumn';
				$this->rightColumnLayoutType = '//layouts/_rightColumnType1';
			}
			elseif ($page->layout == 'right_column_2')
			{
				$this->layout = '//layouts/rightColumn';
				$this->rightColumnLayoutType = '//layouts/_rightColumnType2';
			}
		}

		$this->render('index',array(
			'page'=>$page
		));
	}
}
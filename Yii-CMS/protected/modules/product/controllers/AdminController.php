<?php
class AdminController extends BackController
{
	public $layout='//adminLayouts/column2';
	
	public $sectionLabel;
	
	public function init()
	{
		$this->sectionLabel = 'Produits';
		return parent::init();
	}
	
	public function actions()
	{
		$formSettings = array(
			'redirect'=>$this->createUrl('admin'),
			'forms' => array(
				'id'=>'mainForm',
				'varName'=>'product',
				'models'=>'Product',
				'onAfterSetAttributes' => function($event)
				{
					$model = $event->params['model'];
			
					$model->categories = isset($_POST['Product']['categories']) ? $_POST['Product']['categories'] : array();
					$model->tags = isset($_POST['Product']['tags']) ? $_POST['Product']['tags'] : array();
				},
				'forms' => array(
					array(
						'id'=>'imagesForm',
						'models'=>'ProductImage',
						'parentIdAttribute'=>'product_id',
						'varName'=>'productImages',
					),
					array(
						'id'=>'productTab1Form',
						'blocs' => 'product_tab1',
						'varName'=>'productTab1',
					),
					array(
						'id'=>'productTab2Form',
						'blocs' => 'product_tab2',
						'varName'=>'productTab2',
					),
				),
			),
		);

		return array(
			'create'=>array(
				'class' => 'application.components.actions.Create',
				'formSettings' => $formSettings,
			),
			'update'=>array(
				'class' => 'application.components.actions.Update',
				'formSettings' => $formSettings,
			),
			'delete'=>array(
				'class' => 'application.components.actions.Delete',
				'modelClass' => 'Product',
			),
			'admin'=>array(
				'class' => 'application.components.actions.Admin',
				'modelClass' => 'Product',
			),
		);
	}
}

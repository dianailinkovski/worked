<?php
Yii::import('shop.components.Shop');

class DefaultController extends Controller
{
	public $layout='//layouts/column1';
	
	public function init()
	{
		Yii::app()->clientScript->registerCssFile($this->module->assetsUrl.'/css/product.css');

		return parent::init();
	}
	
	public function actionCategory()
	{
		$categories = ProductCategory::model()->findAll(array('condition'=>'level = 2', 'order'=>'lft ASC'));
		
		$this->render('category', array(
			'categories'=>$categories,
		));
	}
	
	public function actionlinkproduct($id)
	{
		$product = Product::model()->findByPk($id);
		foreach ($product->categories as $category)
		{
			$this->redirect($this->createUrl('detail', array('n'=>$product->name_url, 'c'=>$category->name_url)));	
		}
		throw new CHttpException(404);
	}
	
	public function actionListing($c, $order=null)
	{
		$this->layout = '//layouts/column2';
		$this->sidebarViewFile = '/layouts/_sidebar';
		
		$originalC = $c;
		
		if ($c == 'promos') {
			$c = null;
			$sql = 'id IN (SELECT product_id FROM product_category_product WHERE price_sale IS NOT NULL)';
			$params = array();
			$this->sidebarData['currentCategory'] = null;
			$category = null;
		}
		else {
			$sql = 'id IN (SELECT product_id FROM product_category_product WHERE category_id = :category_id)';
			
			if (!($category = ProductCategory::model()->find('i18nProductCategory.l_name_url=:c', array('c'=>$c))))
				throw new CHttpException(404,'The requested page does not exist.');
			
			$categoryMultilang = ProductCategory::model()->multilang()->find('i18nProductCategory.l_name_url=:c', array('c'=>$c));
			
			Yii::app()->languageManager->translatedGetVars['n'] = array();
			foreach (array_keys(Yii::app()->languageManager->languages) as $language)
			{
				Yii::app()->languageManager->translatedGetVars['n'][$language] = $categoryMultilang->{'name_url_'.$language};
			}
			
			$params = array('category_id'=>$category->id);
			$this->sidebarData['currentCategory'] = $category;
		}
		
		if ($order == 1)
			$orderSql = 'LEAST(COALESCE(price_regular, price_sale), COALESCE(price_sale, price_regular)) ASC';
		elseif ($order == 2)
			$orderSql = 'LEAST(COALESCE(price_regular, price_sale), COALESCE(price_sale, price_regular)) DESC';
		else 
			$orderSql = '';
		
		$productProvider = new CActiveDataProvider('Product', array(
			'criteria' => array(
				'condition' => $sql,
				'params' => $params,
				'order' => $orderSql,
			),
			'pagination' => array(
				'pageSize' => 10
			)
		));
		
		$this->sidebarData['categories'] = ProductCategory::model()->findAll(array('condition'=>'level = 2', 'order'=>'lft ASC'));
	
		if (isset($page))
		{
			$page = (int)$page - 1;
			$productProvider->pagination->currentPage = $page;
		}
		$this->render('listing', array(
			'productProvider'=>$productProvider,
			'category'=>$category,
			'c'=>$originalC,
			'order'=>$order,
		));
	}
	
	
	public function actionDetail($c, $n)
	{
		// Get product
		if (!($product = Product::model()->find('i18nProduct.l_name_url=:n', array('n'=>$n))))
			throw new CHttpException(404,'The requested page does not exist.');
			
		$productMultilang = Product::model()->multilang()->find('i18nProduct.l_name_url=:n', array('n'=>$n));
		
		// Get category
		if (!($category = ProductCategory::model()->find('i18nProductCategory.l_name_url=:c', array('c'=>$c))))
			throw new CHttpException(404,'The requested page does not exist.');
			
		$categoryMultilang = ProductCategory::model()->multilang()->find('i18nProductCategory.l_name_url=:c', array('c'=>$c));
		
		
		Yii::app()->languageManager->translatedGetVars['n'] = array();
		foreach (array_keys(Yii::app()->languageManager->languages) as $language)
		{
			Yii::app()->languageManager->translatedGetVars['n'][$language] = $productMultilang->{'name_url_'.$language};
			Yii::app()->languageManager->translatedGetVars['c'][$language] = $categoryMultilang->{'name_url_'.$language};
		}
		
		$this->render('detail', array(
			'product'=>$product,
			'category'=>$category,
		));
	}
}
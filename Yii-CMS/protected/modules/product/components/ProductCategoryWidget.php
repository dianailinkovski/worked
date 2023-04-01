<?php
Yii::import('product.models.*');

/**
 * List product categories.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class ProductCategoryWidget extends CWidget
{
    public function run()
    {
        $this->render('productCategoryWidget', array(
        	'categories'=>ProductCategory::model()->findAll(array(
        			'condition'=>'level = 2',
        			'order'=>'lft ASC',
			))
        ));
    }
}
?>
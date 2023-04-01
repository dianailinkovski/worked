<?php
Yii::import('news.models.*');

/**
 * List recent news.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class RecentNewsWidget extends CWidget
{
	/**
	 * @var int number of entries to show. Defaults to 5.
	 */
	public $maxNbrEntries=5;
	/**
	 * @var string the view file. Defaults to recentNewsWidget.
	 */
	public $viewFile='recentNewsWidget';

    public function run()
    {
		Yii::app()->clientScript->registerCssFile(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('news.assets'), false, -1, YII_DEBUG).'/css/news.css');
		
        $this->render($this->viewFile, array(
        	'news'=>News::model()->findAll(array(
				'order' => 'date DESC',
				'limit' => $this->maxNbrEntries,
			))
        ));
    }
}
?>
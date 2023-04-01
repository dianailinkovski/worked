<?php
/**
 * List recent jobs.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */

class RecentJobsWidget extends CWidget
{
    /**
     * @var int number of entries to show. Defaults to 5.
     */
	public $maxNbrEntries=5;
    
	
    public function init()
    {
    	Yii::app()->getModule('job');
        parent::init();
    }
 
    protected function renderContent()
    {
		$criteria = new CDbCriteria;
		$criteria->condition = 'active = 1';
		$criteria->order = 'publication_date DESC';
		$criteria->limit = $this->maxNbrEntries;

		$jobs = Job::model()->findAll($criteria);
    	
        $this->render('recentJobsWidget', array(
        	'jobs'=>$jobs
        ));
    }
}
?>
<?php
/**
 * Creates a collapsible panel with an optional title. This is for the admin template and purely cosmetic.
 *
 * This widget must be called with beginWidget() and endWidget() not wiget()
 *
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class XPanel extends CWidget
{
    /**
     * @var string the title
     */
	public $title='';


    public function init()
    {
        $this->render('begin', array(
        	'title'=>$this->title,
        ));
    }
 
    public function run()
    {
        $this->render('end', array(
        ));
    }
}
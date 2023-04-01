<?php
/*
 * ECarouFredSel widget class file.
 * @author Fred <info@frebsite.nl>
 * @link http://caroufredsel.frebsite.nl
 * @licensed under both the MIT and GPL licenses.
 * @version: V1.0
 * @base on caroufredsel V5.6.4
 
 Example:
 <ul id="foo">
    <li> c </li>
    <li> a </li>
    <li> r </li>
    <li> o </li>
</ul>

 OR 
<div id="foo">
	<div>
		<h3>Infinity</h3>
		<p>A concept that in many fields refers to a quantity without bound or end.</p>
	</div>
	<div>
		<h3>Circular journey</h3>
		<p>An excursion in which the final destination is the same as the starting point.</p>
	</div>
	<div>
		<h3>jQuery</h3>
		<p>jQuery  is a cross-browser JavaScript library designed to simplify the client-side scripting.</p>
	</div>
	<div>
		<h3>Carousel</h3>
		<p>A carousel is an amusement ride consisting of a rotating circular platform with seats.</p>
	</div>
</div>


// Using custom configuration
$this->widget('ext.carouFredSel.ECarouFredSel', array(
	'id' => 'carousel',
	'target' => '#foo',
    'config' => array(
    	'items' => 6,
    	'scroll' => array(
    		'items' => 1,
    		'easing' => 'swing',
			'duration' => 800,
			'pauseDuration' => 1500,							
			'pauseOnHover' => false,
			'fx' => 'crossfade',
    	),
    ),
));


 */
class ECarouFredSel extends CWidget
{
	// @ string the id of the widget, since version 1.6
	public $id;
	
	// @ string the taget element on DOM
	public $target;
	
	// @ array of config settings for fancybox
	public $config=array();
	
	// @ boolean wether to register it to onload instead of onready
	public $onload=false;
	
	// function to init the widget
	public function init()
	{
		// if not informed will generate Yii defaut generated id, since version 1.6
		if(!isset($this->id))
			$this->id=$this->getId();
		// publish the required assets
		$this->publishAssets();
	}
	
	// function to run the widget
    public function run()
    {
		$config = CJavaScript::encode($this->config);
		Yii::app()->clientScript->registerScript($this->getId(), "
			$('$this->target').carouFredSel($config);
		", ($this->onload ? CClientScript::POS_LOAD : CClientScript::POS_READY));
	}
	
	// function to publish and register assets on page 
	public function publishAssets()
	{
		$assets = dirname(__FILE__).'/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);
		if(is_dir($assets)){
			Yii::app()->clientScript->registerCoreScript('jquery');
			Yii::app()->clientScript->registerCssFile($baseUrl.'/css/carouFredSel.css');
			Yii::app()->clientScript->registerScriptFile($baseUrl . '/jquery.carouFredSel-5.6.4-packed.js', CClientScript::POS_HEAD);
		} else {
			throw new Exception('ECarouFredSel - Error: Couldn\'t find assets to publish.');
		}
	}
}
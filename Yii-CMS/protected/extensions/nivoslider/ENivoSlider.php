<?php
/*
 * ENivoSlider widget class file.
 * @author Thiago Otaviani Vidal <thiagovidal@othys.com>
 * @link http://www.othys.com
 * Copyright (c) 2010 Thiago Otaviani Vidal
 * MADE IN BRAZIL
 
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:

 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.

 * ENivoSlider extends CWidget and implements a base class for a nivoslider widget.
 * more about nivoslider can be found at http://nivo.dev7studios.com/.
 * @version: 2.1 beta
 */
class ENivoSlider extends CWidget
{
	public $id;
	public $htmlOptions=array();
	public $fancy=true;
	public $cssFile;
	public $config=array();
	public $images=array();

	public function init()
	{
		if (isset($this->id)) {
			$this->htmlOptions['id']=$this->id;
		} else {
			$this->htmlOptions['id']=$this->getId();
		}
		$this->publishAssets();
	}
	
    public function run()
    {
		//echo CHtml::openTag('div', $this->htmlOptions)."\n";
		if(count($this->images)) {
			$this->renderImages($this->images);
		}
		//echo CHtml::closeTag('div')."\n";
		if (!count($this->config)) {
			$config=array(
				'effect'=>'fade',
				'slices'=>25,
				'animSpeed'=>500,
				'pauseTime'=>6000,
				'startSlide'=>0,
				'directionNav'=>true,
				'directionNavHide'=>true,
				'controlNav'=>true,
				'keyboardNav'=>true,
				'pauseOnHover'=>true,
				'manualAdvance'=>false,
				'captionOpacity'=>1.0,
			);
		} else {
			$config=$this->config;
		}
		$config=CJavaScript::encode($config);
		Yii::app()->getClientScript()->registerScript(__CLASS__, "
			$('#".$this->htmlOptions['id']."').nivoSlider($config);
		");
	}
	
	public function renderImages($items)
	{
		foreach($items as $item) {
			if (isset($item['caption'])) {
				$item['imageOptions']['title']=$item['caption'];
			}
			if (isset($item['url'])) {
				echo CHtml::link(CHtml::image($item['src'], $item['caption'], $item['imageOptions']), $item['url'], $item['linkOptions'])."\n";
			} else {
				echo CHtml::image($item['src'], $item['caption'], $item['imageOptions'])."\n";
			}
		}
	}
	
	public function publishAssets()
	{
		$assets = dirname(__FILE__).'/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);
		if(is_dir($assets)){
			Yii::app()->clientScript->registerCoreScript('jquery');
			Yii::app()->clientScript->registerScriptFile($baseUrl . '/jquery.nivo.slider.pack.js', CClientScript::POS_HEAD);
			Yii::app()->clientScript->registerCssFile($baseUrl . '/nivo-slider.css');
			if (isset($this->cssFile)) {
				Yii::app()->clientScript->registerCssFile($this->cssFile);
			} else if($this->fancy) {
				Yii::app()->clientScript->registerCssFile($baseUrl.'/fancy-nivo-slider.css');
			}
		} else {
			throw new Exception('CNivoSlider - Error: Couldn\'t publish assets.');
		}
	}
}

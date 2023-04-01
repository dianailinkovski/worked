<?php

/**
 * JColorPicker class file.
 *
 * @author jerry2801 <jerry2801@gmail.com>
 *
 * A typical usage of JColorPicker is as follows:
 * <pre>
 * $this->widget('application.extensions.colorpicker.JColorPicker', array(
 *     'model' => $model,
 *     'attribute' => 'base_style',
 *     'htmlOptions' => array(),
 * ));
 * </pre>
 */


class JColorPicker extends CWidget
{
    public $model;
    public $attribute;
	public $baseUrl;
    public $options = array();
    public $htmlOptions = array();
    public $selectorHtmlOptions = array();

    public function init()
    {
        $this->options['onSubmit'] = 'js:function(hsb,hex,rgb,el) { $(el).val(hex); $(el).ColorPickerHide(); }';

        $options = CJavaScript::encode($this->options);

        $dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'source';
        $this->baseUrl = Yii::app()->getAssetManager()->publish($dir);

        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile($this->baseUrl.'/js/colorpicker.js');
        $cs->registerCssFile($this->baseUrl.'/css/colorpicker.css');


        $activeId = CHtml::activeId($this->model, $this->attribute);
        $cs->registerScript($activeId.'-Script', '$("#'.$activeId.'").ColorPicker('.$options.');');

    }

    public function run()
    {
        echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
    }
}
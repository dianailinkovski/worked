<?php
if (!empty($model) && !empty($attribute))
{
	$pos = strrpos($attribute, ']');
	$attributeName = $pos ? substr($attribute, $pos+1) : $attribute;
	$preAttributeName = $pos ? substr($attribute, 0, $pos+1) : '';

    echo CHtml::activeTextArea($model, $attribute, $textareaAttributes);
    echo '<script type="text/javascript">CKEDITOR.replace("'.get_class($model).$preAttributeName.'['.$attributeName.']'.'", '.json_encode($config).');</script>';
}
elseif (!empty($name))
{
    echo CHtml::textArea($name, $value, $textareaAttributes);
    echo '<script type="text/javascript">CKEDITOR.replace("'.$name.'");</script>';
}
?>

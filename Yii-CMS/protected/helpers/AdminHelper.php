<?php
/**
 * Helper class for admin section
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Helper
 */

class AdminHelper
{
	/**
	 * Returns a path type string from an alias id
	 * 
	 * @param int $id the alias id
	 * 
	 * @return string the path
	 */
	public static function pathFromId($id) 
	{
		$paths = array();
		while (true) 
		{
			$alias = CmsAlias::model()->findByPk($id);
			$paths[] = $alias->alias;
			$id = $alias->parent_id;
			
			if ($alias->parent_id == 0){
				$paths = array_reverse($paths);
				$pathStr = '';
				foreach ($paths as $path) {
					$pathStr .= $path.'/';
				}
				//$pathStr = trim($pathStr, '/');
				return $pathStr;
			}
		}
	}

	/**
	 * Generate a string to be used in url, checked for uniqueness in a model's table
	 * 
	 * @param string $str the string to process.
	 * @param CActiveRecord $model the model in which it will be stored
	 * @param string $attribute the attribute in which it will be stored
	 * @param int $id the id of the model being edited (if not new model). Defaults to null
	 * @param string $lang if multilang, the language of the url. Defaults to null
	 * @param array $reserved array of reserved words which the url must not be. Defaults to empty array()
	 * 
	 * @return string the url
	 */
    public static function generateUrlStr($str, $model, $attribute, $id=null, $lang=null, $reserved=array())
    {
		$str = mb_strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/u'), array('', '-', ''), Helper::removeAccents(trim($str))));
		
		if (is_numeric($str))
			$str = 'n'.$str;
		
		$strlen = mb_strlen($str);
		if ($strlen > 90)
		{
			if (($lastSpace = strrpos($str, ' ')) !== false)
				$str = mb_substr($str, 0, $lastSpace, 'utf-8');
			else
				$str = mb_substr($str, 0, 90, 'utf-8');
		}
		if ($str == '')
			$str = Helper::generateHash();
		
		$strOrig = $str;
		$i = 0;
		$modelClass = get_class($model);
		while (true) 
		{
			$i++;
			
			if (!is_null($lang))
			{
				if ($id === null)
					$aliasModel = $model::model()->multilang()->find('multilang'.$modelClass.'.l_'.$attribute.' = :str AND multilang'.$modelClass.'.lang_id = :lang', array('str'=>$str, 'lang'=>$lang));
				else
					$aliasModel = $model::model()->multilang()->find('multilang'.$modelClass.'.l_'.$attribute.' = :str AND multilang'.$modelClass.'.lang_id = :lang AND t.id <> :id', array('id'=>$id, 'lang'=>$lang, 'str'=>$str));

			} 
			else {
				if ($id === null)
					$aliasModel = $model::model()->findByAttributes(array($attribute => $str));
				else 
					$aliasModel = $model::model()->findByAttributes(array($attribute => $str), 'id <> '.$id);
			}
			if ($aliasModel === null && !in_array($str, $reserved))
				break;
			else
				$str = $strOrig.$i;
		}
		
		return $str;
    }
	
	/**
	 * Returns a javascript block to make ckeditor function with tabular widget, put it in sortable start.
	 *
	 * Must destroy and restore ckeditor upon moving.
	 *
	 * Operates on textareas with class ckEditor
	 * 
	 * @return string the javascript
	 */
	public static function tabularInputCkEditorSortableStart()
	{
		return "var widgetId = $(event.target).parent('.tabularInputWidget').attr('id');

				$('textarea.ckEditor').filter(function(){ // getting all editors of the widget no matter how deep
					var found = false;
					$(this).parents('.tabularInputWidget').each(function(){
						if ($(this).attr('id') == widgetId)
							found = true;
					});
					if (found)
						return true;
					else
						return false;
				}).each(function(){
					$(this).data('ckeditorConfig', CKEDITOR.instances[$(this).attr('id')].config); // temporarily storing the config that will be restored when stopped
					CKEDITOR.instances[$(this).attr('id')].destroy();
				});";
	}
	
	/**
	 * Returns a javascript block to make ckeditor function with tabular widget, put it in sortable stop.
	 *
	 * Must destroy and restore ckeditor upon moving.
	 *
	 * Operates on textareas with class ckEditor
	 * 
	 * @return string the javascript
	 */
	public static function tabularInputCkEditorSortableStop()
	{
		return "var widgetId = $(event.target).parent('.tabularInputWidget').attr('id');

				$('textarea.ckEditor').filter(function(){ // getting all editors of the widget no matter how deep
					var found = false;
					$(this).parents('.tabularInputWidget').each(function(){
						if ($(this).attr('id') == widgetId)
							found = true;
					});
					if (found)
						return true;
					else
						return false;
				}).each(function(){
			        if (CKEDITOR.instances[$(this).attr('id')] == undefined){
			        	CKEDITOR.replace(this, $(this).data('ckeditorConfig')); // restoring stored config
			        	$(this).data('ckeditorConfig', null);
			        }
				});";
	}
	
	/**
	 * Returns a javascript block to make ckeditor function with tabular widget, put it in before delete item.
	 *
	 * Must destroy ckeditor upon delete otherwise it will create problems, removing textarea is not enough.
	 *
	 * Operates on textareas with class ckEditor
	 * 
	 * @return string the javascript
	 */
	public static function tabularInputCkEditorBeforeDeleteItem()
	{
		return "$('textarea.ckEditor').filter(function(){
					if ($(this).parents('.tabularInputWidget:first').attr('id') != id
						|| $(this).parents('.tabularPortlet:first').attr('id') != id+'_'+itemId)
						return false;
					else
						return true;
				}).each(function(){
					CKEDITOR.instances[$(this).attr('id')].destroy();
				});";
	}
	
	/**
	 * Returns a javascript block to make date time picker function with tabular widget, put it in after add.
	 *
	 * It creates the datetime picker when adding a new item to fields tagged with class datetimePicker.
	 * 
	 * @return string the javascript
	 */
	public static function tabularInputAfterAddItemDatetimePicker()
	{
		return "$('input.datetimePicker').filter(function(){
					if ($(this).parents('.tabularInputWidget:first').attr('id') != id
						|| $(this).parents('.tabularPortlet:first').attr('id') != id+'_'+itemId)
						return false;
					else
						return true;
				}).each(function(){
					$(this).datetimepicker({'timeFormat':'HH:mm:ss','dateFormat':'yy-mm-dd','showSecond':false,'hourGrid':4,'minuteGrid':10});
				});";
	}

	/**
	 * Translates model labels and adds language after labels of fields generated by Multilingual behaviors.
	 *
	 * @param CActiveRecord $model the model to which the labels belong to.
	 * @param array $labels the untranslated labels
	 * @param string $messageFile the message file to use to do the translation
	 * 
	 * @return array the translated labels
	 */
    public static function multilangLabels($model, $labels, $messageFile='admin')
    {
    	$behaviors = $model->behaviors();
    	$localizedAttributes = array();
    	
    	foreach ($behaviors as $behavior)
    	{
    	    if ($behavior['class'] == 'application.components.behaviors.MultilingualBehavior')
	    	{
	    	    foreach ($behavior['localizedAttributes'] as $attribute)
	    		{
	    			if (!in_array($attribute, $localizedAttributes))
	    				$localizedAttributes[] = $attribute;
	    		}
	    	}    	
	    	if ($behavior['class'] == 'application.components.behaviors.MultilangVirtualAttributesBehavior')
	    	{
	    		foreach ($behavior['attributes'] as $attribute)
	    		{
	    			if (!in_array($attribute, $localizedAttributes))
	    				$localizedAttributes[] = $attribute;
	    		}
	    	}
    	}
		foreach ($localizedAttributes as $attribute)
		{
			foreach (Yii::app()->languageManager->languages as $language => $fullLanguage)
			{
				$labels[$attribute.'_'.$language] = $labels[$attribute].' ('.Yii::t($messageFile, $fullLanguage).')';
			}
		}

		return $labels;
    }
    
	/**
	 * Returns an array of blocs to be used in forms errorSummary function.
	 *
	 * You can use array_merge to merge errors from different sources.
	 *
	 * @param array $blocs the blocs models
	 * 
	 * @return array the blocs models
	 */
    public static function blocsErrors($blocs)
    {
    	$outArray = array();
    	foreach ($blocs as $key => $value)
    	{
    		if ($key == '0')
    			$outArray = array_merge($outArray, $value);
    		else
    		{
    			foreach ($value as $valueKey => $valueValue)
    			{
    				if ($value[$valueKey] === null)
    					unset($value[$valueKey]);
    				else
    					$value[$valueKey] = array_values($value[$valueKey]);
    			}
    			
    			$outArray = array_merge($outArray, array_reduce($value, 'array_merge', array()));
    		}
    	}
    	return $outArray;
    }

	/**
	 * Returns javascript bloc that modifies grid view to make rows jquery sortable
	 *
	 * Ajax calls admin/gridviewsort to do its sorting on the server side
	 *
	 * @param string $id the id of the grid view
	 * @param string $modelName the name of the model being sorted
	 * @param string $attributeName the name of the model's attribute being sorted
	 * 
	 * @return array the translated labels
	 */
	public static function sortableGridViewJS($id, $modelName, $attributeName)
	{
	    return "
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };
        $('#".$id." table.items tbody').sortable({
            forcePlaceholderSize: true,
            forceHelperSize: true,
            items: 'tr',
            stop: function(e, ui) {
	            var i = 1;
	            var itemClass = $(ui.item).attr('class');
	            var rank = 0;
	            var id = itemClass.split('_');
	            id = id[1];
	                    
	            $(ui.item).closest('tbody').children('tr').each(function(){
	            	if ($(this).attr('class') == itemClass)
	            		rank = i;
	            		
	            	$(this).removeClass('odd');
	            	$(this).removeClass('even');
	            		
	            	if (i % 2)
	            		$(this).addClass('even');
	            	else
	            		$(this).addClass('odd');

	            	i++;
	            });
                $.ajax({
                    'url': '".Yii::app()->controller->createUrl('admin/gridviewsort', array('modelName'=>$modelName, 'attributeName'=>$attributeName))."',
                    'type': 'post',
                    'data': {rank:rank, id:id},
                    'success': function(data){
                    },
                    'error': function(request, status, error){
                        alert('Unable to set the sort order at this time.');
                    }
                });
            },
            helper: fixHelper
        }).disableSelection();
	    ";
	}
}
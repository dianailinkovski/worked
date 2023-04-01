<?php
/**
 * Renders the blocs.
 * 
 * @see AdminBlocsWidget
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */

class BlocsWidget extends CWidget
{
	/**
	 * @var string the unique id of the blocs to display. Required.
	 */
	public $uniqueId;
	/**
	 * @var int the parent id of the blocs to display. Required.
	 */
	public $parentId;
	/**
	 * @var boolean whether or not to register meta description. Defaults to true.
	 */
	public $registerMetaDesc=true;
	

	public function run()
	{
		$dependency = new CDbCacheDependency('SELECT MAX(last_modified) FROM bloc WHERE parent_id=:parent_id AND unique_id=:unique_id');
		$dependency->params = array(':parent_id'=>$this->parentId, ':unique_id'=>$this->uniqueId);
		$models = Bloc::model()->cache(60*60*24*30, $dependency)->findAllByAttributes(array('parent_id'=>$this->parentId, 'unique_id'=>$this->uniqueId), array('order'=>'rank ASC'));

		$blocs = array();
		foreach ($models as $bloc)
		{
			$blocs[] = array();
			$blocsIndex = count($blocs)-1;
			$blocs[$blocsIndex][0] = $bloc;
			$blocClass = ucfirst($bloc->bloc_type).'Bloc';

			if (!array_key_exists($bloc->bloc_type, Yii::app()->cms->blocs))
			{
				Yii::import('application.components.blocs.'.$bloc->bloc_type.'.*');
				Yii::import('application.components.blocs.'.$bloc->bloc_type.'.models.*');

				Yii::app()->cms->blocs[$bloc->bloc_type] = new $blocClass;
			}

			$className = 'Bloc'.ucfirst($bloc->bloc_type);
			$blocs[$blocsIndex][1] = $className::model()->findByPk($bloc->bloc_id);	
			$blocs[$blocsIndex][2] = $bloc->bloc_type;	
		}
		
		if ($this->registerMetaDesc)
		{
			$metaDesc = '';
			foreach ($blocs as $bloc)
			{
				if ($bloc[0]->title_page)
					$metaDesc .= $bloc[0]->title.' - ';
				
				if ($bloc[0]->bloc_type == "editor")
				{
					$shortText = strip_tags($bloc[1]->html);
					if (strlen($shortText) > 200)
						$shortText = trim(substr($shortText, 0, 200)).'&hellip;';
					
					$metaDesc .= $shortText.' - ';
				}
			}
			if ($metaDesc != '')
				Yii::app()->clientScript->registerMetaTag(substr($metaDesc, 0, -3), 'description');
		}

        $this->render('blocsWidget', array(
        	'blocs'=>$blocs,
        ));
	}
}
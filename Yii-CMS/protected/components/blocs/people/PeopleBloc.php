<?php
/**
 * Bloc people
 *
 * List a number of people with their description.
 *
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Bloc
 */

class PeopleBloc extends CmsBloc
{
	private $_assetsUrl;


	public function subModels()
	{
		return array(
			array('BlocPeoplePeople', 'bloc_people_id'),
		);
	}

    public function getAssetsUrl()
    {
    	if ($this->_assetsUrl === null)
    		$this->_assetsUrl = Yii::app()->getAssetManager()->publish(
    			Yii::getPathOfAlias('application.components.blocs.people.assets'), false, -1, YII_DEBUG);
    	return $this->_assetsUrl;
    }
}
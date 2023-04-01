<?php
/**
 * Bloc citation
 *
 * List of citations from people.
 *
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Bloc
 */

class CitationBloc extends CmsBloc
{
	private $_assetsUrl;


	public function subModels()
	{
		return array(
			array('BlocCitationCitation', 'bloc_citation_id'),
		);
	}

    public function getAssetsUrl()
    {
    	if ($this->_assetsUrl === null)
    		$this->_assetsUrl = Yii::app()->getAssetManager()->publish(
    			Yii::getPathOfAlias('application.components.blocs.citation.assets'), false, -1, YII_DEBUG);
    	return $this->_assetsUrl;
    }
}
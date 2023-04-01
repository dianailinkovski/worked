<?php
/**
 * Base class for all blocs
 * 
 * The CmsBloc classes are instanced once per bloc and stored in the CMS component
 *
 * If you create new blocs there are a few requirements. Add a folder in protected/components/blocs named with your bloc unique id (ex: editor).
 * Inside it, create a class with your capitalized unique id followed by "Bloc" that extends this class (ex: EditorBloc).
 * Inside this folder, create a "models" and "views" folders. "models" must contain at least the model for your bloc, which must at least have an "id" field.
 * This model must be named "Bloc" followed by your capitalized bloc's unique id (ex: BlocEditor).
 * In the views folder, there must be an "admin.php" and "bloc.php" files where admin.php is the admin form (see other blocs for examples)
 * and bloc.php is the view file for the front-end. A "$bloc" variable will be passed to this view file containting the bloc's model.
 * You may also create an "assets" folder for your assets. If you do, you must do the publish in the bloc's component (see other blocs for examples).
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Core
 */

abstract class CmsBloc extends CComponent
{
	/**
	* Returns array containing information on this bloc's sub models.
	* 
	* In your bloc view file, a $model variable is given to you containing the bloc model.
	* However, if you need sub models to your bloc you need to define them here so that the action knows what variables to give to your view file.
	* The format is an array containing array(s) of 2 values, 
	* first is the model name of your sub model and the second is your parent id attribute name linking it to the bloc model.
	* The action will use the model name to create the sub forms for the form manager and the variable containing the sub models will be named after the model name.
	* Currently, only one level of sub models is supported.
	* 
	* Example: 
	* return array(
	*      array('BlocDocumentDocument', 'bloc_document_id')
	* );
	* 
	* @see CrudAction
	* @return array the sub models array
	*/
	public function subModels()
	{
	}

	/**
	* Javascript code that will be executed in afterInit of tabular input
	* Override this method and return a string containing javascript
	* @see TabularInputWidget
	*/
	public function afterInit()
	{
	}

	/**
	* Javascript code that will be executed in beforeAddItem of tabular input
	* Override this method and return a string containing javascript
	* @see TabularInputWidget
	*/
	public function beforeAddItem()
	{
	}

	/**
	* Javascript code that will be executed in afterAddItem of tabular input
	* Override this method and return a string containing javascript
	* @see TabularInputWidget
	*/
	public function afterAddItem()
	{
	}

	/**
	* Javascript code that will be executed in beforeDeleteItem of tabular input
	* Override this method and return a string containing javascript
	* @see TabularInputWidget
	*/
	public function beforeDeleteItem()
	{
	}

	/**
	* Javascript code that will be executed in afterDeleteItem of tabular input
	* Override this method and return a string containing javascript
	* @see TabularInputWidget
	*/
	public function afterDeleteItem()
	{
	}

	/**
	* Javascript code that will be executed when sortable starts in tabular input
	* Override this method and return a string containing javascript
	* @see TabularInputWidget
	*/
	public function sortableStart()
	{
	}

	/**
	* Javascript code that will be executed when sortable stops in tabular input
	* Override this method and return a string containing javascript
	* @see TabularInputWidget
	*/
	public function sortableStop()
	{
	}
}
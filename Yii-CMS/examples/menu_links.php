//================================================================================
// Liens dans les menus
//================================================================================

<!-- Link to a keyword -->
<a href="<?php echo $this->createUrl('/', array('keyword'=>'keyword')); ?>">Page</a>

<!-- Link to first child of a parent keyword -->
<a href="<?php echo $this->createUrl('/', array('keyword'=>CmsAlias::model()->findByAttributes(array('keyword'=>'parent-keyword'))->descendants()->with('routes')->find('routes.id IS NOT NULL')->keyword)); ?>">First child</a>

<!-- Link to the index page of a module -->
<a href="<?PHP echo $this->createUrl('/module/default/index'); ?>">Module</a>

<!-- Link to home page -->
<a href="<?PHP echo $this->createUrl('/site/index'); ?>">Accueil</a>


//================================================================================
// Add a "active" class to menu items
//================================================================================

<?php
// For keyword type links
$currentAliasKeyword = (isset(Yii::app()->cms->currentAlias)) ? Yii::app()->cms->currentAlias->keyword : '';
// For whole modules
$currentModuleId = (isset($this->module->id)) ? $this->module->id : "";
?>

<ul>

	<!-- To set active if it's a child of a certain parent -->
	<li<?PHP echo (isset(Yii::app()->cms->currentAlias) ? Yii::app()->cms->currentAlias->isDescendantOf(CmsAlias::model()->findByAttributes(array('keyword'=>'parent-keyword'))) : false) ? " class='active'" : ""; ?>><a href="#">Child</a></li>
	
	<!-- For keyword type links -->
	<li<?PHP echo ($currentAliasKeyword == 'keyword') ? " class='active'" : ""; ?>><a href="#">Page</a></li>
	
	<!-- To set active if in module -->
	<li<?PHP echo ($currentModuleId == "name-module") ? " class='active'" : ""; ?>><a href="#">Module</a></li>
	
</ul>
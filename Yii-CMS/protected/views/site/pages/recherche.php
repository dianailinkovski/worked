<?php
// POUR OBTENIR LE CODE GOOGLE CUSTOM SEARCH POUR CE PROJET, CONFIGUREZ LE DOMAINE DANS LES OUTILS GOOGLE POUR WEBMESTRE.
// NE PAS OUBLIER DE CRÉER UN ALIAS POUR CETTE PAGE À PARTIR DU CMS.

$this->pageTitle =  Yii::app()->cms->currentAlias->title.' | '.Yii::app()->name;
$this->breadcrumbs=array(
	Yii::app()->cms->currentAlias->title,
);
?>

<article class="mod-content mod-type-detail">
		
	<header>
		<h1 class="page-title"><?PHP echo Yii::app()->cms->currentAlias->title; ?></h1>
	</header>

	<div id="search-field">
	
		<script>
		  (function() {
			var cx = '007120745352997743613:hcepsdffyl8';
			var gcse = document.createElement('script');
			gcse.type = 'text/javascript';
			gcse.async = true;
			gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
				'//www.google.com/cse/cse.js?cx=' + cx;
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(gcse, s);
		  })();
		</script>
		<gcse:search></gcse:search>
	
	</div>
	
</article>
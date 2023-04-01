<?php
$this->pageTitle=Yii::t('memberModule.common', 'Récupération de mot de passe').$this->titleSeparator.Yii::app()->name;
$this->breadcrumbs=array(
	Yii::t('memberModule.common', 'Récupération de mot de passe') => array('/member/default/recover'),
	Yii::t('memberModule.common', 'Confirmation'),
);
?>

<article>

	<header>
		<h1 class="mainTitle"><?PHP echo Yii::t('memberModule.common', 'Récupération de mot de passe'); ?></h1>
	</header>

	<section>

		<p><?PHP echo Yii::t('memberModule.common', 'Des instructions pour récupérer votre mot de passe vous ont été envoyés par courriel.'); ?></p>

	</section>
	
</article>